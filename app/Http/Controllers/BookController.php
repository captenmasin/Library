<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Book;
use Inertia\Inertia;
use App\Actions\TrackEvent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BookResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ReviewResource;
use App\Actions\Books\FetchOrCreateBook;
use Inertia\Response as InertiaResponse;
use App\Actions\Books\ImportBookFromData;
use App\Actions\Books\SearchBooksFromApi;
use App\Actions\Books\GetPublicBookPageData;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Resources\PreviousSearchResource;

class BookController extends Controller
{
    public function index(Request $request): InertiaResponse|JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = 10;

        $originalQuery = $request->get('q');
        $originalQuery = is_array($originalQuery) ? implode(' ', $originalQuery) : $originalQuery;

        $query = $originalQuery;
        $author = null;
        $tag = null;

        if (Str::contains(strtolower($originalQuery), 'author:')) {
            $originalQuery = str_ireplace('author:', 'author:', $originalQuery);

            $author = Str::of($originalQuery)->after('author:')->trim()->value();
            $query = Str::of($originalQuery)->before('author:')->trim()->value();
        }

        if (Str::contains(strtolower($originalQuery), 'tag:')) {
            $originalQuery = str_ireplace('tag:', 'tag:', $originalQuery);

            $tag = Str::of($originalQuery)->after('tag:')->trim()->value();
            $query = Str::of($originalQuery)->before('tag:')->trim()->value();
        }

        if ($request->filled('q')) {
            $request->user()->previousSearches()->updateOrCreate(
                [
                    'search_term' => $originalQuery, 'user_id' => Auth::id(),
                    'type' => $tag ? 'tag' : ($author ? 'author' : 'query'),
                ],
                [
                    'search_term' => $originalQuery,
                    'type' => $tag ? 'tag' : ($author ? 'author' : 'query'),
                    'updated_at' => now(),
                ]
            );

            Cache::forget('previous_searches_'.$request->user()->id);
        }

        $previousSearches = Cache::remember('previous_searches_'.$request->user()->id, 60, function () use ($request) {
            return $request->user()->previousSearches()
                ->limit(8)->orderBy('updated_at', 'desc')->get();
        });

        if ($request->expectsJson()) {
            $results = SearchBooksFromApi::run(
                query: $query,
                author: $author,
                subject: $tag,
                maxResults: $perPage,
                page: $page,
            );

            return response()->json([
                'query' => $originalQuery,
                'page' => $page,
                'per_page' => $perPage,
                'total' => $results['total'],
                'books' => $results['books'],
                'previous_searches' => PreviousSearchResource::collection($previousSearches)->resolve($request),
            ]);
        }

        return Inertia::render('books/Search', [
            'initialQuery' => $originalQuery,
            'page' => $page,
            'perPage' => $perPage,
            'previousSearches' => PreviousSearchResource::collection($previousSearches),
            'results' => Inertia::defer(
                fn () => SearchBooksFromApi::run(
                    query: $query,
                    author: $author,
                    subject: $tag,
                    maxResults: $perPage,
                    page: $page,
                )
            )->deepMerge()->matchOn(''),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Books', 'href' => route('user.books.index')],
                ['title' => 'Find Book', 'href' => route('books.search')],
            ],
        ])->withMeta([
            'title' => 'Find Book',
            'description' => 'Add a new book to your collection by searching for it online or scanning its barcode.',
        ]);
    }

    public function scan(Request $request)
    {
        return Inertia::render('books/Scan', [
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Books', 'href' => route('user.books.index')],
                ['title' => 'Scan Book', 'href' => route('books.scan')],
            ],
        ])->withMeta([
            'title' => 'Find Book',
            'description' => 'Add a new book to your collection by searching for it online or scanning its barcode.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $identifier = $request->string('identifier');
        FetchOrCreateBook::run($identifier);

        return redirect()->back();
    }

    public function show(Request $request, string $book): InertiaResponse|JsonResponse
    {
        if ($request->user()) {
            return $this->showAuthenticatedBook($request, $book);
        }

        $payload = GetPublicBookPageData::run($book, $request);

        if ($request->expectsJson()) {
            return response()->json([
                'book' => $payload['book'],
                'average_rating' => $payload['average_rating'],
                'related' => $payload['related'],
                'reviews' => $payload['reviews'],
            ]);
        }

        return Inertia::render('books/Show', [
            'book' => $payload['book'],
            'averageRating' => $payload['average_rating'],
            'related' => Inertia::defer(fn () => $payload['related']),
            'reviews' => Inertia::defer(fn () => $payload['reviews']),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Books', 'href' => route('user.books.index')],
                ['title' => Str::limit($payload['book']['title'], 52), 'href' => route('books.show', $book)],
            ],
        ])->withMeta($payload['meta']);
    }

    public function apiShow(Request $request, Book $book): JsonResponse
    {
        $book->load([
            'authors',
            'reviews',
            'ratings',
            'publisher',
            'tags',
            'users' => fn ($query) => $query->where('user_id', $request->user()->id),
            'notes' => fn ($query) => $query->where('user_id', $request->user()->id),
        ]);

        $reviews = $book->reviews->load('user', 'book')
            ->reject(fn ($review) => $review->user_id === $request->user()->id)
            ->values();

        return response()->json([
            'book' => BookResource::make($book)->resolve($request),
            'average_rating' => number_format($book->ratings->avg('value') ?? 0, 1),
            'related' => [],
            'reviews' => ReviewResource::collection($reviews)->resolve($request),
        ]);
    }

    private function showAuthenticatedBook(Request $request, string $path): InertiaResponse|JsonResponse
    {
        $book = Book::query()->where('path', $path)->firstOrFail();

        if ($request->expectsJson()) {
            return $this->apiShow($request, $book);
        }

        $book->load(['authors', 'reviews', 'ratings', 'publisher', 'tags',
            'users' => fn ($query) => $query->where('user_id', Auth::id()),
            'notes' => fn ($query) => $query->where('user_id', Auth::id()),
        ]);

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
            'averageRating' => number_format($book->ratings->avg('value') ?? 0, 1),
            'related' => Inertia::defer(function () use ($book) {
                $relatedBooks = $book->relatedBooksBySearch(4);
                $relatedBooks->map(fn ($related) => $related->load(['authors']));

                return BookResource::collection($relatedBooks);
            }),
            'reviews' => Inertia::defer(fn () => ReviewResource::collection(
                $book->reviews->load('user', 'book')
                    ->reject(fn ($review) => $review->user_id === Auth::id())
            )),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Books', 'href' => route('user.books.index')],
                ['title' => Str::limit($book->title, 52), 'href' => route('books.show', $book)],
            ],
        ])->withMeta([
            'title' => $book->title,
            'image' => $book->primary_cover,
            'description' => $book->description ?? $book->title.' by '.$book->authors->pluck('name')->implode(', '),
        ]);
    }

    public function preview(Request $request, string $identifier): InertiaResponse|RedirectResponse
    {
        if (Book::where('identifier', $identifier)->exists()) {
            return redirect()->route('books.show', Book::where('identifier', $identifier)->first());
        }

        ImportBookFromData::dispatchAfterResponse($identifier);

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookPreviewed, [
            'user_id' => $request->user()?->id,
            'book' => [
                'identifier' => $identifier,
            ],
        ]);

        return Inertia::render('books/Preview', [
            'identifier' => $identifier,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Books', 'href' => route('user.books.index')],
                ['title' => 'Importing Book'],
            ],
        ])->withMeta([
            'title' => 'Importing Book...',
            'description' => 'We are fetching the book details from the database. This may take a few seconds.',
        ]);
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('user.books.index');
    }
}

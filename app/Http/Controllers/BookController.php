<?php

namespace App\Http\Controllers;

use App\Actions\Books\FetchOrCreateBook;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReviewResource;
use App\Models\Book;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bookQuery = Auth::user()->books()->with(['authors', 'reviews', 'publisher', 'covers']);
        if ($request->filled('status')) {
            $statuses = Arr::wrap($request->input('status'));
            $bookQuery->wherePivotIn('status', $statuses);
        }

        $books = $bookQuery->get();

        $books = $books->sortByDesc('id');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $books = $books->filter(function ($book) use ($search) {
                return str_contains(strtolower($book->title), strtolower($search)) ||
                    str_contains(strtolower($book->description), strtolower($search)) ||
                    str_contains(strtolower(implode(', ', $book->authors->pluck('name')->toArray())), strtolower($search)) ||
                    str_contains(strtolower($book->identifier), strtolower($search));
            });
        }

        $sort = $request->get('sort', 'added');
        if (! in_array($sort, ['title', 'rating', 'published_date', 'added'])) {
            $sort = 'added';
        }

        $direction = $request->get('order', 'desc');

        $desc = $direction === 'desc';

        if ($sort === 'title') {
            $books = $books->sortBy(function ($book) {
                return strtolower($book->title);
            }, SORT_NATURAL | SORT_FLAG_CASE, $desc);
        } elseif ($sort === 'rating') {
            $books = $books->sortBy(function ($book) {
                return $book->reviews->avg('rating') ?? 0;
            }, SORT_REGULAR, $desc);
        } elseif ($sort === 'published_date') {
            $books = $books->sortBy('published_date', SORT_REGULAR, $desc);
        } elseif ($sort === 'added') {
            $books = $books->sortBy('id', SORT_REGULAR, $desc);
        }

        if ($request->filled('author')) {
            $authorUuid = $request->get('author');
            $books = $books->filter(function ($book) use ($authorUuid) {
                return $book->authors->contains('uuid', $authorUuid);
            });
        }

        if ($request->filled('publisher')) {
            $publisherUuid = $request->get('publisher');
            $books = $books->filter(function ($book) use ($publisherUuid) {
                return $book->publisher->uuid === $publisherUuid;
            });
        }

        return Inertia::render('books/Index', [
            'books' => BookResource::collection($books->values()),
            'selectedStatuses' => $request->get('status', []),
            'selectedAuthor' => $request->get('author'),
            'selectedPublisher' => $request->get('publisher'),
            'selectedSort' => $sort,
            'selectedOrder' => $request->get('order', 'desc'),
            'searchQuery' => $request->get('search', ''),
            'authors' => AuthorResource::collection(Auth::user()->books()->with('authors')->get()
                ->flatMap(fn ($book) => $book->authors)->unique('uuid'))->values(),
            'publishers' => Auth::user()->books()->with('publisher')->get()
                ->map(fn ($book) => $book->publisher)->unique('uuid')->values(),
        ])->withMeta([
            'title' => 'Books',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('books/Create', [
            'query' => $request->get('q'),
        ])->withMeta([
            'title' => 'Add Book',
            'description' => 'Add a new book to your collection by providing its identifier (ISBN, Open Library ID, etc.).',
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

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['authors', 'reviews', 'notes', 'covers']);

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
            'averageRating' => round($book->reviews->avg('rating'), 1),
            'reviews' => Inertia::defer(fn () => ReviewResource::collection($book->reviews)),
        ])->withMeta([
            'title' => $book->title,
        ]);
    }

    public function temporary(string $identifier)
    {
        if (Book::where('identifier', $identifier)->exists()) {
            return redirect()->route('books.show', Book::where('identifier', $identifier)->first());
        }

        return Inertia::render('books/Importing', [
            'identifier' => $identifier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        if ($request->has('notes')) {
            $notes = $request->get('notes');
            $request->user()->notes()->updateOrCreate(
                ['book_id' => $book->id],
                ['title' => 'test', 'content' => $notes]
            );
        }

        return redirect()->back()
            ->with('success', 'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('books.index');
        //            ->banner('Book '.$book->code.' removed successfully');
    }
}

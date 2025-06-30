<?php

namespace App\Http\Controllers;

use App\Actions\Books\SearchBooksFromApi;
use App\Enums\UserBookStatus;
use App\Http\Requests\Books\DestroyBookUserRequest;
use App\Http\Requests\Books\StoreBookUserRequest;
use App\Http\Requests\Books\UpdateBookUserRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserBookController extends Controller
{
    public function index(Request $request)
    {
        $bookQuery = Auth::user()->books()->with(['authors', 'reviews', 'covers']);
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
        if (! in_array($sort, ['title', 'rating', 'published_date', 'added', 'colour'])) {
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
        } elseif ($sort === 'colour') {
            $books = $books->sortBy(function ($book) {
                $hex = ltrim($book->settings()->get('colour', '#000000'), '#');

                if (strlen($hex) !== 6) {
                    return 0; // fallback if invalid hex
                }

                [$r, $g, $b] = [
                    hexdec(substr($hex, 0, 2)),
                    hexdec(substr($hex, 2, 2)),
                    hexdec(substr($hex, 4, 2)),
                ];

                return rgbToHue($r, $g, $b);
            }, SORT_NUMERIC, $desc);
        }

        if ($request->filled('author')) {
            $authorUuid = $request->get('author');
            $books = $books->filter(function ($book) use ($authorUuid) {
                return $book->authors->contains('uuid', $authorUuid);
            });
        }

        $selectedStatuses = $request->get('status', []);

        return Inertia::render('books/Index', [
            'books' => BookResource::collection($books->values()),
            'selectedStatuses' => $selectedStatuses,
            'selectedAuthor' => $request->get('author'),
            'selectedSort' => $sort,
            'selectedOrder' => $request->get('order', 'desc'),
            'searchQuery' => $request->get('search', ''),
            'authors' => AuthorResource::collection(Auth::user()->books()->with('authors')->get()
                ->flatMap(fn ($book) => $book->authors)->unique('uuid'))->values(),
        ])->withMeta([
            'title' => 'Books',
        ]);
    }

    public function search(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = 10;

        return Inertia::render('books/Search', [
            'query' => $request->get('q'),
            'author' => $request->get('author'),
            'page' => $page,
            'perPage' => $perPage,
            'results' => Inertia::defer(
                fn () => SearchBooksFromApi::run(
                    query: $request->get('q'),
                    author: $request->get('author'),
                    maxResults: $perPage,
                    page: $page,
                )
            )->deepMerge()->matchOn(''),
        ])->withMeta([
            'title' => 'Add Book',
            'description' => 'Add a new book to your collection by providing its identifier (ISBN, Open Library ID, etc.).',
        ]);
    }

    public function store(StoreBookUserRequest $request)
    {
        $book = Book::where('identifier', $request->get('identifier'))->firstOr(fn () => null);

        if (! $book) {
            return redirect()->back()->with('message', 'Book not found.');
        }

        if ($request->user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('message', 'Book already added to your collection.');
        }

        $request->user()->books()->attach($book, [
            'status' => $request->get('status', UserBookStatus::PlanToRead),
        ]);

        return redirect()->back();
    }

    public function updateStatus(UpdateBookUserRequest $request, Book $book)
    {
        if (! $request->user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('message', 'Book not found in your collection.');
        }

        $request->user()->books()->updateExistingPivot($book->id, [
            'status' => $request->get('status', UserBookStatus::PlanToRead),
        ]);

        return redirect()->back()
            ->with('success', 'Book status updated successfully');
    }

    public function updateTags(Request $request, Book $book)
    {
        $bookUser = $request->user()->books()->where('book_id', $book->id)->first()?->pivot;
        $bookUser->tags = $request->get('tags', []);
        $bookUser->save();

        return redirect()->back()
            ->with('success', 'Tags updated successfully');
    }

    public function edit(Request $request)
    {
        return Inertia::render('settings/Library', [

        ])->withMeta([
            'title' => 'Library Settings',
            'description' => 'Manage your book collection and settings.',
        ]);
    }

    public function destroy(DestroyBookUserRequest $request, Book $book)
    {
        if (! $request->user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('message', 'Book not found in your collection.');
        }

        $request->user()->books()->detach($book);

        return redirect()->back();
    }
}

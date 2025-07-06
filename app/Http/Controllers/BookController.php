<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Enums\UserPermission;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReviewResource;
use App\Actions\Books\FetchOrCreateBook;
use App\Actions\Books\ImportBookFromData;
use App\Actions\Books\SearchBooksFromApi;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = 10;

        return Inertia::render('books/Search', [
            'initialQuery' => $request->get('q'),
            'initialAuthor' => $request->get('author'),
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
        $book->load(['authors', 'reviews', 'notes', 'covers', 'publisher', 'tags',
            'users' => fn ($query) => $query->where('user_id', Auth::id()),
        ]);

        dd(\request()->user()->can(UserPermission::VIEW_HORIZON_PANEL));

        $relatedBooks = $book->relatedBooksByAuthorsAndTags();
        $relatedBooks->map(fn ($related) => $related->load(['authors', 'covers']));

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
            'related' => BookResource::collection($relatedBooks),
            'averageRating' => round($book->reviews->avg('rating'), 1),
            'reviews' => Inertia::defer(fn () => ReviewResource::collection(
                $book->reviews()->where('user_id', '!=', Auth::id())->get()
            )),
        ])->withMeta([
            'title' => $book->title,
        ]);
    }

    public function preview(string $identifier)
    {
        if (Book::where('identifier', $identifier)->exists()) {
            return redirect()->route('books.show', Book::where('identifier', $identifier)->first());
        }

        ImportBookFromData::dispatchAfterResponse($identifier);

        return Inertia::render('books/Preview', [
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
                ['content' => $notes]
            );
        }

        return redirect()->back()
            ->with('success', 'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('user.books.index');
        //            ->banner('Book '.$book->code.' removed successfully');
    }
}

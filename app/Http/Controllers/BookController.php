<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReviewResource;
use App\Actions\Books\FetchOrCreateBook;
use App\Actions\Books\ImportBookFromData;
use App\Actions\Books\SearchBooksFromApi;
use App\Http\Requests\Books\StoreBookRequest;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = 10;

        return Inertia::render('books/Search', [
            'initialQuery' => $request->get('q'),
            'initialAuthor' => $request->get('author'),
            'scan' => $request->get('scan', false),
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

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['authors', 'reviews', 'ratings', 'publisher', 'tags', 'covers',
            'users' => fn ($query) => $query->where('user_id', Auth::id()),
            'notes' => fn ($query) => $query->where('user_id', Auth::id()),
        ]);

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
            'averageRating' => number_format($book->ratings->avg('value') ?? 0, 1),
            'related' => Inertia::defer(function () use ($book) {
                $relatedBooks = $book->relatedBooksByAuthorsAndTags(4);
                $relatedBooks->map(fn ($related) => $related->load(['authors', 'covers']));

                return BookResource::collection($relatedBooks);
            }),
            'reviews' => Inertia::defer(fn () => ReviewResource::collection(
                $book->reviews->load('user', 'book')
                    ->reject(fn ($review) => Auth::check() ? $review->user_id === Auth::id() : false)
            )),
        ])->withMeta([
            'title' => $book->title,
            'description' => $book->description ?? $book->title.' by '.$book->authors->pluck('name')->implode(', '),
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
        ])->withMeta([
            'title' => 'Importing Book...',
            'description' => 'We are fetching the book details from the database. This may take a few seconds.',
        ]);
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('user.books.index');
        //            ->banner('Book '.$book->code.' removed successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\Books\FetchOrCreateBook;
use App\Actions\Books\ImportBookFromData;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReviewResource;
use App\Models\Book;
use Auth;
use Inertia\Inertia;

class BookController extends Controller
{
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
        $book->load(['authors', 'reviews', 'notes', 'covers', 'publisher', 'categories']);

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
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

        ImportBookFromData::dispatch($identifier);

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
                ['title' => 'test', 'content' => $notes]
            );
        }

        return redirect()->back()
            ->with('success', 'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('library.index');
        //            ->banner('Book '.$book->code.' removed successfully');
    }
}

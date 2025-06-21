<?php

namespace App\Http\Controllers;

use App\Actions\FetchBookByIdentifier;
use App\Http\Requests\Books\DestroyBookUserRequest;
use App\Http\Requests\Books\StoreBookUserRequest;
use App\Models\Book;

class UserBookController extends Controller
{
    public function store(StoreBookUserRequest $request)
    {
        $book = FetchBookByIdentifier::run($request->get('identifier'));

        if (! $book) {
            return redirect()->back()->with('message', 'Book not found.');
        }

        if ($request->user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('message', 'Book already added to your collection.');
        }

        $request->user()->books()->attach($book);

        return redirect()->back();
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

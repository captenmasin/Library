<?php

namespace App\Http\Controllers;

use App\Actions\FetchBookByIdentifier;
use App\Enums\UserBookStatus;
use App\Http\Requests\Books\DestroyBookUserRequest;
use App\Http\Requests\Books\StoreBookUserRequest;
use App\Http\Requests\Books\UpdateBookUserRequest;
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

    public function destroy(DestroyBookUserRequest $request, Book $book)
    {
        if (! $request->user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('message', 'Book not found in your collection.');
        }

        $request->user()->books()->detach($book);

        return redirect()->back();
    }
}

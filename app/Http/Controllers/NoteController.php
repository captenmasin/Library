<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Models\Book;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(StoreNoteRequest $request, Book $book)
    {
        Note::updateOrCreate(
            [
                'book_id' => $book->id,
                'user_id' => $request->user()->id,
            ],
            [
                'content' => $request->validated('content'),
            ]
        );

        return back()->with('success', 'Note added.');
    }
}

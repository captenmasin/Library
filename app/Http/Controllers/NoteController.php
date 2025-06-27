<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyNoteRequest;
use App\Http\Requests\StoreNoteRequest;
use App\Models\Book;
use App\Models\Note;

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

    public function destroy(DestroyNoteRequest $request, Note $note)
    {
        $note->delete();

        return back()->with('success', 'Note deleted.');
    }
}

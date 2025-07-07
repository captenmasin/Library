<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Note;
use App\Enums\ActivityType;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\DestroyNoteRequest;

class NoteController extends Controller
{
    public function store(StoreNoteRequest $request, Book $book)
    {
        $note = Note::where('book_id', $book->id)
            ->where('user_id', $request->user()->id)
            ->first();

        $updatedNote = Note::updateOrCreate(
            [
                'book_id' => $book->id,
                'user_id' => $request->user()->id,
            ],
            [
                'content' => $request->validated('content'),
            ]
        );

        logActivity(
            $note ? ActivityType::BookNoteUpdated : ActivityType::BookNoteAdded,
            $updatedNote,
            [
                'note' => $updatedNote->content,
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        return back()->with('success', 'Note added.');
    }

    public function destroy(DestroyNoteRequest $request, Book $book, Note $note)
    {
        $note->delete();

        logActivity(ActivityType::BookNoteRemoved, $book, [
            'book_identifier' => $book->identifier,
            'book_title' => $book->title,
        ]);

        return back()->with('success', 'Note deleted.');
    }
}

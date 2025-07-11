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
        $note = $book->notes()->create([
            'user_id' => $request->user()->id,
            'book_status' => $book->getUserStatus($request->user()),
            'content' => $request->validated('content'),
        ]);

        logActivity(
            //            $note ? ActivityType::BookNoteUpdated : ActivityType::BookNoteAdded,
            ActivityType::BookNoteAdded,
            $note,
            [
                'note' => $note->content,
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

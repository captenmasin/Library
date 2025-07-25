<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Note;
use Inertia\Inertia;
use App\Actions\TrackEvent;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use App\Enums\AnalyticsEvent;
use App\Http\Resources\NoteResource;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\DestroyNoteRequest;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = $request->user()->notes()
            ->with(['book.authors', 'book.covers', 'book.ratings'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('user/Notes', [
            'notes' => NoteResource::collection($notes),
            'breadcrumbs' => [
                ['title' => 'Home', 'href' => route('home')],
                ['title' => 'Notes', 'href' => route('user.notes.index')],
            ],
        ])->withMeta([
            'title' => 'Notes',
            'description' => 'A list of your private notes on books.',
        ]);
    }

    public function store(StoreNoteRequest $request, Book $book)
    {
        $note = $book->notes()->create([
            'user_id' => $request->user()->id,
            'book_status' => $book->getUserStatus($request->user()),
            'content' => $request->validated('content'),
        ]);

        TrackEvent::dispatch(AnalyticsEvent::BookNoteAdded, [
            'user_id' => $request->user()?->id,
            'book' => [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
                'note' => $note->content,
            ],
        ]);

        $request->user()->logActivity(
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

        TrackEvent::dispatch(AnalyticsEvent::BookNoteRemoved, [
            'user_id' => $request->user()?->id,
            'book' => [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ],
        ]);

        $request->user()->logActivity(ActivityType::BookNoteRemoved, $book, [
            'book_identifier' => $book->identifier,
            'book_title' => $book->title,
        ]);

        return back()->with('success', 'Note deleted.');
    }
}

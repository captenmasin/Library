<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Note;
use Inertia\Inertia;
use App\Actions\TrackEvent;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Support\SubscriptionLimits;
use App\Http\Resources\NoteResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreNoteRequest;
use Inertia\Response as InertiaResponse;
use App\Http\Requests\DestroyNoteRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoteController extends Controller
{
    public function index(Request $request): InertiaResponse|AnonymousResourceCollection
    {
        $notes = $request->user()->notes()
            ->with(['book.authors', 'book.covers', 'book.ratings'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson()) {
            return NoteResource::collection($notes);
        }

        return Inertia::render('user/Notes', [
            'notes' => NoteResource::collection($notes),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Notes', 'href' => route('user.notes.index')],
            ],
        ])->withMeta([
            'title' => 'Notes',
            'description' => 'A list of your private notes on books.',
        ]);
    }

    public function store(StoreNoteRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        // Enforce subscription limitation for private notes
        if (! SubscriptionLimits::allowPrivateNotes($request->user())) {
            $message = 'Your current plan does not allow adding notes.';

            return $request->wantsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        if (! $request->user()->books()->whereKey($book->id)->exists()) {
            $message = 'You can only add notes to books in your library.';

            return $request->wantsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        $note = $book->notes()->create([
            'user_id' => $request->user()->id,
            'book_status' => $book->getUserStatus($request->user()),
            'content' => $request->validated('content'),
        ]);

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookNoteAdded, [
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

        return $request->wantsJson()
            ? (new NoteResource($note))->response()->setStatusCode(201)
            : back()->with('success', 'Note added.');
    }

    public function destroy(DestroyNoteRequest $request, Book $book, Note $note): Response|RedirectResponse
    {
        $note->delete();

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookNoteRemoved, [
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

        return $request->wantsJson()
            ? response()->noContent()
            : back()->with('success', 'Note deleted.');
    }
}

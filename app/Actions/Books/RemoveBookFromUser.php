<?php

namespace App\Actions\Books;

use App\Models\Book;
use App\Models\User;
use App\Actions\TrackEvent;
use App\Enums\ActivityType;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Http\Requests\Books\DestroyBookUserRequest;

class RemoveBookFromUser
{
    use AsAction;

    public function handle(Book $book, User $user): void
    {
        if (! $user->books()->where('book_id', $book->id)->exists()) {
            throw new \Exception('Book not found in user library.');
        }

        $user->book_covers->where('book_id', $book->id)->each(function ($cover) {
            $cover->delete();
        });

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookRemoved, [
            'user_id' => $user->id,
            'book' => [
                'identifier' => $book->identifier,
                'title' => $book->title,
            ],
        ]);

        $user->logActivity(
            ActivityType::BookRemoved,
            $book, [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        $user->books()->detach($book);
    }

    public function asController(DestroyBookUserRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        try {
            $this->handle($book, $request->user());

            return $request->wantsJson()
                ? response()->json([
                    'success' => true,
                    'message' => 'Book removed from your library successfully.',
                ])
                : redirect()->back()->with('success', 'Book removed from your library successfully.');
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400)
                : redirect()->back()->with('error', $e->getMessage());
        }
    }
}

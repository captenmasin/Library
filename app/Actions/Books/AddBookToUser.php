<?php

namespace App\Actions\Books;

use App\Models\Book;
use App\Models\User;
use App\Actions\TrackEvent;
use App\Enums\ActivityType;
use App\Enums\AnalyticsEvent;
use App\Enums\UserBookStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Http\Requests\Books\StoreBookUserRequest;

class AddBookToUser
{
    use AsAction;

    public function handle(Book $book, User $user, ?UserBookStatus $status = null): void
    {
        if ($user->books()->where('book_id', $book->id)->exists()) {
            throw new \Exception('Book already exists in your library.');
        }

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookAdded, [
            'user_id' => $user->id,
            'book' => [
                'identifier' => $book->identifier,
                'title' => $book->title,
                'status' => $status?->value ?? UserBookStatus::PlanToRead->value,
            ],
        ]);

        $user->books()->attach($book, [
            'status' => $status->value ?? UserBookStatus::PlanToRead,
        ]);

        $user->logActivity(ActivityType::BookAdded, $book, [
            'book_identifier' => $book->identifier,
            'book_title' => $book->title,
            'status' => $status->value ?? UserBookStatus::PlanToRead->value,
        ]);
    }

    public function asController(StoreBookUserRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $book = Book::where('identifier', $request->get('identifier'))->firstOr(fn () => null);
            $status = $request->enum('status', UserBookStatus::class);
            $this->handle(
                $book,
                $request->user(),
                $request->enum('status', UserBookStatus::class, default: UserBookStatus::PlanToRead)
            );

            return $request->wantsJson()
                ? response()->json([
                    'success' => true,
                    'message' => 'Book added to your library successfully.',
                ])
                : redirect()->back()->with('success', 'Book added to your library successfully.');
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

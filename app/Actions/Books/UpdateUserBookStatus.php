<?php

namespace App\Actions\Books;

use Exception;
use App\Models\Book;
use App\Models\User;
use App\Enums\ActivityType;
use App\Enums\UserBookStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Http\Requests\Books\UpdateBookUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateUserBookStatus
{
    use AsAction;

    public function handle(User $user, Book $book, UserBookStatus $status): int
    {
        $updated = $user->books()
            ->updateExistingPivot($book->id, ['status' => $status]);

        if (! $updated) {
            throw new ModelNotFoundException('Book not found in your library.');
        }

        logActivity(ActivityType::BookStatusUpdated, $book, [
            'status' => $status,
        ]);

        return $updated;
    }

    public function asController(UpdateBookUserRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        try {
            $this->handle(
                $request->user(),
                $book,
                $request->enum('status', UserBookStatus::class, default: UserBookStatus::PlanToRead)
            );

            return $request->wantsJson()
                ? response()->json([
                    'success' => true,
                    'message' => 'Book status updated successfully.',
                    'status' => $request->enum('status', UserBookStatus::class),
                ])
                : redirect()->back()->with('success', 'Book status updated successfully.');

        } catch (Exception $e) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 404)
                : redirect()->back()->with('error', $e->getMessage());
        }
    }
}

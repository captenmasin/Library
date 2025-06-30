<?php

namespace App\Actions\Books;

use App\Enums\UserBookStatus;
use App\Http\Requests\Books\UpdateBookUserRequest;
use App\Models\Book;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserBookStatus
{
    use AsAction;

    public function handle(User $user, Book $book, string $status): int
    {
        $updated = $user->books()
            ->updateExistingPivot($book->id, ['status' => $status]);

        if (! $updated) {
            throw new ModelNotFoundException('Book not found in your library.');
        }

        return $updated;
    }

    public function asController(UpdateBookUserRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        try {
            $this->handle(
                $request->user(),
                $book,
                $request->get('status', UserBookStatus::PlanToRead)
            );

            return $request->wantsJson()
                ? response()->json([
                    'success' => true,
                    'message' => 'Book status updated successfully.',
                    'status' => $request->get('status', UserBookStatus::PlanToRead),
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

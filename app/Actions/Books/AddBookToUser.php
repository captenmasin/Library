<?php

namespace App\Actions\Books;

use App\Models\Book;
use App\Models\User;
use App\Enums\UserBookStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Http\Requests\Books\StoreBookUserRequest;

class AddBookToUser
{
    use AsAction;

    public function handle(Book $book, User $user, ?string $status = null): void
    {
        if ($user->books()->where('book_id', $book->id)->exists()) {
            throw new \Exception('Book already exists in your library.');
        }

        $user->books()->attach($book, [
            'status' => $status ?? UserBookStatus::PlanToRead,
        ]);
    }

    public function asController(StoreBookUserRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $book = Book::where('identifier', $request->get('identifier'))->firstOr(fn () => null);
            $this->handle($book, $request->user(), $request->get('status', UserBookStatus::PlanToRead));

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

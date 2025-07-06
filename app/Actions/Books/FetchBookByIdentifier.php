<?php

namespace App\Actions\Books;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchBookByIdentifier
{
    use AsAction;

    public function handle(string $identifier): ?Book
    {
        return Book::where('identifier', $identifier)->firstOr(fn () => null);
    }

    public function asController(Request $request, string $identifier): JsonResponse
    {
        if (! $identifier) {
            abort(400, 'Identifier is required');
        }

        $book = $this->handle($identifier);

        if (! $book) {
            abort(404, 'Book not found');
        }

        return response()->json([
            'data' => $book,
        ]);
    }
}

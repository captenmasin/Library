<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Http\Resources\BookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchOrCreateBook
{
    use AsAction;

    public function __construct(protected BookApiServiceInterface $booksApi) {}

    public function handle(string $identifier)
    {
        $book = FetchBookByIdentifier::run($identifier);

        return $book ?: ImportBookFromData::run($identifier);
    }

    public function asController(Request $request): JsonResponse
    {
        $identifier = $request->get('identifier');

        if (! $identifier) {
            abort(400, 'Identifier is required');
        }

        $book = $this->handle($identifier);

        return response()->json([
            'book' => new BookResource($book),
        ]);
    }
}

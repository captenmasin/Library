<?php

namespace App\Actions;

use App\Contracts\BookApiServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetBookByBarcode
{
    use AsAction;

    public function __construct(protected BookApiServiceInterface $booksApi) {}

    public function handle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $book = $this->booksApi::getByCode($request->get('code'));

        if (! $book) {
            return response()->json([
                'errors' => [
                    'code' => 'Book not found',
                ],
            ], 422);
        }

        return response()->json($book);
    }
}

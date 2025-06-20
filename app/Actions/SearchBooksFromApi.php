<?php

namespace App\Actions;

use App\Services\BooksApiService;
use Cache;
use Captenmasin\GoogleBooks\GoogleBooks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchBooksFromApi
{
    use AsAction;

    public function handle(Request $request): JsonResponse
    {
        $results = Cache::remember('books:search:' . $request->query('q'), 60 * 60, function () use ($request) {
            return (new BooksApiService())->search($request->query('q'));
        });

        return response()->json($results);
    }
}

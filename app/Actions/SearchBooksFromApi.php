<?php

namespace App\Actions;

use App\Contracts\BookApiServiceInterface;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchBooksFromApi
{
    use AsAction;

    public function __construct(protected BookApiServiceInterface $booksApi) {}

    public function handle(?string $query = null, ?string $author = null, bool $cache = true): array
    {
        $cacheKey = 'books:search:'.md5(strtolower(trim($query)).'__'.strtolower(trim($author)));
        $cacheTTL = $cache ? now()->addDay() : null;

        return Cache::remember($cacheKey, $cacheTTL, function () use ($query, $author) {
            return $this->booksApi::search(
                $query, $author
            );
        });
    }

    public function asController(Request $request): JsonResponse
    {
        $results = $this->handle(
            $request->query('q'),
            $request->query('author'),
        );

        return response()->json($results);
    }
}

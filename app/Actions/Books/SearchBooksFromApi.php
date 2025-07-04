<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Data\BookData;
use App\Models\Book;
use App\Transformers\BookTransformer;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchBooksFromApi
{
    use AsAction;

    public function __construct(protected BookApiServiceInterface $booksApi) {}

    public function handle(
        ?string $query = null,
        ?string $author = null,
        int $maxResults = 30,
        int $page = 1,
        bool $cache = true): array
    {
        $cacheKey = $this->getCachedResultsKey(query: $query ?? '', author: $author ?? '', page: $page, maxResults: $maxResults);
        $cacheTTL = $cache ? now()->addDay() : null;

        return Cache::remember($cacheKey, $cacheTTL, function () use ($query, $author, $maxResults, $page) {
            $results = $this->booksApi::search(
                query: $query,
                author: $author,
                maxResults: $maxResults,
                page: $page
            );

            $total = $results['total'] ?? 0;
            $books = collect($results['items'] ?? []);

            $allBooks = Book::whereIn('identifier', $books->pluck('identifier'))
                ->get()
                ->keyBy('identifier');

            $results = BookData::collect(
                $books->map(fn ($book) => BookTransformer::fromIsbn($book, $allBooks))
            );

            ImportBooksFromArray::dispatch($books);

            return [
                'total' => $total,
                'books' => $results,
            ];
        });

    }

    private function getCachedResultsKey(string $query = '', string $author = '', int $page = 1, int $maxResults = 30): string
    {
        return 'books:search:'.md5(strtolower(trim($query)).'__'.strtolower(trim($author)).'__'.$page.'__'.$maxResults);
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

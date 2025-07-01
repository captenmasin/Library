<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Models\Book;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

            $allBooks = Book::whereIn('identifier', $books->pluck('identifier'))->get()->keyBy('identifier');
            $results = $books->map(fn ($book) => $this->transform($book, $allBooks));

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

    private function transform($book, $allBooks): array
    {
        if (empty($book['identifier'])) {
            return [];
        }

        if ($allBooks->has($book['identifier'])) {
            $existing = $allBooks->get($book['identifier']);
            $book['links']['show'] = route('books.show', $existing);
        } else {
            $book['links']['show'] = route('books.preview', ['identifier' => $book['identifier']]);
        }

        $book['authors'] = collect($book['authors'] ?? [])
            ->map(fn ($author) => [
                'name' => $author ?? 'Unknown',
                'uuid' => (string) Str::uuid(),
            ])
            ->values()
            ->toArray();

        return $book;
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

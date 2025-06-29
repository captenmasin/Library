<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Models\Book;
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
        $page = 1,
        bool $cache = true): array
    {
        $cacheKey = 'books:search:'.md5(strtolower(trim($query)).'__'.strtolower(trim($author)));
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

            $existingBooks = Book::whereIn('identifier', $books->pluck('identifier'))->get()->keyBy('identifier');

            $results = $books->map(function ($book) use ($existingBooks) {
                if ($existingBooks->has($book['identifier'])) {
                    $existing = $existingBooks->get($book['identifier']);
                    $book['imported'] = true;
                    $book['link'] = route('books.show', $existing);
                } else {
                    $book['imported'] = false;
                    $book['link'] = route('books.preview', ['identifier' => $book['identifier']]);
                }

                return $book;
            });

            ImportBooksFromArray::dispatch($books);

            return [
                'total' => $total,
                'books' => $results,
            ];
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

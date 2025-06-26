<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Models\Book;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchBooksFromApi
{
    use AsAction;

    public function __construct(protected BookApiServiceInterface $booksApi) {}

    public function handle(?string $query = null, ?string $author = null, bool $cache = true): Collection
    {
        $cacheKey = 'books:search:'.md5(strtolower(trim($query)).'__'.strtolower(trim($author)));
        $cacheTTL = $cache ? now()->addDay() : null;

        return Cache::remember($cacheKey, $cacheTTL, function () use ($query, $author) {
            $results = collect($this->booksApi::search(
                $query, $author
            ));

            $existingBooks = Book::whereIn('identifier', $results->pluck('identifier'))->get()->keyBy('identifier');

            $results = $results->map(function ($book) use ($existingBooks) {
                if ($existingBooks->has($book['identifier'])) {
                    $existing = $existingBooks->get($book['identifier']);
                    $book['imported'] = true;
                    $book['link'] = route('books.show', $existing);
                } else {
                    $book['imported'] = false;
                    $book['link'] = route('books.temporary', ['identifier' => $book['identifier']]);
                }

                return $book;
            });

            ImportBooksFromArray::dispatch($results);

            return $results;
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

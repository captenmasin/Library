<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Transformers\BookTransformer;
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
        int $page = 1): array
    {
        $results = $this->booksApi->search(
            query: $query,
            author: $author,
            maxResults: $maxResults,
            page: $page
        );

        $total = $results['total'] ?? 0;
        $books = collect($results['items'] ?? []);
        $results = $books->map(fn ($book) => BookTransformer::fromIsbn($book));

        ImportBooksFromArray::dispatch($books);

        return [
            'total' => $total,
            'books' => $results,
        ];
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

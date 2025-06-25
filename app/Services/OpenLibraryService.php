<?php

namespace App\Services;

use App\Contracts\BookApiServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenLibraryService implements BookApiServiceInterface
{
    public const string ServiceName = 'OpenLibrary';

    protected static string $baseUrl = 'https://openlibrary.org';

    public static function search(
        ?string $query = null,
        ?string $author = null,
        int $maxResults = 30,
        $pageIndex = 0
    ): array {
        if (! $query) {
            return [];
        }

        $params = [
            'q' => $query.($author ? " author:$author" : ''),
            'limit' => $maxResults,
            'offset' => $pageIndex * $maxResults,
        ];

        $response = Http::get(self::$baseUrl.'/search.json', $params);

        $docs = $response->json('docs') ?? [];

        return collect($docs)
            ->map(fn ($doc) => self::transform($doc))
            ->filter()
            ->all();
    }

    public static function get(string $id): ?array
    {
        return Cache::remember("books:id:$id", now()->addWeek(), function () use ($id) {
            return self::transform(self::getFromApi($id));
        });
    }

    public static function getByCode(string $isbn): ?array
    {
        return self::get($isbn);
    }

    public static function getFromApi(string $isbn): ?array
    {
        $response = Http::get(self::$baseUrl.'/isbn/'.urlencode($isbn).'.json');

        if (! $response->ok()) {
            return null;
        }

        return $response->json();
    }

    protected static function transform(?array $book = null): ?array
    {
        if (! $book) {
            return null;
        }

        dd($book);

        return [
            'codes' => [
                ['type' => 'ISBN_13', 'identifier' => $book['isbn_13'][0] ?? null],
                ['type' => 'ISBN_10', 'identifier' => $book['isbn_10'][0] ?? null],
            ],
            'identifier' => $book['isbn_13'][0] ?? $book['isbn_10'][0] ?? null,
            'title' => $book['title'] ?? null,
            'pageCount' => $book['number_of_pages'] ?? null,
            'categories' => $book['subjects'] ?? null,
            'publisher' => is_array($book['publishers'] ?? null) ? implode(', ', $book['publishers']) : $book['publishers'] ?? null,
            'description' => is_array($book['description'] ?? null) ? ($book['description']['value'] ?? null) : ($book['description'] ?? null),
            'authors' => $book['author_name'] ?? null,
            'publishedDate' => $book['publish_date'] ?? null,
            'cover' => isset($book['covers'][0]) ? 'https://covers.openlibrary.org/b/id/'.$book['covers'][0].'-L.jpg' : null,
            'service' => self::ServiceName,
        ];
    }
}

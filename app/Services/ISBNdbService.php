<?php

namespace App\Services;

use App\Contracts\BookApiServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ISBNdbService implements BookApiServiceInterface
{
    public const string ServiceName = 'ISBNdb';

    protected static string $baseUrl = 'https://api2.isbndb.com';

    public static function search(
        ?string $query = null,
        ?string $author = null,
        int $maxResults = 30,
        $pageIndex = 0
    ): array {
        if (! $query) {
            return [];
        }

        $queryParts = [];
        $queryParts['page'] = $pageIndex + 1; // API uses 1-based indexing
        $queryParts['pageSize'] = $maxResults;

        if ($author) {
            $queryParts['author'] = $author;
        }

        $response = Http::withHeaders([
            'Authorization' => config('services.isbndb.key'),
        ])->get(self::$baseUrl.'/books/'.urlencode($query).'?'.http_build_query($queryParts));

        $items = $response->json('books') ?? [];

        return collect($items)
            ->filter(function ($book) use ($author) {
                if (! empty($author)) {
                    $authors = array_map('strtolower', $book['authors']);

                    return in_array(strtolower($author), $authors);
                }

                return true;
            })
            ->map(fn ($book) => self::transform($book))
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

    public static function getFromApi(string $id): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => config('services.isbndb.key'),
        ])->get(self::$baseUrl.'/book/'.urlencode($id));

        if (! $response->ok()) {
            return null;
        }

        return $response->json('book');
    }

    protected static function transform(?array $book = null): ?array
    {
        if (! $book) {
            return null;
        }

        return [
            'codes' => [
                ['type' => 'ISBN_13', 'identifier' => $book['isbn13'] ?? null],
                ['type' => 'ISBN_10', 'identifier' => $book['isbn'] ?? null],
            ],
            'identifier' => $book['isbn13'] ?? $book['isbn'] ?? null,
            'title' => $book['title'] ?? null,
            'pageCount' => $book['pages'] ?? null,
            'categories' => $book['subjects'] ?? null,
            'publisher' => $book['publisher'] ?? null,
            'description' => $book['overview'] ?? $book['synopsis'] ?? null,
            'authors' => $book['authors'] ?? null,
            'publishedDate' => $book['date_published'] ?? null,
            'cover' => $book['image_original'] ?? $book['image'] ?? null,
            'service' => self::ServiceName,
        ];
    }
}

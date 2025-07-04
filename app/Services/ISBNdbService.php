<?php

namespace App\Services;

use App\Contracts\BookApiServiceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Uri;

class ISBNdbService implements BookApiServiceInterface
{
    public const ServiceName = 'ISBNdb';

    protected static string $baseUrl = 'https://api2.isbndb.com';

    protected static function client(): PendingRequest
    {
        static $client;

        return $client ??= Http::withHeaders([
            'Authorization' => config('services.isbndb.key'),
        ]);
    }

    public static function search(
        ?string $query = null,
        ?string $author = null,
        int $maxResults = 30,
        $page = 1
    ): array {
        if (! $query && ! $author) {
            return [];
        }

        $sanitizedQuery = Str::slug($query);
        $sanitizedAuthor = Str::slug($author);
        $hash = md5("$sanitizedQuery|$sanitizedAuthor|$maxResults|$page");
        $cacheKey = "books:search:$hash";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($query, $author, $maxResults, $page, $cacheKey) {
            $queryParts = collect([
                'page' => $page,
                'pageSize' => $maxResults,
                'shouldMatchAll' => 0,
            ])->when($author, function ($parts) use ($author, &$query) {
                $query .= ' '.$author;

                return $parts->put('shouldMatchAll', 1);
            })->all();

            $url = Uri::of(self::$baseUrl)
                ->withPath('books/'.urlencode($query))
                ->withQuery($queryParts);

            $response = self::client()
                ->retry(3, 200)
                ->get($url);

            if (! $response->ok()) {
                Cache::forget($cacheKey);

                return [];
            }

            $items = $response->json('books') ?? [];

            return [
                'total' => $response->json('total'),
                'items' => collect($items)
                    ->map(fn ($book) => self::transform($book))
                    ->filter()
                    ->all(),
            ];
        });
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
        $response = self::client()
            ->retry(3, 200)
            ->get(self::$baseUrl.'/book/'.urlencode($id));

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

        $subjects = array_values(array_unique(array_filter(array_map(function ($subject) {
            return trim(Str::before($subject, '--'));
        }, $book['subjects'] ?? []))));

        $description = $book['overview'] ?? $book['synopsis'] ?? null;

        return [
            'codes' => [
                ['type' => 'ISBN_13', 'identifier' => $book['isbn13'] ?? null],
                ['type' => 'ISBN_10', 'identifier' => $book['isbn'] ?? null],
            ],
            'identifier' => $book['isbn13'] ?? $book['isbn'] ?? null,
            'title' => $book['title'] ?? null,
            'pageCount' => $book['pages'] ?? null,
            'categories' => $subjects ?? null,
            'publisher' => $book['publisher'] ?? null,
            'description' => $description,
            'description_clean' => strip_tags($description ?? ''),
            'authors' => $book['authors'] ?? null,
            'publishedDate' => $book['date_published'] ?? null,
            'cover' => $book['image_original'] ?? $book['image'] ?? null,
            'service' => self::ServiceName,
        ];
    }
}

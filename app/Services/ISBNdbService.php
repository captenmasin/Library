<?php

namespace App\Services;

use App\Contracts\BookApiServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Uri;

class ISBNdbService implements BookApiServiceInterface
{
    public const ServiceName = 'ISBNdb';

    protected static string $baseUrl = 'https://api2.isbndb.com';

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
        $cacheKey = "books:search:query:$sanitizedQuery:author:$sanitizedAuthor:maxResults:$maxResults:page:$page";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($query, $author, $maxResults, $page, $cacheKey) {
            $queryParts = [];
            $queryParts['page'] = $page; // API uses 1-based indexing
            $queryParts['pageSize'] = $maxResults;
            $queryParts['shouldMatchAll'] = 0;

            if ($author) {
                $query .= ' '.$author;
                $queryParts['shouldMatchAll'] = 1;
            }

            $url = Uri::of(self::$baseUrl)
                ->withPath('books/'.urlencode($query))
                ->withQuery($queryParts);

            $response = Http::withHeaders([
                'Authorization' => config('services.isbndb.key'),
            ])->get($url);

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

        $subjects = $book['subjects'] ?? [];
        $subjects = collect($subjects)->map(function ($subject) {
            return trim(Str::before($subject, '--'));
        })->filter()->unique()->values()->all();

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

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    protected static string $baseUrl = 'https://www.googleapis.com/books/v1';

    protected const COVER_SIZES = [
        'extraLarge',
        'large',
        'medium',
        'thumbnail',
        'smallThumbnail',
    ];

    protected const FIELDS = [
        'items' => [
            'id',
            'volumeInfo' => [
                'title',
                'pageCount',
                'publisher',
                'description',
                'authors',
                'publishedDate',
                'categories',
                'maturityRating',
                'industryIdentifiers' => [
                    'type',
                    'identifier',
                ],
                'imageLinks',
            ],
        ],
    ];

    public static function search(
        ?string $query = null,
        ?string $author = null,
        int $maxResults = 15,
        $pageIndex = 0): array
    {
        $response = Http::get(self::$baseUrl.'/volumes', [
            'q' => $query.($author ? (' inauthor:"'.$author.'"') : ''),
            'startIndex' => $pageIndex * $maxResults,
            'fields' => self::buildFieldsString(),
            'maxResults' => $maxResults,
        ]);

        $items = $response->json('items') ?? [];

        return collect($items)
            ->map(fn ($item) => self::transform($item))
            ->filter()
            ->all();
    }

    public static function get(string $volumeId): ?array
    {
        return Cache::remember('books:id:'.$volumeId, now()->addWeek(), function () use ($volumeId) {
            return self::transform(self::getFromApi($volumeId));
        });
    }

    public static function getByCode(string $isbn): ?array
    {
        $results = self::search("isbn:{$isbn}");

        return $results[0] ?? null;
    }

    protected static function getFromApi(string $volumeId): ?array
    {
        $response = Http::get(self::$baseUrl."/volumes/{$volumeId}");
        if (! $response->ok()) {
            return null;
        }

        return $response->json();
    }

    protected static function buildFieldsString(?array $fields = null)
    {
        if (! $fields) {
            $fields = self::FIELDS;
        }

        return collect($fields)
            ->map(function ($value, $key) {
                // Numeric keys → scalar values (leaf nodes)
                if (is_int($key)) {
                    return $value;
                }

                return $key.'('.self::buildFieldsString($value).')';
            })
            ->implode(',');
    }

    protected static function transform(?array $volume = null): ?array
    {
        if (! $volume || empty($volume['volumeInfo'])) {
            return null;
        }

        $book = $volume['volumeInfo'];
        $book['id'] = $volume['id'] ?? null;

        return [
            'codes' => $book['industryIdentifiers'] ?? [],
            'identifier' => $book['id'] ?? null,
            'title' => $book['title'] ?? null,
            'pageCount' => $book['pageCount'] ?? null,
            'categories' => $book['categories'] ?? null,
            'maturityRating' => $book['maturityRating'] ?? null,
            'publisher' => $book['publisher'] ?? null,
            'description' => $book['description'] ?? null,
            'authors' => $book['authors'] ?? null,
            'publishedDate' => $book['publishedDate'] ?? null,
            'cover' => $book['imageLinks']['extraLarge'] ??
                      $book['imageLinks']['large'] ??
                      $book['imageLinks']['medium'] ??
                      $book['imageLinks']['thumbnail'] ??
                      $book['imageLinks']['smallThumbnail'] ?? null,
        ];
    }
}

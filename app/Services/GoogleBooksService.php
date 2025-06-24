<?php

namespace App\Services;

use App\Contracts\BookApiServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleBooksService implements BookApiServiceInterface
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
        int $maxResults = 30,
        $pageIndex = 0): array
    {
        $query = $query.($author ? (' inauthor:"'.$author.'"') : '');
        $query = trim($query);

        $response = Http::get(self::$baseUrl.'/volumes', [
            'q' => $query,
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

    public static function get(string $id): ?array
    {
        return Cache::remember('books:id:'.$id, now()->addWeek(), function () use ($id) {
            return self::transform(self::getFromApi($id));
        });
    }

    public static function getByCode(string $isbn): ?array
    {
        $results = self::search("isbn:{$isbn}");

        return $results[0] ?? null;
    }

    public static function getFromApi(string $id): ?array
    {
        $response = Http::get(self::$baseUrl."/volumes/{$id}");
        if (! $response->ok()) {
            return null;
        }

        return $response->json();
    }

    protected static function buildFieldsString(?array $fields = null): string
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

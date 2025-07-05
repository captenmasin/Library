<?php

namespace App\Transformers;

use App\Models\Book;
use App\Services\ISBNdbService;
use Illuminate\Support\Str;

class BookTransformer
{
    public static function fromIsbn(array $data): array
    {
        $identifier = $data['isbn13'] ?? $data['isbn'] ?? $data['isbn10'] ?? null;

        $existing = Book::where('identifier', $identifier)->first();
        $links = $existing
            ? ['show' => route('books.show', $existing)]
            : ['show' => route('books.preview', ['identifier' => $identifier])];

        $subjects = collect($data['subjects'] ?? [])
            ->map(fn ($s) => trim(Str::before($s, '--')))
            ->unique()->values()->all();

        $authors = collect($data['authors'] ?? [])
            ->map(fn ($author) => [
                'name' => $author ?? 'Unknown',
                'uuid' => null,
            ])->values()->toArray();

        $description = $data['overview'] ?? $data['synopsis'] ?? $data['description'] ?? null;

        return [
            'identifier' => $identifier,
            'codes' => [
                ['type' => 'ISBN_13', 'identifier' => $data['isbn13'] ?? null],
                ['type' => 'ISBN_10', 'identifier' => $data['isbn'] ?? null],
            ],
            'title' => $data['title'] ?? null,
            'pageCount' => $data['pages'] ?? null,
            'tags' => $subjects ?? null,
            'publisher' => ! empty($data['publisher']) ? [
                'name' => $data['publisher'],
                'uuid' => null,
            ] : null,
            'description' => $description,
            'description_clean' => strip_tags($description ?? ''),
            'authors' => $authors,
            'published_date' => $data['date_published'] ?? null,
            'cover' => $data['image_original'] ?? $data['image'] ?? null,
            'service' => ISBNdbService::ServiceName,
            'links' => $links,
        ];
    }
}

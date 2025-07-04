<?php

namespace App\Transformers;

use App\Data\BookData;
use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BookTransformer
{
    /**
     * Transform external API response into a BookData DTO.
     */
    public static function fromIsbn(array $book, null|array|Collection $existingIdentifiers = null): BookData
    {
        $subjects = array_values(array_unique(array_filter(array_map(function ($subject) {
            return trim(Str::before($subject, '--'));
        }, $book['subjects'] ?? []))));

        $description = $book['overview'] ?? $book['synopsis'] ?? null;
        $identifier = $book['isbn13'] ?? $book['isbn'] ?? null;
        $isImported = ! empty($existingIdentifiers) && collect($existingIdentifiers)->has($identifier);

        return BookData::from([
            'identifier' => $identifier,
            'codes' => [
                ['type' => 'ISBN_13', 'identifier' => $book['isbn13'] ?? null],
                ['type' => 'ISBN_10', 'identifier' => $book['isbn'] ?? null],
            ],
            'title' => $book['title'] ?? null,
            'description' => $description,
            'description_clean' => strip_tags($description ?? ''),
            'published_date' => $book['date_published'] ?? null,
            'categories' => $subjects ?: [],
            'page_count' => $book['pages'] ?? null,
            'publisher' => ! empty($book['publisher']) ? [
                'uuid' => Str::random(8),
                'name' => $book['publisher'],
            ] : null,
            'authors' => collect($book['authors'] ?? [])->map(fn ($author) => [
                'uuid' => Str::uuid()->toString(),
                'name' => $author,
            ])->toArray(),
            'cover' => $book['image_original'] ?? $book['image'] ?? null,
            'service' => 'isbn',
            'links' => [
                'show' => $isImported
                    ? route('books.show', $book['identifier'])
                    : route('books.preview', ['identifier' => $book['isbn13'] ?? $book['isbn']]),
            ],
        ]);
    }

    /**
     * Transform a local Book model into BookData DTO.
     */
    public static function fromModel(Book $book, ?\App\Models\User $user = null): BookData
    {
        return BookData::from([
            'identifier' => $book->identifier,
            'title' => $book->title,
            'description' => $book->description,
            'description_clean' => strip_tags($book->description),
            'published_date' => $book->published_date,
            'categories' => $book->categories?->pluck('name')->toArray(),
            'page_count' => $book->page_count,
            'publisher' => $book->publisher?->name,
            'authors' => $book->authors?->map(fn ($a) => [
                'uuid' => $a->uuid ?? Str::uuid()->toString(),
                'name' => $a->name,
            ])->toArray(),
            'cover' => $book->getCover($user),
            'has_custom_cover' => $user ? $book->hasCustomCover($user) : false,
            'in_library' => $user ? $book->isInLibrary($user) : false,
            'user_status' => $user ? $book->getUserStatus($user) : null,
            'user_tags' => $user ? $book->getUserTags($user) : [],
            'colour' => $book->settings()->get('colour', '#000000'),
            'created_at' => $book->created_at,
            'updated_at' => $book->updated_at,
            'links' => [
                'show' => route('books.show', $book),
            ],
        ]);
    }
}

<?php

namespace App\Services;

use Captenmasin\GoogleBooks\GoogleBooks;
use Captenmasin\GoogleBooks\Volume;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BooksApiService
{
    public GoogleBooks $api;

    public function __construct()
    {
        $this->api = new GoogleBooks;
    }

    public function getById(int|string $id): ?array
    {
        return $this->transform($this->api->volumes->get($id));
    }

    public function getByCode(string|int $code): ?array
    {
        return $this->transform($this->api->volumes->byIsbn($code));
    }

    public function search(string $query = ''): array
    {
        $results = [];
        $this->api->maxResults = 30;

        foreach ($this->api->volumes->search($query) as $book) {
            $results[] = $this->transform($book);
        }

        return $results;
        //        return Cache::remember('book:'.Str::slug($query), now()->addDay(), fn () => $results);
    }

    private function transform(?Volume $volume = null): ?array
    {
        if (! $volume) {
            return null;
        }

        return [
            'codes' => $volume->industryIdentifiers,
            'identifier' => $volume->id,
            'title' => $volume->title,
            'publisher' => $volume->publisher,
            'description' => $volume->description,
            'authors' => $volume->authors ?? null,
            'publishedDate' => $volume->publishedDate,
            'cover' => $volume->getCover('extraLarge'),
        ];
    }
}

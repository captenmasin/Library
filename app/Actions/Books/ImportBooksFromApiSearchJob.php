<?php

namespace App\Actions\Books;

use App\Models\Tag;
use App\Models\Author;
use App\Models\Publisher;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportBooksFromApiSearchJob
{
    use AsAction;

    public function handle($books): void
    {
        $collection = collect($books);

        // Preload authors/tags/publishers
        $allAuthorNames = $collection->pluck('authors')->flatten(1)->pluck('name')->unique()->values();
        $allTagNames = $collection->pluck('tags')
            ->flatten()
            ->map(fn ($tagPath) => array_map('trim', explode('/', $tagPath)))
            ->flatten()
            ->unique()
            ->values();
        $allPublisherNames = $collection->pluck('publisher')->pluck('name')->unique()->filter()->values();

        $existingAuthors = Author::whereIn('name', $allAuthorNames)->get()->keyBy('name');
        $existingTags = Tag::whereIn('name', $allTagNames)->get()->keyBy('name');
        $existingPublishers = Publisher::whereIn('name', $allPublisherNames)->get()->keyBy('name');

        // Dispatch individual import jobs
        $collection->each(function ($book) use ($existingAuthors, $existingTags, $existingPublishers) {
            ImportBookFromData::dispatch($book, false, [
                'authors' => $existingAuthors,
                'tags' => $existingTags,
                'publishers' => $existingPublishers,
            ]);
        });
    }
}

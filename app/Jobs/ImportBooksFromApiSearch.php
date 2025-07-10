<?php

namespace App\Jobs;

use App\Models\Tag;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Collection;
use App\Actions\Books\ImportBookFromData;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportBooksFromApiSearch implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Collection $books) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $incomingIds = $this->books->pluck('identifier')->filter()->unique();
        $existingIds = Book::whereIn('identifier', $incomingIds)->pluck('identifier');
        $collection = $this->books->whereNotIn('identifier', $existingIds)->values();

        if (! empty($collection)) {
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
}

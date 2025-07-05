<?php

namespace App\Actions\Books;

use App\Contracts\BookApiServiceInterface;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportBookFromData
{
    use AsAction;

    public function __construct(protected BookApiServiceInterface $booksApi) {}

    public function handle(string $identifier, ?array $data = null, bool $force = false): Book
    {
        if (Book::where('identifier', $identifier)->exists() && ! $force) {
            return Book::where('identifier', $identifier)->first();
        }

        if (empty($data)) {
            $data = $this->booksApi->get($identifier);
            if (empty($data)) {
                //                abort(404, "No data found for identifier: $identifier");
                throw new \Exception("No data found for identifier: $identifier");
            }
        }

        $book = Book::create([
            'identifier' => $identifier,
            'codes' => $data['codes'],
            'page_count' => $data['pageCount'] ?? null,
            'title' => $data['title'],
            'published_date' => $data['published_date'],
            'description' => $data['description'],
            'service' => $data['service'] ?? $this->booksApi::ServiceName,
        ]);

        $primaryCover = $book->covers()->create([
            'is_primary' => true,
        ]);

        if ($data['cover']) {
            try {
                $primaryCover->addMediaFromUrl($data['cover'])
                    ->toMediaCollection('image');

                $book->updateColour();
            } catch (\Exception $e) {
                // Handle the exception if the cover image cannot be fetched
                \Log::error('Failed to fetch cover image for book: '.$identifier, ['error' => $e->getMessage()]);
            }
        }

        if (! empty($data['authors'])) {
            $authorIds = collect($data['authors'])
                ->map(fn ($name) => Author::firstOrCreate(['name' => $name])->id)
                ->toArray();

            $book->authors()->syncWithoutDetaching($authorIds);
        }

        if (! empty($data['categories'])) {
            $categoryNames = collect($data['categories'])
                ->flatMap(fn ($path) => array_map('trim', explode('/', $path)))
                ->unique()
                ->values();

            $existing = Category::whereIn('name', $categoryNames)->get()->keyBy('name');

            $categoryIds = $categoryNames->map(function ($name) use ($existing) {
                return $existing[$name]?->id ?? Category::create(['name' => $name])->id;
            });

            $book->categories()->syncWithoutDetaching($categoryIds);
        }

        if (! empty($data['publisher'])) {
            $publisher = Publisher::firstOrCreate(['name' => $data['publisher']]);
            $book->publisher()->associate($publisher)->save();
        }

        return $book;
    }
}

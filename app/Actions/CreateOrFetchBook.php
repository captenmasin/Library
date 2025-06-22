<?php

namespace App\Actions;

use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Services\GoogleBooksService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateOrFetchBook
{
    use AsAction;

    public function handle(string $identifier)
    {
        $book = FetchBookByIdentifier::run($identifier);

        if ($book) {
            return $book;
        }

        return $this->createBookFromApi($identifier);
    }

    public function asController(Request $request): JsonResponse
    {
        $identifier = $request->get('identifier');

        if (! $identifier) {
            abort(400, 'Identifier is required');
        }

        $book = $this->handle($identifier);

        return response()->json([
            'book' => new BookResource($book),
        ]);
    }

    protected function createBookFromApi(string $identifier): Book
    {
        $data = GoogleBooksService::get($identifier);
        abort_if(! $data, 404, 'Book not found from API');

        $book = Book::create([
            'identifier' => $identifier,
            'codes' => $data['codes'],
            'page_count' => $data['pageCount'] ?? null,
            'title' => $data['title'],
            'published_date' => $data['publishedDate'],
            'description' => $data['description'],
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

        //        if (! empty($data['authors'])) {
        //            foreach ($data['authors'] as $name) {
        //                if (! Author::where('name', $name)->exists()) {
        //                    $author = Author::create(['name' => $name]);
        //                } else {
        //                    $author = Author::where('name', $name)->first();
        //                }
        //
        //                $book->authors()->attach($author);
        //            }
        //        }

        // Categories
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

        //        if (! empty($data['categories'])) {
        //            $categoryNames = collect($data['categories'])
        //                ->flatMap(fn ($fullPath) => array_map('trim', explode('/', $fullPath)))
        //                ->unique()
        //                ->values();
        //
        //            $existing = Category::whereIn('name', $categoryNames)->get()->keyBy('name');
        //
        //            $categoryIds = [];
        //
        //            foreach ($categoryNames as $name) {
        //                if ($existing->has($name)) {
        //                    $category = $existing->get($name);
        //                } else {
        //                    $category = Category::create(['name' => $name]);
        //                    $existing->put($name, $category); // prevent re-querying
        //                }
        //
        //                $categoryIds[] = $category->id;
        //            }
        //
        //            // Attach without duplicates
        //            $book->categories()->syncWithoutDetaching($categoryIds);
        //        }

        if (! empty($data['publisher'])) {
            $publisher = Publisher::firstOrCreate(['name' => $data['publisher']]);
            $book->publisher()->associate($publisher)->save();
        }

        //        if (! empty($data['publisher'])) {
        //            $publisherName = $data['publisher'];
        //            if (! Publisher::where('name', $publisherName)->exists()) {
        //                $publisher = Publisher::create(['name' => $publisherName]);
        //            } else {
        //                $publisher = Publisher::where('name', $publisherName)->first();
        //            }
        //
        //            $book->publisher()->associate($publisher);
        //        }

        return $book;
    }
}

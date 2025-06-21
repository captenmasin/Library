<?php

namespace App\Actions;

use App\Models\Author;
use App\Models\Book;
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
            'data' => $book,
        ]);
    }

    protected function createBookFromApi(string $identifier): Book
    {
        $data = GoogleBooksService::get($identifier);
        abort_if(! $data, 404, 'Book not found from API');

        $book = Book::create([
            'identifier' => $identifier,
            'codes' => $data['codes'],
            'title' => $data['title'],
            'published_date' => $data['publishedDate'],
            'description' => $data['description'],
        ]);

        $primaryCover = $book->covers()->create([
            'is_primary' => true,
        ]);

        try {
            $primaryCover->addMediaFromUrl($data['cover'])
                ->toMediaCollection('image');

            $book->updateColour();
        } catch (\Exception $e) {
            // Handle the exception if the cover image cannot be fetched
            \Log::error('Failed to fetch cover image for book: '.$identifier, ['error' => $e->getMessage()]);
        }

        if (! empty($data['authors'])) {
            foreach ($data['authors'] as $name) {
                if (! Author::where('name', $name)->exists()) {
                    $author = Author::create(['name' => $name]);
                } else {
                    $author = Author::where('name', $name)->first();
                }

                $book->authors()->attach($author);
            }
        }

        if (! empty($data['publisher'])) {
            $publisherName = $data['publisher'];
            if (! Publisher::where('name', $publisherName)->exists()) {
                $publisher = Publisher::create(['name' => $publisherName]);
            } else {
                $publisher = Publisher::where('name', $publisherName)->first();
            }

            $book->publisher()->associate($publisher);
        }

        return $book;
    }
}

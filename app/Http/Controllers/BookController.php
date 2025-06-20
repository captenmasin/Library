<?php

namespace App\Http\Controllers;

use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Services\BooksApiService;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = Auth::user()->books()->with(['authors', 'reviews'])->get();
        $books = $books->sortByDesc('id');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $books = $books->filter(function ($book) use ($search) {
                return str_contains(strtolower($book->title), strtolower($search)) ||
                    str_contains(strtolower(implode(', ', $book->authors->pluck('name')->toArray())), strtolower($search)) ||
                    str_contains(strtolower($book->identifier), strtolower($search));
            });
        }

        if ($request->filled('sort')) {
            $sort = $request->get('sort');
            $direction = $request->get('order', 'asc');

            $desc = $direction === 'desc';

            if ($sort === 'title') {
                $books = $books->sortBy(function ($book) {
                    return strtolower($book->title);
                }, SORT_NATURAL | SORT_FLAG_CASE, $desc);
            } elseif ($sort === 'rating') {
                $books = $books->sortBy(function ($book) {
                    return $book->reviews->avg('rating') ?? 0;
                }, SORT_REGULAR, $desc);
            } elseif ($sort === 'published_date') {
                $books = $books->sortBy('published_date', SORT_REGULAR, $desc);
            }
        }

        if ($request->filled('author')) {
            $authorId = $request->get('author');
            $books = $books->filter(function ($book) use ($authorId) {
                return $book->authors->contains('uuid', $authorId);
            });
        }

        return Inertia::render('books/Index', [
            'books' => BookResource::collection($books->values()),
            'authors' => AuthorResource::collection(Auth::user()->books->flatMap(function ($book) {
                return $book->authors;
            })->unique('uuid')),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $identifier = $request->get('identifier');

        $query = Book::where('identifier', $identifier)->whereNotNull('identifier');

        $book = $query->firstOr(function () use ($identifier) {
            $data = (new BooksApiService)->getById($identifier);

            if (! $data) {
                return redirect()->route('books.index');
                //                    ->dangerBanner('Book cannot be found via barcode');
            }

            if (! empty($data)) {
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

                $primaryCover->addMediaFromUrl($data['cover'])
                    ->toMediaCollection('image');

                $book->updateColour();

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

            return null;
        });

        if ($book && ! Auth::user()->books->contains($book)) {
            Auth::user()->books()->attach($book);

            return redirect()->route('books.index');
            //                ->banner('Book '.$book?->code.' added successfully');
        } else {
            return redirect()->route('books.index');
            //                ->dangerBanner('Book '.$book?->code.' has already been added');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $book->load([
            'reviews',
            'notes' => function ($query) {
                $query->where('user_id', Auth::id());
            },
        ]);

        $averageRating = round($book->reviews->avg('rating'), 1);
        $reviewCount = $book->reviews->count();

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
            'reviews' => $book->reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        if ($request->has('notes')) {
            $notes = $request->get('notes');
            $request->user()->notes()->updateOrCreate(
                ['book_id' => $book->id],
                ['title' => 'test', 'content' => $notes]
            );
        }
    }

    public function toggleRead(Request $request, Book $book)
    {
        $user = Auth::user();

        $isRead = $user->books()
            ->where('book_id', $book->id)
            ->wherePivot('read_at', '!=', null)
            ->exists();

        if ($isRead) {
            $user->books()->updateExistingPivot($book->id, ['read_at' => null]);
            $message = 'marked as unread';
        } else {
            $user->books()->updateExistingPivot($book->id, ['read_at' => now()]);
            $message = 'marked as read';
        }

        return redirect()->back();
        //            ->banner("Book {$book->code} has been {$message}.");
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('books.index');
        //            ->banner('Book '.$book->code.' removed successfully');
    }
}

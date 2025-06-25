<?php

namespace App\Http\Controllers;

use App\Actions\Books\FetchOrCreateBook;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReviewResource;
use App\Models\Book;
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
        $books = Auth::user()->books()->with(['authors', 'reviews', 'publisher', 'covers'])->get();
        $books = $books->sortByDesc('id');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $books = $books->filter(function ($book) use ($search) {
                return str_contains(strtolower($book->title), strtolower($search)) ||
                    str_contains(strtolower($book->description), strtolower($search)) ||
                    str_contains(strtolower(implode(', ', $book->authors->pluck('name')->toArray())), strtolower($search)) ||
                    str_contains(strtolower($book->identifier), strtolower($search));
            });
        }

        $sort = $request->get('sort', 'id');
        if (! in_array($sort, ['title', 'rating', 'published_date'])) {
            $sort = 'id';
        }

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
        } elseif ($sort === 'id') {
            $books = $books->sortBy('id', SORT_REGULAR, $desc);
        }

        if ($request->filled('author')) {
            $authorUuid = $request->get('author');
            $books = $books->filter(function ($book) use ($authorUuid) {
                return $book->authors->contains('uuid', $authorUuid);
            });
        }

        if ($request->filled('publisher')) {
            $publisherUuid = $request->get('publisher');
            $books = $books->filter(function ($book) use ($publisherUuid) {
                return $book->publisher->uuid === $publisherUuid;
            });
        }

        return Inertia::render('books/Index', [
            'books' => BookResource::collection($books->values()),
            'authors' => AuthorResource::collection(Auth::user()->books()->with('authors')->get()
                ->flatMap(fn ($book) => $book->authors)->unique('uuid')),
            'publishers' => Auth::user()->books()->with('publisher')->get()
                ->map(fn ($book) => $book->publisher)->unique('uuid'),
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
        $identifier = $request->string('identifier');
        FetchOrCreateBook::run($identifier);

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['authors', 'reviews', 'notes', 'covers']);

        return Inertia::render('books/Show', [
            'book' => new BookResource($book),
            'averageRating' => round($book->reviews->avg('rating'), 1),
            'reviews' => Inertia::defer(fn () => ReviewResource::collection($book->reviews)),
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

        return redirect()->back()
            ->with('success', 'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        Auth::user()->books()->detach($book);

        return redirect()->route('books.index');
        //            ->banner('Book '.$book->code.' removed successfully');
    }
}

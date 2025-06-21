<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrFetchBook;
use App\Enums\UserBookStatus;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
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
        $identifier = $request->string('identifier');
        CreateOrFetchBook::run($identifier);

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Book $book)
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
            'userBookStatuses' => UserBookStatus::options(),
            'initialUserBookStatus' => Auth::user()->books()->where('book_id', $book->id)->first()?->pivot?->status,
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

        if ($request->has('status')) {
            $status = $request->get('status');
            Auth::user()->books()->updateExistingPivot($book, ['status' => $status]);
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

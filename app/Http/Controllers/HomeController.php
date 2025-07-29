<?php

namespace App\Http\Controllers;

use Number;
use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Enums\UserBookStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TagResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\ActivityResource;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $books = $request->user()->books()
            ->with(['covers', 'authors', 'tags', 'ratings'])
            ->withPivot('status')
            ->get();

        $booksByStatus = $books->groupBy(fn ($book) => $book->pivot->status);

        $topTagNames = $books->flatMap(fn ($book) => $book->tags->pluck('name'))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(10);

        $tags = Tag::whereIn('name', $topTagNames)
            ->get()->sortBy(fn ($tag) => $topTagNames->search($tag->name))->values();

        $authors = Author::query()
            ->select('authors.*', DB::raw('count(*) as book_count'))
            ->join('author_book', 'authors.id', '=', 'author_book.author_id')
            ->join('book_user', 'author_book.book_id', '=', 'book_user.book_id')
            ->where('book_user.user_id', $request->user()->id)
            ->where('book_user.status', UserBookStatus::Completed->value)
            ->groupBy('authors.id')
            ->orderByDesc('book_count')
            ->limit(5)
            ->get();

        $currentlyReading = collect([
            ...$booksByStatus[UserBookStatus::Reading->value] ?? [],
            ...$booksByStatus[UserBookStatus::OnHold->value] ?? [],
            ...$booksByStatus[UserBookStatus::Dropped->value] ?? [],
        ])->take(4);

        $completedBooks = $booksByStatus[UserBookStatus::Completed->value] ?? collect();
        $planToReadBooks = $booksByStatus[UserBookStatus::PlanToRead->value] ?? collect();
        $readingBooks = $booksByStatus[UserBookStatus::Reading->value] ?? collect();

        return Inertia::render('Home', [
            'statValues' => [
                'booksInLibrary' => $books->count(),
                'completedBooks' => $completedBooks->count() ?? 0,
                'readingBooks' => $readingBooks->count() ?? 0,
                //                'pagesRead' => Number::format($completedBooks->sum('page_count')) ?? 0,
                'planToRead' => $planToReadBooks->count() ?? 0,
            ],
            'currentlyReading' => BookResource::collection(
                $currentlyReading
            ),
            'activities' => ActivityResource::collection(
                $request->user()->activities->load('subject')->sortBy('id')->take(5)
            ),
            'tags' => TagResource::collection(
                $tags
            ),
            'authors' => AuthorResource::collection(
                $authors
            ),
            'breadcrumbs' => [
                ['title' => 'Home', 'href' => route('home')],
            ],
        ])->withMeta([
            'title' => 'Home',
            'description' => 'Your personal dashboard.',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Number;
use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Enums\UserBookStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TagResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\ActivityResource;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request): InertiaResponse|JsonResponse
    {
        $request->user()->load('activities');

        $books = $request->user()->books()
            ->with(['authors', 'tags'])
            ->withPivot('status')
            ->get();

        //        dd(BookResource::collection(
        //            $books
        //        )->toArray($request));

        $books = $books->sortByDesc(fn ($book) => $book->pivot->created_at)
            ->values();

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
            ->where('book_user.status', UserBookStatus::Read->value)
            ->groupBy('authors.id')
            ->orderByDesc('book_count')
            ->limit(5)
            ->get();

        $currentlyReading = collect([
            ...$booksByStatus[UserBookStatus::Reading->value] ?? [],
            //            ...$booksByStatus[UserBookStatus::OnHold->value] ?? [],
            //            ...$booksByStatus[UserBookStatus::Dropped->value] ?? [],
        ])->take(4);

        $completedBooks = $booksByStatus[UserBookStatus::Read->value] ?? collect();
        $planToReadBooks = $booksByStatus[UserBookStatus::PlanToRead->value] ?? collect();
        $readingBooks = $booksByStatus[UserBookStatus::Reading->value] ?? collect();

        $stats = [
            'books_in_library' => $books->count(),
            'completed_books' => $completedBooks->count(),
            'reading_books' => $readingBooks->count(),
            'plan_to_read' => $planToReadBooks->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'stats' => $stats,
                'currently_reading' => BookResource::collection($currentlyReading)->resolve($request),
                'activities' => ActivityResource::collection(
                    $request->user()->activities->sortByDesc('id')->take(5)
                )->resolve($request),
                'tags' => TagResource::collection($tags)->resolve($request),
                'authors' => AuthorResource::collection($authors)->resolve($request),
            ]);
        }

        return Inertia::render('Dashboard', [
            'statValues' => [
                'booksInLibrary' => $stats['books_in_library'],
                'completedBooks' => $stats['completed_books'],
                'readingBooks' => $stats['reading_books'],
                //                'pagesRead' => Number::format($completedBooks->sum('page_count')) ?? 0,
                'planToRead' => $stats['plan_to_read'],
            ],
            'currentlyReading' => BookResource::collection(
                $currentlyReading
            ),
            'activities' => ActivityResource::collection(
                $request->user()->activities->sortByDesc('id')->take(5)
            ),
            'tags' => TagResource::collection(
                $tags
            ),
            'authors' => AuthorResource::collection(
                $authors
            ),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
            ],
        ])->withMeta([
            'title' => 'Dashboard',
        ]);
    }
}

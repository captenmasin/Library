<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Inertia\Inertia;
use App\Models\Review;
use App\Actions\TrackEvent;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\ReviewResource;
use Inertia\Response as InertiaResponse;
use App\Http\Requests\DestroyReviewRequest;
use App\Actions\Books\GetPublicBookPageData;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    public function __construct(private GetPublicBookPageData $publicBookPageData) {}

    public function index(Request $request): InertiaResponse|AnonymousResourceCollection
    {
        $reviews = $request->user()->reviews()
            ->with(['book.authors', 'book.covers', 'book.ratings', 'user'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson()) {
            return ReviewResource::collection($reviews);
        }

        return Inertia::render('user/Reviews', [
            'reviews' => ReviewResource::collection($reviews),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Reviews', 'href' => route('user.reviews.index')],
            ],
        ])->withMeta([
            'title' => 'Reviews',
            'description' => 'A list of your reviews on books.',
        ]);
    }

    public function store(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:256',
            'content' => 'nullable|string|max:2000',
        ]);

        $existing = Review::where('book_id', $book->id)
            ->where('user_id', $request->user()->id)
            ->first();

        $review = Review::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => $request->user()->id],
            [
                'content' => $validated['content'],
                'title' => $validated['title'],
            ]
        );

        TrackEvent::dispatchAfterResponse(
            $existing ? AnalyticsEvent::BookReviewUpdated : AnalyticsEvent::BookReviewAdded,
            [
                'user_id' => $request->user()?->id,
                'book' => [
                    'book_identifier' => $book->identifier,
                    'book_title' => $book->title,
                    'review_title' => $review->title,
                    'review_content' => $review->content,
                ],
            ]);

        $request->user()->logActivity(
            $existing ? ActivityType::BookReviewUpdated : ActivityType::BookReviewAdded,
            $review,
            [
                'title' => $review->title,
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        $this->publicBookPageData->forget($book);

        return $request->wantsJson()
            ? (new ReviewResource($review->load('user')))->response()->setStatusCode($existing ? 200 : 201)
            : back()->with('success', 'Review saved.');
    }

    public function destroy(DestroyReviewRequest $request, Book $book, Review $review): Response|RedirectResponse
    {
        $review->delete();

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookReviewRemoved, [
            'user_id' => $request->user()?->id,
            'book' => [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ],
        ]);

        $request->user()->logActivity(
            ActivityType::BookReviewRemoved,
            null,
            [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        $this->publicBookPageData->forget($book);

        return $request->wantsJson()
            ? response()->noContent()
            : back()->with('success', 'Review deleted.');
    }
}

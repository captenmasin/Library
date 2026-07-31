<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use App\Actions\TrackEvent;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\RatingResource;
use App\Actions\Books\GetPublicBookPageData;
use App\Http\Requests\Ratings\StoreRatingRequest;
use App\Http\Requests\Ratings\UpdateRatingRequest;
use App\Http\Requests\Ratings\DestroyRatingRequest;

class RatingController extends Controller
{
    public function __construct(private GetPublicBookPageData $publicBookPageData) {}

    public function store(StoreRatingRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        $rating = $book->ratings()
            ->create([
                'value' => $request->integer('rating.value'),
                'user_id' => $request->user()->id,
            ]);

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookRatingAdded, [
            'user_id' => $request->user()?->id,
            'book' => [
                'rating_value' => $request->integer('rating.value'),
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ],
        ]);

        $this->publicBookPageData->forget($book);

        return $request->wantsJson()
            ? (new RatingResource($rating))->response()->setStatusCode(201)
            : redirect()->back()->with('success', 'Rating added successfully.');
    }

    public function update(UpdateRatingRequest $request, Book $book, Rating $rating): JsonResponse|RedirectResponse
    {
        $rating->update([
            'value' => $request->integer('rating.value'),
        ]);

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookRatingUpdated, [
            'user_id' => $request->user()?->id,
            'book' => [
                'rating_value' => $request->integer('rating.value'),
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ],
        ]);

        $this->publicBookPageData->forget($book);

        return $request->wantsJson()
            ? (new RatingResource($rating))->response()
            : redirect()->back()->with('success', 'Rating updated successfully.');
    }

    public function destroy(DestroyRatingRequest $request, Book $book, Rating $rating): Response|RedirectResponse
    {
        $rating->forceDelete();

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookRatingRemoved, [
            'user_id' => $request->user()?->id,
            'book' => [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ],
        ]);

        $this->publicBookPageData->forget($book);

        return $request->wantsJson()
            ? response()->noContent()
            : redirect()->back()->with('success', 'Rating deleted successfully.');
    }
}

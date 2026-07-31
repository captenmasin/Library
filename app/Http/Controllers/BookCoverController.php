<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Actions\TrackEvent;
use App\Enums\ActivityType;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Support\SubscriptionLimits;
use App\Http\Resources\BookResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateBookCoverRequest;

class BookCoverController extends Controller
{
    public function update(UpdateBookCoverRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        if (! SubscriptionLimits::allowCustomCovers($request->user())) {
            $message = 'Your current plan does not allow adding custom covers.';

            return $request->wantsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        // Validate via dedicated Form Request (sends errors to 'bookCoverBag')
        $request->validated();

        $created = false;

        if ($request->file('cover')) {
            $newCover = $book->covers()->create([
                'user_id' => $request->user()->id,
            ]);

            $newCover->addMedia($request->file('cover'))->toMediaCollection('image');
            $created = true;

            TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookCoverUpdated, [
                'user_id' => $request->user()?->id,
                'book' => [
                    'book_identifier' => $book->identifier,
                    'book_title' => $book->title,
                ],
            ]);

            $request->user()->logActivity(
                ActivityType::BookCoverUpdated,
                $newCover,
                [
                    'book_identifier' => $book->identifier,
                    'book_title' => $book->title,
                ]
            );
        }

        return $request->wantsJson()
            ? (new BookResource($book))->response()->setStatusCode($created ? 201 : 200)
            : redirect()->back()->with('success', __('Book cover updated successfully.'));
    }

    public function destroy(UpdateBookCoverRequest $request, Book $book): ?Response
    {
        TrackEvent::dispatchAfterResponse(AnalyticsEvent::BookCoverRemoved, [
            'user_id' => $request->user()?->id,
            'book' => [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ],
        ]);

        $request->user()->logActivity(
            ActivityType::BookCoverRemoved,
            null,
            [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        $request->user()->book_covers()->where('book_id', $book->id)->delete();

        return $request->wantsJson() ? response()->noContent() : null;
    }
}

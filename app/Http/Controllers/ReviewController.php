<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DestroyReviewRequest;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:0|max:5',
            'title' => 'nullable|string|max:256',
            'content' => 'nullable|string|max:2000',
        ]);

        $existing = Review::where('book_id', $book->id)
            ->where('user_id', Auth::id())
            ->first();

        $review = Review::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => Auth::id()],
            [
                'rating' => $validated['rating'],
                'content' => $validated['content'],
                'title' => $validated['title'],
            ]
        );

        logActivity(
            $existing ? ActivityType::BookReviewUpdated : ActivityType::BookReviewAdded,
            $review,
            [
                'rating' => $review->rating,
                'title' => $review->title,
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        return back()->with('success', 'Review saved.');
    }

    public function destroy(DestroyReviewRequest $request, Book $book, Review $review)
    {
        $review->delete();

        logActivity(
            ActivityType::BookReviewRemoved,
            null,
            [
                'book_identifier' => $book->identifier,
                'book_title' => $book->title,
            ]
        );

        return back()->with('success', 'Review deleted.');
    }
}

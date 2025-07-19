<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use App\Http\Requests\DestroyReviewRequest;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        dd($request->user()->reviews()->with('book')->get());
    }

    public function store(Request $request, Book $book)
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

        $request->user()->logActivity(
            $existing ? ActivityType::BookReviewUpdated : ActivityType::BookReviewAdded,
            $review,
            [
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

        $request->user()->logActivity(
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

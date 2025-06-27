<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:0|max:5',
            'title' => 'nullable|string|max:256',
            'content' => 'nullable|string|max:2000',
        ]);

        Review::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => Auth::id()],
            ['rating' => $validated['rating'], 'content' => $validated['content'], 'title' => $validated['title']]
        );

        return back()->with('success', 'Review saved.');
    }

    public function destroy(DestroyReviewRequest $request, Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}

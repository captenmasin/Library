<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:2000',
        ]);

        Review::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => Auth::id()],
            ['rating' => $validated['rating'], 'content' => $validated['content']]
        );

        return back()->with('success', 'Review saved.');
    }
}

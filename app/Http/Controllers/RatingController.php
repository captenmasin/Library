<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use App\Http\Requests\Ratings\StoreRatingRequest;
use App\Http\Requests\Ratings\UpdateRatingRequest;
use App\Http\Requests\Ratings\DestroyRatingRequest;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Book $book)
    {
        $book->ratings()
            ->create([
                'value' => $request->integer('rating.value'),
                'user_id' => $request->user()->id,
            ]);

        return redirect()->back()
            ->with('success', 'Rating added successfully.');
    }

    public function update(UpdateRatingRequest $request, Book $book, Rating $rating)
    {
        $rating->update([
            'value' => $request->integer('rating.value'),
        ]);

        return redirect()->back()
            ->with('success', 'Rating updated successfully.');
    }

    public function destroy(DestroyRatingRequest $request, Book $book, Rating $rating)
    {
        $rating->forceDelete();

        return redirect()->back()
            ->with('success', 'Rating deleted successfully.');
    }
}

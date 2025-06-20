<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class BookCoverController extends Controller
{
    public function update(Request $request, Book $book)
    {
        Validator::make($request->all(), [
            'cover' => ['nullable', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],
        ])->validateWithBag('updateBookInformation');

        if ($request->file('cover')) {
            $newCover = $book->covers()->create([
                'user_id' => $request->user()->id,
            ]);

            $newCover->addMedia($request->file('cover'))->toMediaCollection('image');
        }
    }

    public function destroy(Request $request, Book $book)
    {
        $request->user()->book_covers()->where('book_id', $book->id)->delete();
    }
}

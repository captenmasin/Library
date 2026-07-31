<?php

namespace App\Http\Requests\Ratings;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');
        $rating = $this->route('rating');
        $user = $this->user();

        return $book instanceof Book
            && $rating instanceof Rating
            && $user !== null
            && $rating->book_id === $book->id
            && $rating->user_id === $user->id
            && $user->books()->whereKey($book->id)->exists();
    }

    public function rules(): array
    {
        return [];
    }
}

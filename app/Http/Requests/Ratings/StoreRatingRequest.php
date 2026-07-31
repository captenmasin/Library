<?php

namespace App\Http\Requests\Ratings;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');

        return $book instanceof Book
            && $this->user() !== null
            && $this->user()->books()->whereKey($book->id)->exists();
    }

    public function rules(): array
    {
        return [
            'rating.value' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }
}

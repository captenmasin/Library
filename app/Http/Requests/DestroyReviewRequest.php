<?php

namespace App\Http\Requests;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class DestroyReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $book = $this->route('book');
        $review = $this->route('review');
        $user = $this->user();

        return $book instanceof Book
            && $review instanceof Review
            && $user !== null
            && $review->book_id === $book->id
            && $review->user_id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}

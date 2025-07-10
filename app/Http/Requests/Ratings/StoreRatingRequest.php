<?php

namespace App\Http\Requests\Ratings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && $this->user()->books()->whereKey($this->book->id)->exists();
    }

    public function rules(): array
    {
        return [
            'rating.value' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }
}

<?php

namespace App\Http\Requests\Ratings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check()
            && $this->user()->ratings()->whereKey($this->rating->id)->exists()
            && $this->user()->books()->whereKey($this->book->id)->exists();
    }

    public function rules(): array
    {
        return [
        ];
    }
}

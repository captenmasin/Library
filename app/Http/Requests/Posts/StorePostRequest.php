<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:20480'], // Optional image, max size 20MB
            'book_identifier' => ['nullable', 'sometimes', 'exists:books,identifier'], // Ensure the book exists
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Book;
use App\Models\Note;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class DestroyNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $book = $this->route('book');
        $note = $this->route('note');
        $user = $this->user();

        return $book instanceof Book
            && $note instanceof Note
            && $user !== null
            && $note->book_id === $book->id
            && $note->user_id === $user->id;
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

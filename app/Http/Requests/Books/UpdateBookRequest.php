<?php

namespace App\Http\Requests\Books;

use App\Enums\UserBookStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'nullable', 'string', 'in:'.implode(',', UserBookStatus::names())],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        dd($validator->errors(), implode(',', UserBookStatus::names()));
    }
}

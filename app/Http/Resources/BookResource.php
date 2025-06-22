<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'path' => $this->path,
            'identifier' => $this->identifier,
            'title' => $this->title,
            //            'codes' => $this->codes,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'description' => $this->description,

            'cover' => $request->user() ? $this->getCover($request->user()) : null,
            'has_custom_cover' => $request->user() ?
                $request->user()->book_covers()->where('book_id', $this->id)->exists()
            : false,

            'notes' => $this->whenLoaded('notes', function () use ($request) {
                return $this->notes->where('user_id', $request->user()->id)->first();
            }),
            'user_review' => $this->whenLoaded('reviews', function () use ($request) {
                return $this->reviews->where('user_id', $request->user()->id)->first();
            }),
            'colour' => $this->settings()->get('colour', '#000000'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'show' => route('books.show', $this),
            ],
        ];
    }

    public function getCover(User $user)
    {
        $hasCustomCover = $user->book_covers()->where('book_id', $this->id)->exists();
        if ($hasCustomCover) {
            $hasCustomCoverImage = $user->book_covers()->where('book_id', $this->id)->first()->hasMedia('image');
            if ($hasCustomCoverImage) {
                return $user->book_covers()->where('book_id', $this->id)->first()->image;
            }
        }

        return $this->primary_cover;
    }
}

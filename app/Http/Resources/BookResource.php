<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'path' => $this->path,
            'identifier' => $this->identifier,
            'title' => $this->title,
            'authors' => $this->whenLoaded('authors', fn () => AuthorResource::collection($this->whenLoaded('authors'))),
            'description' => $this->description,

            'cover' => $this->whenLoaded('covers', fn () => $this->getCover($user)),
            'has_custom_cover' => $user ? $this->hasCustomCover($user) : false,

            'notes' => $this->whenLoaded('notes', fn () => $this->notes->firstWhere('user_id', $user?->id)),
            'user_review' => $this->whenLoaded('reviews', fn () => $this->getUserReview($user)),
            'in_library' => $user && $this->isInLibrary($user),
            'user_status' => $user ? $this->getUserStatus($user) : null,

            'colour' => $this->settings()->get('colour', '#000000'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'links' => [
                'show' => route('books.show', $this),
            ],
        ];
    }

    protected function getCover(User $user): ?string
    {
        $cover = $user->book_covers->where('book_id', $this->id)->first();

        return $cover?->hasMedia('image')
            ? $cover->image
            : $this->primary_cover;
    }

    protected function hasCustomCover(User $user): bool
    {
        return $user->book_covers->contains('book_id', $this->id);
    }

    protected function isInLibrary(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    protected function getUserStatus(User $user): ?string
    {
        return $this->users()->where('user_id', $user->id)->first()?->pivot?->status;
    }

    protected function getUserReview(User $user): ?ReviewResource
    {
        $review = $this->reviews->firstWhere('user_id', $user?->id);

        return $review ? new ReviewResource($review) : null;
    }
}

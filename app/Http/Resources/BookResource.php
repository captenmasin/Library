<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
            'description' => $this->description,
            'description_clean' => strip_tags($this->description),
            'published_date' => $this->published_date,
            'categories' => $this->whenLoaded('categories', fn () => $this->categories->pluck('name')->toArray()),
            'page_count' => $this->page_count,

            'has_custom_cover' => $user ? $this->hasCustomCover($user) : false,
            'cover' => $this->whenLoaded('covers', fn () => $this->getCover($user)),
            'authors' => $this->whenLoaded('authors', fn () => $this->getAuthors()),
            'publisher' => $this->whenLoaded('publisher', fn () => $this->getPublisher()),
            'notes' => $this->whenLoaded('notes', fn () => $this->getNote($user)),
            'user_review' => $this->whenLoaded('reviews', fn () => $this->getUserReview($user)),

            'in_library' => $user && $this->isInLibrary($user),
            'user_status' => $user ? $this->getUserStatus($user) : null,
            'user_tags' => $user ? $this->getUserTags($user) : [],

            'colour' => $this->settings()->get('colour', '#000000'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'links' => [
                'show' => route('books.show', $this),
            ],
        ];
    }

    protected function getAuthors(): AnonymousResourceCollection
    {
        return AuthorResource::collection($this->authors);
    }

    protected function getPublisher(): PublisherResource
    {
        return new PublisherResource($this->publisher);
    }

    protected function getCover(?User $user = null): ?string
    {
        if (! $user) {
            return $this->primary_cover;
        }

        $cover = $user->book_covers->where('book_id', $this->id)->last();

        return $cover?->hasMedia('image')
            ? $cover->image
            : $this->primary_cover;
    }

    protected function hasCustomCover(?User $user = null): bool
    {
        return $user->book_covers->contains('book_id', $this->id);
    }

    protected function getUserPivot(User $user)
    {
        if ($this->relationLoaded('users')) {
            return $this->users->firstWhere('id', $user->id)?->pivot;
        }

        return $this->users()
            ->where('user_id', $user->id)
            ->first()?->pivot;
    }

    protected function isInLibrary(User $user): bool
    {
        return (bool) $this->getUserPivot($user);
    }

    protected function getUserStatus(User $user): ?string
    {
        return $this->getUserPivot($user)?->status;
    }

    protected function getUserTags(User $user): ?array
    {
        return $this->getUserPivot($user)?->tags;
    }

    protected function getUserReview(?User $user = null): ?ReviewResource
    {
        if (! $user) {
            return null;
        }

        $review = $this->reviews->firstWhere('user_id', $user?->id);

        return $review ? new ReviewResource($review) : null;
    }

    protected function getNote(?User $user = null): ?NoteResource
    {
        if (! $user) {
            return null;
        }

        $note = $this->notes->firstWhere('user_id', $user?->id);

        return $note ? new NoteResource($note) : null;
    }
}

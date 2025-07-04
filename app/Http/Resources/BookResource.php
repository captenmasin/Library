<?php

namespace App\Http\Resources;

use App\Data\BookData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return BookData::from([
            ...$this->attributesToArray(),
            'categories' => $this->relationLoaded('categories')
                ? $this->categories->pluck('name')->toArray()
                : null,
            'authors' => $this->relationLoaded('authors')
                ? $this->getAuthors()->resolve()
                : null,
            'publisher' => $this->relationLoaded('publisher')
                ? $this->getPublisher()
                : null,
            'cover' => $this->relationLoaded('covers')
                ? $this->getCover($request->user())
                : null,
            'description_clean' => strip_tags($this->description ?? ''),
            'in_library' => $request->user() && $this->isInLibrary($request->user()),
            'user_status' => $request->user() ? $this->getUserStatus($request->user()) : null,
            'user_tags' => $request->user() ? $this->getUserTags($request->user()) : [],
            'has_custom_cover' => $request->user() ? $this->hasCustomCover($request->user()) : false,
            'links' => [
                'show' => route('books.show', $this),
            ],
        ])->toArray();
    }

    protected function getAuthors(): AnonymousResourceCollection
    {
        return AuthorResource::collection($this->authors);
    }

    protected function getPublisher(): ?array
    {
        return $this->publisher ?
            (new PublisherResource($this->publisher))->resolve()
            : null;
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

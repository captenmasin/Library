<?php

namespace App\Observers;

use App\Models\Tag;
use App\Models\Author;
use Illuminate\Support\Str;

class AuthorObserver
{
    public function creating(Author $author): void
    {
        if (! $author->slug) {
            $author->slug = $this->generateSlug($author);
        }
    }

    protected function generateSlug(Author $author): string
    {
        if (Tag::where('slug', $author->slug)->exists()) {
            return Str::slug($author->name).'-'.Str::random(5);
        }

        return Str::slug($author->name);
    }
}

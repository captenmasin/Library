<?php

namespace App\Observers;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagObserver
{
    public function creating(Tag $tag): void
    {
        if (! $tag->slug) {
            $tag->slug = $this->generateSlug($tag);
        }
    }

    protected function generateSlug(Tag $tag): string
    {
        if (Tag::where('slug', $tag->slug)->exists()) {
            return Str::slug($tag->name).'-'.Str::random(5);
        }

        return Str::slug($tag->name);
    }
}

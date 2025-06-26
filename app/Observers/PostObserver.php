<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    public function creating(Post $post): void
    {
        if (! $post->path) {
            $post->path = $this->generatePath($post);
        }
    }

    public function created(Post $post): void
    {
        $post->path = $this->generatePath($post);
        $post->saveQuietly();
    }

    public function updated(Post $post): void
    {
        $post->path = $this->generatePath($post);
        $post->saveQuietly();
    }

    protected function generatePath(Post $post): string
    {
        return Str::slug($post->title).'-'.$post->uuid;
    }
}

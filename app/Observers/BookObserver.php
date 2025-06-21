<?php

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Str;

class BookObserver
{
    public function creating(Book $book): void
    {
        if (! $book->path) {
            $book->path = $this->generatePath($book);
        }
    }

    public function created(Book $book): void
    {
        $book->path = $this->generatePath($book);
        $book->saveQuietly();
    }

    public function updated(Book $book): void
    {
        $book->path = $this->generatePath($book);
        $book->saveQuietly();
    }

    protected function generatePath(Book $book): string
    {
        return Str::slug($book->title).'-'.$book->identifier;
    }
}

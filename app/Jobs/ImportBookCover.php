<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportBookCover implements ShouldQueue
{
    use Queueable;

    public $tries = 25;

    /**
     * Create a new job instance.
     */
    public function __construct(public Book $book, public ?string $coverUrl = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $primaryCover = $this->book->covers()->create(['is_primary' => true]);

            $primaryCover->addMediaFromUrl($this->coverUrl)
                ->toMediaCollection('image');

            $this->book->updateColour();
        } catch (\Exception $e) {
            \Log::error('Failed to fetch cover image for book: '.$this->book->identifier, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}

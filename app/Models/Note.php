<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\NoteFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected static $unguarded = true;

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}

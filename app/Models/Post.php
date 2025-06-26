<?php

namespace App\Models;

use App\Observers\PostObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([PostObserver::class])]
class Post extends Model implements HasMedia
{
    use HasFactory, HasUuid, InteractsWithMedia;

    protected static $unguarded = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function getFeaturedImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured-image');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured-image')->singleFile();
    }
}

<?php

namespace App\Models;

use App\Observers\BookObserver;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Vite;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([BookObserver::class])]
class Book extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory, HasSettingsField, InteractsWithMedia;

    protected static $unguarded = true;

    protected $with = ['authors', 'covers'];

    protected function casts(): array
    {
        return [
            'codes' => 'json',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'path';
    }

    protected static function booted(): void
    {
        self::deleted(static function (Book $book): void {
            $book->covers()->delete();
        });
    }

    public function primaryCover()
    {
        return $this->covers()->where('is_primary', true)->first();
    }

    public function updateColour(): void
    {
        $cover = $this->primaryCover()?->getFirstMedia('image');
        $colour = $cover ? getDominantColour($cover->getPath()) : '#000000';
        $this->settings()->set('colour', $colour);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating(): float|int|null
    {
        return $this->reviews()->avg('rating');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function covers(): HasMany
    {
        return $this->hasMany(Cover::class);
    }

    public function getPrimaryCoverAttribute(): string
    {
        if (! $this->primaryCover()?->hasMedia('image')) {
            return Vite::asset('resources/images/default-cover.svg');
        }

        return $this->primaryCover()->image;
    }
}

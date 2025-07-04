<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Vite;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Cover extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected static $unguarded = true;

    protected $with = ['media'];

    protected function image(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->hasMedia('image') ?
                $this->getFirstMediaUrl('image')
                : Vite::asset('resources/images/default-cover.svg');
        });
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}

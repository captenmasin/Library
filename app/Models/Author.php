<?php

namespace App\Models;

use App\Traits\HasUuid;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory, HasSlug, HasUuid;

    protected static $unguarded = true;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}

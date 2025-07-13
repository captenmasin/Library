<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Observers\AuthorObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([AuthorObserver::class])]
class Author extends Model
{
    use HasFactory, HasUuid;

    protected static $unguarded = true;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}

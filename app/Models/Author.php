<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

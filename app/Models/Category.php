<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected static $unguarded = true;

    public $timestamps = true;

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}

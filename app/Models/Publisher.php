<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publisher extends Model
{
    use HasFactory, HasUuid;

    protected static $unguarded = true;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}

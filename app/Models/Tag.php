<?php

namespace App\Models;

use App\Observers\TagObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([TagObserver::class])]
class Tag extends Model
{
    use HasFactory;

    protected static $unguarded = true;

    public $timestamps = true;

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}

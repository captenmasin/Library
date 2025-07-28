<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreviousSearch extends Model
{
    protected static $unguarded = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

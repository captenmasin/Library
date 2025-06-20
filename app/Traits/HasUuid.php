<?php

namespace App\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $model->uuid = $model->generateHashid();
        });
    }

    protected function uuid(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
        );
    }

    public function generateHashid($padding = 10, $append = null)
    {
        $title = $this::class ?? 'Untitled';
        $uuid = new Hashids($title.($append), $padding)->encode(rand(0, 99999));
        while (self::where('uuid', $uuid)->exists()) {
            $uuid = $this->generateHashid($padding, uniqid());
        }

        return $uuid;
    }
}

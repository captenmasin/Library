<?php

namespace App\Traits;

use Laravel\Jetstream\Features;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasAvatar
{
    public function updateAvatar(UploadedFile $photo): Media
    {
        return $this->addMedia($photo)->toMediaCollection('avatar');
    }

    public function deleteAvatar(): void
    {
        if (! Features::managesProfilePhotos()) {
            return;
        }

        if (! $this->hasMedia('avatar')) {
            return;
        }

        $this->getFirstMedia('avatar')->delete();
    }

    protected function avatar(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->hasMedia('avatar')
                ? $this->getFirstMediaUrl('avatar')
                : $this->defaultAvatar();
        });
    }

    protected function hasAvatar(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->hasMedia('avatar');
        });
    }

    protected function defaultAvatar(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
}

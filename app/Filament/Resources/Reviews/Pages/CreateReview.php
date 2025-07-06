<?php

namespace App\Filament\Resources\Reviews\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Reviews\ReviewResource;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;
}

<?php

namespace App\Filament\Resources\Authors\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Authors\AuthorResource;

class CreateAuthor extends CreateRecord
{
    protected static string $resource = AuthorResource::class;
}

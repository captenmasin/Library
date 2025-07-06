<?php

namespace App\Filament\Resources\Books\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Books\BookResource;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;
}

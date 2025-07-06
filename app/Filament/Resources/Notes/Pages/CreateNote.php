<?php

namespace App\Filament\Resources\Notes\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Notes\NoteResource;

class CreateNote extends CreateRecord
{
    protected static string $resource = NoteResource::class;
}

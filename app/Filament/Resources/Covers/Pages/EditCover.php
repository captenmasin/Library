<?php

namespace App\Filament\Resources\Covers\Pages;

use App\Filament\Resources\Covers\CoverResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCover extends EditRecord
{
    protected static string $resource = CoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

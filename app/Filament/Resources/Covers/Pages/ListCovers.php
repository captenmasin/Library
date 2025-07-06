<?php

namespace App\Filament\Resources\Covers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Covers\CoverResource;

class ListCovers extends ListRecords
{
    protected static string $resource = CoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

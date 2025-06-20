<?php

namespace App\Filament\Resources\Covers\Pages;

use App\Filament\Resources\Covers\CoverResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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

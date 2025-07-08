<?php

namespace App\Filament\Resources\Passkeys\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Passkeys\PasskeyResource;

class ListPasskeys extends ListRecords
{
    protected static string $resource = PasskeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

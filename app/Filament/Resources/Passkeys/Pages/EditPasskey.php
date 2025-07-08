<?php

namespace App\Filament\Resources\Passkeys\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Passkeys\PasskeyResource;

class EditPasskey extends EditRecord
{
    protected static string $resource = PasskeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

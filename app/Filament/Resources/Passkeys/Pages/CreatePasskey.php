<?php

namespace App\Filament\Resources\Passkeys\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Passkeys\PasskeyResource;

class CreatePasskey extends CreateRecord
{
    protected static string $resource = PasskeyResource::class;
}

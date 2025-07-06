<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}

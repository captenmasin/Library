<?php

namespace App\Filament\Resources\Publishers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class PublisherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('uuid')
                    ->disabledOn('edit')
                    ->label('UUID'),
            ]);
    }
}

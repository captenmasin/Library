<?php

namespace App\Filament\Resources\Passkeys\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;

class PasskeyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('authenticatable_id')
                    ->relationship('authenticatable', 'name')
                    ->required(),
                Textarea::make('name')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('credential_id')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('data')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('last_used_at'),
            ]);
    }
}

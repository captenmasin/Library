<?php

namespace App\Filament\Resources\Covers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CoverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Toggle::make('is_primary')
                    ->required(),
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}

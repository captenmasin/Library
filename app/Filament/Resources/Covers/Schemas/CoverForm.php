<?php

namespace App\Filament\Resources\Covers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;

class CoverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Select::make('user_id')
                    ->disabled(fn (Get $get): bool => $get('is_primary') === false)
                    ->relationship('user', 'name'),

                Toggle::make('is_primary')
                    ->disabledOn('edit'),
            ]);
    }
}

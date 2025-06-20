<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('published_date'),
                Textarea::make('settings')
                    ->columnSpanFull(),
                TextInput::make('identifier'),
                Textarea::make('codes')
                    ->columnSpanFull(),
            ]);
    }
}

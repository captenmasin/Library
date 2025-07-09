<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('identifier')
                    ->disabled(),
                TextInput::make('path')
                    ->disabled(),
                TextInput::make('title')
                    ->columnSpanFull()
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('original_cover')
                    ->label('Original Cover URL')
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('page_count')
                    ->numeric(),
                TextInput::make('published_date'),
                Textarea::make('settings'),
                Textarea::make('codes'),
            ]);
    }
}

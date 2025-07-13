<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('identifier')
                    ->disabled(),
                TextInput::make('path')
                    ->disabled(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('original_cover')
                    ->label('Original Cover URL')
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('page_count')
                    ->numeric(),
                TextInput::make('published_date'),
                Textarea::make('settings')
                    ->formatStateUsing(fn ($state) => is_array($state) || is_object($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state),
                Textarea::make('codes')
                    ->formatStateUsing(fn ($state) => is_array($state) || is_object($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state),
                Grid::make(3) // 3 columns
                    ->schema([
                        TextInput::make('edition')
                            ->label('Edition'),

                        TextInput::make('binding')
                            ->label('Binding'),
                        TextInput::make('language'),
                    ])->columnSpanFull(),
            ]);
    }
}

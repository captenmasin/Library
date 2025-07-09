<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->columnSpan(1)
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('rating')
                    ->options(fn () => collect(range(1, 5))->mapWithKeys(fn ($value) => [$value => str_repeat('★', $value)]))
                    ->required(),
                TextInput::make('title'),
                MarkdownEditor::make('content')
                    ->columnSpanFull(),
            ]);
    }
}

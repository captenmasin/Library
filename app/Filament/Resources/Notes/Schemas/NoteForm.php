<?php

namespace App\Filament\Resources\Notes\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;

class NoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('book_id')
                    ->relationship('book', 'title'),
                MarkdownEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

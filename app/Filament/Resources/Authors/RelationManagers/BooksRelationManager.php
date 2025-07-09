<?php

namespace App\Filament\Resources\Authors\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Books\BookResource;
use Filament\Resources\RelationManagers\RelationManager;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';

    protected static ?string $relatedResource = BookResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

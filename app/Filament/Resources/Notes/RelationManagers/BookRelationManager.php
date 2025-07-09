<?php

namespace App\Filament\Resources\Notes\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Books\BookResource;
use Filament\Resources\RelationManagers\RelationManager;

class BookRelationManager extends RelationManager
{
    protected static string $relationship = 'book';

    protected static ?string $relatedResource = BookResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Books\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Authors\AuthorResource;
use Filament\Resources\RelationManagers\RelationManager;

class AuthorsRelationManager extends RelationManager
{
    protected static string $relationship = 'authors';

    protected static ?string $relatedResource = AuthorResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Covers\CoverResource;
use Filament\Resources\RelationManagers\RelationManager;

class CoversRelationManager extends RelationManager
{
    protected static string $relationship = 'book_covers';

    protected static ?string $relatedResource = CoverResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

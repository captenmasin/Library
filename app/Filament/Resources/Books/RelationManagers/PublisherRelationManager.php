<?php

namespace App\Filament\Resources\Books\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Publishers\PublisherResource;
use Filament\Resources\RelationManagers\RelationManager;

class PublisherRelationManager extends RelationManager
{
    protected static string $relationship = 'publisher';

    protected static ?string $relatedResource = PublisherResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

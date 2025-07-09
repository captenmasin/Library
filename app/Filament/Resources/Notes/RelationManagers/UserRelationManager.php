<?php

namespace App\Filament\Resources\Notes\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\RelationManagers\RelationManager;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'user';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

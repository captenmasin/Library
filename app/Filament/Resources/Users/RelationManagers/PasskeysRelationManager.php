<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Passkeys\PasskeyResource;
use Filament\Resources\RelationManagers\RelationManager;

class PasskeysRelationManager extends RelationManager
{
    protected static string $relationship = 'passkeys';

    protected static ?string $relatedResource = PasskeyResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

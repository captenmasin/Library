<?php

namespace App\Filament\Resources\Covers;

use App\Filament\Resources\Covers\Pages\CreateCover;
use App\Filament\Resources\Covers\Pages\EditCover;
use App\Filament\Resources\Covers\Pages\ListCovers;
use App\Filament\Resources\Covers\Schemas\CoverForm;
use App\Filament\Resources\Covers\Tables\CoversTable;
use App\Models\Cover;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CoverResource extends Resource
{
    protected static ?string $model = Cover::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CoverForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCovers::route('/'),
            'create' => CreateCover::route('/create'),
            'edit' => EditCover::route('/{record}/edit'),
        ];
    }
}

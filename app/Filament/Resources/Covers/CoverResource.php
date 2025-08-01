<?php

namespace App\Filament\Resources\Covers;

use BackedEnum;
use App\Models\Cover;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Covers\Pages\EditCover;
use App\Filament\Resources\Covers\Pages\ListCovers;
use App\Filament\Resources\Covers\Pages\CreateCover;
use App\Filament\Resources\Covers\Schemas\CoverForm;
use App\Filament\Resources\Covers\Tables\CoversTable;
use App\Filament\Resources\Covers\RelationManagers\BookRelationManager;

class CoverResource extends Resource
{
    protected static ?string $model = Cover::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function getNavigationBadge(): ?string
    {
        return (string) Number::format(static::$model::query()->count());
    }

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
            BookRelationManager::class,
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

<?php

namespace App\Filament\Resources\Activities;

use BackedEnum;
use App\Models\Activity;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Activities\Pages\EditActivity;
use App\Filament\Resources\Activities\Pages\CreateActivity;
use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Activities\Schemas\ActivityForm;
use App\Filament\Resources\Activities\Tables\ActivitiesTable;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationBadge(): ?string
    {
        return (string) Number::format(static::$model::query()->count());
    }

    public static function form(Schema $schema): Schema
    {
        return ActivityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
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
            'index' => ListActivities::route('/'),
            'create' => CreateActivity::route('/create'),
            //            'edit' => EditActivity::route('/{record}/edit'),
        ];
    }
}

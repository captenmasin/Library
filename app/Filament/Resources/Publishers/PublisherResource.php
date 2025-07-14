<?php

namespace App\Filament\Resources\Publishers;

use BackedEnum;
use App\Models\Publisher;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Publishers\Pages\EditPublisher;
use App\Filament\Resources\Publishers\Pages\ListPublishers;
use App\Filament\Resources\Publishers\Pages\CreatePublisher;
use App\Filament\Resources\Publishers\Schemas\PublisherForm;
use App\Filament\Resources\Publishers\Tables\PublishersTable;
use App\Filament\Resources\Publishers\RelationManagers\BooksRelationManager;

class PublisherResource extends Resource
{
    protected static ?string $model = Publisher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function getNavigationBadge(): ?string
    {
        return (string) Number::format(static::$model::query()->count());
    }

    public static function form(Schema $schema): Schema
    {
        return PublisherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublishersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BooksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublishers::route('/'),
            'create' => CreatePublisher::route('/create'),
            'edit' => EditPublisher::route('/{record}/edit'),
        ];
    }
}

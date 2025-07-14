<?php

namespace App\Filament\Resources\Reviews;

use BackedEnum;
use App\Models\Review;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\Pages\CreateReview;
use App\Filament\Resources\Reviews\Schemas\ReviewForm;
use App\Filament\Resources\Reviews\Tables\ReviewsTable;
use App\Filament\Resources\Reviews\RelationManagers\BookRelationManager;
use App\Filament\Resources\Reviews\RelationManagers\UserRelationManager;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    public static function getNavigationBadge(): ?string
    {
        return (string) Number::format(static::$model::query()->count());
    }

    public static function form(Schema $schema): Schema
    {
        return ReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BookRelationManager::class,
            UserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}

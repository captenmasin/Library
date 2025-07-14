<?php

namespace App\Filament\Resources\Users;

use BackedEnum;
use App\Models\User;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Filament\Resources\Users\RelationManagers\BooksRelationManager;
use App\Filament\Resources\Users\RelationManagers\NotesRelationManager;
use App\Filament\Resources\Users\RelationManagers\CoversRelationManager;
use App\Filament\Resources\Users\RelationManagers\ReviewsRelationManager;
use App\Filament\Resources\Users\RelationManagers\PasskeysRelationManager;
use App\Filament\Resources\Users\RelationManagers\ActivitiesRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    public static function getNavigationBadge(): ?string
    {
        return (string) Number::format(static::$model::query()->count());
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BooksRelationManager::class,
            NotesRelationManager::class,
            ReviewsRelationManager::class,
            CoversRelationManager::class,
            ActivitiesRelationManager::class,
            PasskeysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

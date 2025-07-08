<?php

namespace App\Filament\Resources\Passkeys;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Spatie\LaravelPasskeys\Models\Passkey;
use App\Filament\Resources\Passkeys\Pages\EditPasskey;
use App\Filament\Resources\Passkeys\Pages\ListPasskeys;
use App\Filament\Resources\Passkeys\Pages\CreatePasskey;
use App\Filament\Resources\Passkeys\Schemas\PasskeyForm;
use App\Filament\Resources\Passkeys\Tables\PasskeysTable;

class PasskeyResource extends Resource
{
    protected static ?string $model = Passkey::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PasskeyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PasskeysTable::configure($table);
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
            'index' => ListPasskeys::route('/'),
            'create' => CreatePasskey::route('/create'),
            'edit' => EditPasskey::route('/{record}/edit'),
        ];
    }
}

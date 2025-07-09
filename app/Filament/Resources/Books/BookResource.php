<?php

namespace App\Filament\Resources\Books;

use BackedEnum;
use App\Models\Book;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Books\Pages\EditBook;
use App\Filament\Resources\Books\Pages\ListBooks;
use App\Filament\Resources\Books\Pages\CreateBook;
use App\Filament\Resources\Books\Schemas\BookForm;
use App\Filament\Resources\Books\Tables\BooksTable;
use App\Filament\Resources\Books\RelationManagers\NotesRelationManager;
use App\Filament\Resources\Books\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Books\RelationManagers\CoversRelationManager;
use App\Filament\Resources\Books\RelationManagers\AuthorsRelationManager;
use App\Filament\Resources\Books\RelationManagers\PublisherRelationManager;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function getRecordTitle($record): string
    {
        return $record->title;
    }

    public static function form(Schema $schema): Schema
    {
        return BookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BooksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class,
            PublisherRelationManager::class,
            UsersRelationManager::class,
            CoversRelationManager::class,
            NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBooks::route('/'),
            'create' => CreateBook::route('/create'),
            'edit' => EditBook::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Models\Book;
use App\Models\Note;
use App\Models\Cover;
use App\Models\Review;
use App\Enums\ActivityType;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\MorphToSelect;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('type')
                    ->options(fn () => collect(ActivityType::cases())->mapWithKeys(function ($status) {
                        return [$status->value => $status->name];
                    })->toArray())
                    ->required(),

                MorphToSelect::make('subject')
                    ->types([
                        MorphToSelect\Type::make(Book::class)
                            ->titleAttribute('title'),
                        MorphToSelect\Type::make(Note::class)
                            ->titleAttribute('content'),
                        MorphToSelect\Type::make(Review::class)
                            ->titleAttribute('title'),
                        MorphToSelect\Type::make(Cover::class)
                            ->titleAttribute('book_id'),
                    ])->columnSpanFull(),
                Textarea::make('properties')
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state) => is_array($state) || is_object($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state),
            ]);
    }
}

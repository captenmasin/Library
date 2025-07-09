<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject')
                    ->getStateUsing(function (Model $record): ?string {
                        $record->load('subject');
                        if (! $record->subject) {
                            return null;
                        }

                        return match (get_class($record->subject)) {
                            'App\Models\Book' => 'Book: '.$record->subject->title,
                            'App\Models\Note' => 'Note: '.$record->subject->book->title,
                            'App\Models\Review' => 'Review: '.$record->subject->book->title,
                            'App\Models\Cover' => 'Cover: '.$record->subject->book->title,
                            default => null,
                        };
                    }),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

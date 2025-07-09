<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('book.title')
                    ->limit(32)
                    ->width(250)
                    ->sortable(),
                TextColumn::make('user.name')
                    ->limit(32)
                    ->width(250)
                    ->sortable(),
                TextColumn::make('rating')
                    ->getStateUsing(function (Model $record): string {
                        $output = '';
                        foreach (range(1, $record->rating) as $i) {
                            $output .= '★';
                        }

                        return $record->rating.' '.$output;
                    }),
                TextColumn::make('title')
                    ->sortable(),
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

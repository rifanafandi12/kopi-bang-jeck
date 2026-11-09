<?php

namespace App\Filament\Resources\MenuItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        if (!$record->foto) return null;
                        // Jika URL internet
                        if (filter_var($record->foto, FILTER_VALIDATE_URL)) {
                            return $record->foto;
                        }
                        // Jika file lokal (WebP)
                        return Storage::disk('public')->url($record->foto);
                    }),

                TextColumn::make('nama')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.nama')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                IconColumn::make('tersedia')
                    ->label('Tersedia')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_best_seller')
                    ->label('Terlaris')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

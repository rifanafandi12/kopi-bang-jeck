<?php

namespace App\Filament\Resources\CafeTables;

use App\Filament\Resources\CafeTables\Pages\CreateCafeTable;
use App\Filament\Resources\CafeTables\Pages\EditCafeTable;
use App\Filament\Resources\CafeTables\Pages\ListCafeTables;
use App\Filament\Resources\CafeTables\Schemas\CafeTableForm;
use App\Filament\Resources\CafeTables\Tables\CafeTablesTable;
use App\Models\CafeTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

class CafeTableResource extends Resource
{
    protected static ?string $model = CafeTable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nomor')
                    ->required()
                    ->maxLength(10)
                    ->label('Nomor Meja')
                    ->unique(ignoreRecord: true), // Tidak boleh ada nomor meja ganda

                Toggle::make('aktif')
                    ->default(true)
                    ->label('Meja Aktif'),

                // Token disembunyikan, dibuat otomatis saat meja baru dibuat
                TextInput::make('token')
                    ->default(fn() => (string) Str::ulid())
                    ->disabled()
                    ->dehydrated() // Tetap simpan ke database meskipun disabled
                    ->label('Token QR (Otomatis)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return CafeTablesTable::configure($table);
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
            'index' => ListCafeTables::route('/'),
            'create' => CreateCafeTable::route('/create'),
            'edit' => EditCafeTable::route('/{record}/edit'),
        ];
    }
}

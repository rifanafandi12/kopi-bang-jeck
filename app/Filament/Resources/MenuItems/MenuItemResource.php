<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Filament\Resources\MenuItems\Tables\MenuItemsTable;
use App\Models\MenuItem;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $modelLabel = 'Menu';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Informasi Menu')
                    ->tabs([
                        Tab::make('Data Menu')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'nama')
                                    ->required()
                                    ->label('Kategori'),
                                TextInput::make('nama')
                                    ->required()
                                    ->maxLength(150)
                                    ->label('Nama Menu'),
                                Textarea::make('deskripsi')
                                    ->rows(3)
                                    ->label('Deskripsi'),
                                TextInput::make('harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->label('Harga'),
                                TextInput::make('urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Urutan Tampil'),
                                Toggle::make('tersedia')
                                    ->default(true)
                                    ->label('Tersedia'),
                                Toggle::make('is_best_seller')
                                    ->default(false)
                                    ->label('Tandai sebagai Terlaris'),
                            ]),

                        Tab::make('Foto Menu')
                            ->schema([
                                // 1. FITUR UPLOAD FILE (KONVERSI KE WEBP)
                                FileUpload::make('foto')
                                    ->image()
                                    ->directory('menu-images')
                                    ->label('Upload Gambar (Drag & Drop)')
                                    ->helperText('Tarik gambar ke sini. Sistem otomatis mengonversi ke WebP.')
                                    ->maxSize(5120)
                                    ->columnSpanFull()
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        // Ambil isi file asli
                                        $imageString = file_get_contents($file->getRealPath());

                                        // Muat gambar dengan GD (PHP)
                                        $image = imagecreatefromstring($imageString);

                                        if ($image !== false) {
                                            // Buat nama file WebP unik
                                            $filename = Str::random(40) . '.webp';
                                            $path = 'menu-images/' . $filename;

                                            // Tampung output WebP
                                            ob_start();
                                            imagewebp($image, null, 80); // Kualitas 80%
                                            $webpContent = ob_get_clean();

                                            // Simpan ke storage
                                            Storage::disk('public')->put($path, $webpContent);
                                            imagedestroy($image);

                                            return $path;
                                        }

                                        // Fallback jika gagal konversi (simpan apa adanya)
                                        return $file->store('menu-images', 'public');
                                    }),

                                // 2. FITUR AMBIL DARI URL
                                TextInput::make('foto_url')
                                    ->url()
                                    ->label('Atau Ambil dari URL Google')
                                    ->helperText('Tempelkan link gambar. Kosongkan jika menggunakan upload file.')
                                    ->columnSpanFull()
                                    ->formatStateUsing(
                                        fn ($record) => ($record && filter_var($record->foto, FILTER_VALIDATE_URL)) ? $record->foto : null
                                    )
                                    ->dehydrated(false) // Jangan simpan ke DB
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Jika URL diisi, simpan ke kolom 'foto'
                                        if (filter_var($state, FILTER_VALIDATE_URL)) {
                                            $set('foto', $state);
                                        }
                                        // Jika URL dikosongkan TAPI field 'foto' saat ini berisi URL (bukan file)
                                        elseif (empty($state)) {
                                            $currentFoto = $get('foto');
                                            if ($currentFoto && filter_var($currentFoto, FILTER_VALIDATE_URL)) {
                                                $set('foto', null); // Hapus URL dari DB
                                            }
                                        }
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return MenuItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}

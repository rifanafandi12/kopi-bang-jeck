<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'nama', 'deskripsi', 'harga', 'foto',
        'tersedia', 'is_best_seller', 'urutan',
    ];

    protected $casts = [
        'harga' => 'integer',
        'tersedia' => 'boolean',
        'is_best_seller' => 'boolean',
        'urutan' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // --- TAMBAHKAN KODE INI ---
    protected static function booted()
    {
        static::saving(function ($menuItem) {
            // Jika foto adalah file lokal (bukan URL), dan belum berakhiran .webp
            if ($menuItem->foto && !filter_var($menuItem->foto, FILTER_VALIDATE_URL)) {

                // Cek apakah file ada di storage
                if (Storage::disk('public')->exists($menuItem->foto)) {

                    // Jika bukan .webp, konversi
                    if (pathinfo($menuItem->foto, PATHINFO_EXTENSION) !== 'webp') {
                        $path = $menuItem->foto;
                        $imageContent = Storage::disk('public')->get($path);

                        // Konversi ke WebP menggunakan Intervention Image
                        $manager = new ImageManager(new Driver());
                        $image = $manager->read($imageContent);
                        $encoded = $image->toWebp(80); // Kualitas 80%

                        // Buat nama file baru dengan ekstensi .webp
                        $newPath = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME) . '.webp';

                        // Simpan file baru
                        Storage::disk('public')->put($newPath, (string) $encoded);

                        // Hapus file asli (misal .jxl atau .jpg)
                        Storage::disk('public')->delete($path);

                        // Update nama file di database
                        $menuItem->foto = $newPath;
                    }
                }
            }
        });
    }
}

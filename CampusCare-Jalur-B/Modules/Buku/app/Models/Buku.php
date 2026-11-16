<?php

namespace Modules\Buku\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Buku\Database\Factories\BukuFactory;

class Buku extends Model
{
    /** @use HasFactory<BukuFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_buku',
        'judul',
        'penulis',
        'kategori',
        'stok',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stok' => 'integer',
        ];
    }

    protected static function newFactory(): BukuFactory
    {
        return BukuFactory::new();
    }
}

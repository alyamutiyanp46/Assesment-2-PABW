<?php

namespace Modules\Peminjaman\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = ['user_id', 'nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status', 'file_path'];

    protected function casts(): array
    {
        return ['tanggal_pinjam' => 'date:Y-m-d', 'tanggal_kembali' => 'date:Y-m-d'];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

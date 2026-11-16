<?php

namespace Modules\Pustaka\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['category_id', 'title', 'author', 'isbn', 'stock', 'cover_path', 'description'];

    // Buku menyimpan foreign key category_id dan mempunyai banyak transaksi.
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }
}

<?php

namespace Modules\Pustaka\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = ['book_id', 'user_id', 'borrowed_at', 'due_at', 'returned_at', 'status', 'notes'];
    protected $casts = ['borrowed_at' => 'date', 'due_at' => 'date', 'returned_at' => 'date'];

    // Peminjaman selalu terhubung ke satu buku dan satu pengguna.
    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

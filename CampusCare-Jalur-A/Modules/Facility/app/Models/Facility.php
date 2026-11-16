<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Complaint\Models\Complaint;

class Facility extends Model
{
    protected $fillable = ['name', 'location', 'category', 'description', 'condition'];

    // Satu fasilitas dapat muncul pada banyak laporan kerusakan.
    public function complaints(): HasMany { return $this->hasMany(Complaint::class); }
}

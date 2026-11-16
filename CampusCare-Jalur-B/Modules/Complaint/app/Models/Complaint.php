<?php

namespace Modules\Complaint\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Facility\Models\Facility;

class Complaint extends Model
{
    protected $fillable = ['user_id', 'facility_id', 'title', 'description', 'location', 'photo_path', 'status'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function facility(): BelongsTo { return $this->belongsTo(Facility::class); }
}

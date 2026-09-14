<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'email', 'state_id', 'lga_id', 'ward_id', 'interest', 'consent',
    ];

    protected $casts = ['consent' => 'boolean'];

    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function lga(): BelongsTo { return $this->belongsTo(Lga::class); }
    public function ward(): BelongsTo { return $this->belongsTo(Ward::class); }
}

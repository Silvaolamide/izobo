<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    protected $fillable = ['lga_id', 'name', 'code'];

    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }
}

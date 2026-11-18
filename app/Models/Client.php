<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'whatsapp_number',
    ];

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(
            LegalCase::class,
            'case_client',
            'client_id',
            'case_id'
        );
    }
}
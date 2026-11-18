<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalCase extends Model
{
    protected $fillable = [
        'title',
        'status',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'case_client',
            'case_id',
            'client_id'
        );
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'case_id');
    }
}
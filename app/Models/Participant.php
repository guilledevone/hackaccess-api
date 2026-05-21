<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'nom',
        'cognom',
        'email',
        'tipus_entrada',
        'necessitats_especials',
    ];

    protected $casts = [
        'necessitats_especials' => 'boolean',
    ];
}
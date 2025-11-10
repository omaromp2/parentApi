<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name', 'path', 'status', 'extracted', 'summary', 'error'
    ];

    protected $casts = [
        'extracted' => 'array',
        'summary'   => 'array',
    ];
}

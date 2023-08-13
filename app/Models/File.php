<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'path',
        'size',
        'type'
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}

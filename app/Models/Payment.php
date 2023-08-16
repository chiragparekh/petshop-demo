<?php

namespace App\Models;

use App\Casts\PaymentDetailsCast;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'details'
    ];

    protected $casts = [
        'type' => PaymentType::class,
        'details' => PaymentDetailsCast::class
    ];

    public function uniqueIds()
    {
        return ['uuid'];
    }
}

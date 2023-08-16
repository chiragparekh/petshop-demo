<?php

namespace App\Models;

use App\Casts\ProductMetadataCast;
use App\Data\ProductMetadataData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_uuid',
        'title',
        'uuid',
        'price',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => ProductMetadataData::class
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            related: Category::class,
            foreignKey: 'category_uuid',
            ownerKey: 'uuid'
        );
    }
}

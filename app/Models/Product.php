<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'cost',
        'stock',
        'status',
        'is_featured',
        'is_visible',
        'brand',
        'category',
        'images',
        'tags',
        'specifications',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'specifications' => 'array',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'published_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

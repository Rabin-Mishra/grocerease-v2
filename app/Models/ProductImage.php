<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ─────────────────────────────────

    protected function url(): Attribute
    {
        return Attribute::get(function () {
            if ($this->image_path) {
                return Storage::disk(config('filesystems.default'))->url($this->image_path);
            }

            return asset('images/placeholder.jpg');
        });
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::disk(config('filesystems.default'))->url($this->image_path);
        }
        return asset('images/placeholder.jpg');
    }
}

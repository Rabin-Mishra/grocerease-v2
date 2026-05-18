<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    // ── Relationships ─────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ── Accessors ─────────────────────────────────

    protected function itemCount(): Attribute
    {
        return Attribute::get(fn () => $this->items->sum('quantity'));
    }

    protected function total(): Attribute
    {
        return Attribute::get(fn () => $this->items->sum(fn ($item) => $item->quantity * $item->product->price));
    }
}

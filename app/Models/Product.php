<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'producer_id', 'name', 'slug', 'reference', 'short_description',
        'description', 'origin', 'unit', 'packaging_type_id', 'price', 'professional_price', 'wholesale_price',
        'promo_price', 'promo_starts_at', 'promo_ends_at', 'stock_quantity',
        'stock_alert_threshold', 'expiry_date', 'weight', 'is_featured', 'is_new', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'professional_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'promo_starts_at' => 'datetime',
        'promo_ends_at' => 'datetime',
        'expiry_date' => 'date',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function supplierProducts(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function packagingType(): BelongsTo
    {
        return $this->belongsTo(PackagingType::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function isOnPromo(): bool
    {
        if (! $this->promo_price) {
            return false;
        }

        $now = now();

        if ($this->promo_starts_at && $now->lt($this->promo_starts_at)) {
            return false;
        }

        if ($this->promo_ends_at && $now->gt($this->promo_ends_at)) {
            return false;
        }

        return true;
    }

    public function currentPrice(): float
    {
        return $this->isOnPromo() ? (float) $this->promo_price : (float) $this->price;
    }

    public const PRICE_TIERS = [
        'retail' => 'Tarif public',
        'professionnel' => 'Tarif professionnel',
        'gros' => 'Tarif de gros',
    ];

    public function priceFor(?User $user, int $quantity = 1): float
    {
        return $this->tieredPrice($user, $quantity)[1];
    }

    public function priceTierFor(?User $user, int $quantity = 1): string
    {
        return $this->tieredPrice($user, $quantity)[0];
    }

    /**
     * @return array{0: string, 1: float}
     */
    protected function tieredPrice(?User $user, int $quantity): array
    {
        if ($user && $user->isApprovedB2B()) {
            if ($this->wholesale_price && $quantity >= 10) {
                return ['gros', (float) $this->wholesale_price];
            }

            if ($this->professional_price) {
                return ['professionnel', (float) $this->professional_price];
            }
        }

        return ['retail', $this->currentPrice()];
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function mainImage(): ?string
    {
        return $this->images->first()?->path;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true)->latest();
    }

    public function averageRating(): ?float
    {
        $average = $this->approvedReviews()->avg('rating');

        return $average ? round($average, 1) : null;
    }

    public function reviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }

    public function purchasedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->orderItems()
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id)->where('status', 'livree'))
            ->exists();
    }

    public function reviewedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->reviews()->where('user_id', $user->id)->exists();
    }

    public function wishlistedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return Wishlist::where('user_id', $user->id)->where('product_id', $this->id)->exists();
    }

    public static function bestSellerIds(int $limit = 5): array
    {
        return Cache::remember('best_seller_product_ids', 3600, function () use ($limit) {
            return OrderItem::query()
                ->whereHas('order', fn ($q) => $q->where('created_at', '>=', now()->subDays(30))
                    ->whereNotIn('status', ['annulee', 'remboursee']))
                ->selectRaw('product_id, SUM(quantity) as total_qty')
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->limit($limit)
                ->pluck('product_id')
                ->toArray();
        });
    }

    public function isBestSeller(): bool
    {
        return in_array($this->id, self::bestSellerIds(), true);
    }

    public function toCard(?User $user = null): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => (float) $this->price,
            'promo_price' => $this->promo_price ? (float) $this->promo_price : null,
            'professional_price' => $this->professional_price ? (float) $this->professional_price : null,
            'is_new' => (bool) $this->is_new,
            'is_on_promo' => $this->isOnPromo(),
            'is_best_seller' => $this->isBestSeller(),
            'in_stock' => $this->inStock(),
            'stock_quantity' => $this->stock_quantity,
            'stock_alert_threshold' => $this->stock_alert_threshold,
            'rating' => $this->averageRating(),
            'category_name' => $this->category?->name,
            'producer_name' => $this->producer?->name,
            'image' => $this->images->first()?->path,
            'is_wishlisted' => $this->wishlistedBy($user),
        ];
    }
}

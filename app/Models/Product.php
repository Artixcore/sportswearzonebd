<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use LogicException;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_at_price',
        'cost_price',
        'discount_percent',
        'sku',
        'stock',
        'low_stock_threshold',
        'is_active',
        'is_featured',
        'sort_order',
        'main_image_path',
        'size_type',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Build a URL slug from a product name that does not collide with any row (including soft-deleted).
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreProductId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'product';
        }

        $maxSlugLength = 255;
        $suffixReserve = 20;
        if (strlen($base) > $maxSlugLength - $suffixReserve) {
            $base = rtrim(substr($base, 0, $maxSlugLength - $suffixReserve), '-');
            if ($base === '') {
                $base = 'product';
            }
        }

        $n = 1;
        for ($iter = 0; $iter < 10_000; $iter++) {
            $suffix = $n === 1 ? '' : '-'.$n;
            $maxBase = $maxSlugLength - strlen($suffix);
            $truncatedBase = strlen($base) > $maxBase ? rtrim(substr($base, 0, $maxBase), '-') : $base;
            if ($truncatedBase === '') {
                $truncatedBase = 'product';
            }
            $slug = $truncatedBase.$suffix;

            $query = static::query()->withTrashed()->where('slug', $slug);
            if ($ignoreProductId !== null) {
                $query->where('id', '!=', $ignoreProductId);
            }
            if (! $query->exists()) {
                return $slug;
            }
            $n++;
        }

        throw new LogicException('Could not generate a unique product slug.');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPrimaryImageAttribute(): \stdClass|ProductImage|null
    {
        if ($this->main_image_path) {
            return (object) ['path' => $this->main_image_path];
        }

        return $this->images->first();
    }

    /**
     * Main image first (if set), then gallery images. Use for product show gallery + thumbs.
     */
    public function getDisplayImagesAttribute(): \Illuminate\Support\Collection
    {
        $main = $this->main_image_path
            ? collect([(object) ['path' => $this->main_image_path]])
            : collect();

        return $main->concat($this->images);
    }

    /**
     * Whether this product has a valid discount (compare-at price or stored discount %).
     */
    public function getHasDiscountAttribute(): bool
    {
        if ($this->compare_at_price && (float) $this->compare_at_price > (float) $this->price) {
            return true;
        }
        $raw = $this->getRawOriginal('discount_percent');

        return $raw !== null && (float) $raw > 0;
    }

    /**
     * Final price the customer pays (after discount). Rounded to 2 decimals.
     */
    public function getFinalPriceAttribute(): float
    {
        $price = (float) $this->getRawOriginal('price');
        $compareAt = $this->compare_at_price ? (float) $this->compare_at_price : null;
        $discountPercent = $this->getRawOriginal('discount_percent');
        $discountPercent = $discountPercent !== null ? (float) $discountPercent : 0;

        if ($compareAt !== null && $compareAt > $price) {
            return round($price, 2);
        }
        if ($discountPercent > 0) {
            return round($price - ($price * $discountPercent / 100), 2);
        }

        return round($price, 2);
    }

    /**
     * Original/list price to show with strikethrough when has_discount; null otherwise.
     */
    public function getOriginalPriceAttribute(): ?float
    {
        if (! $this->has_discount) {
            return null;
        }
        $price = (float) $this->getRawOriginal('price');
        $compareAt = $this->compare_at_price ? (float) $this->compare_at_price : null;
        if ($compareAt !== null && $compareAt > $price) {
            return round($compareAt, 2);
        }

        return round($price, 2);
    }

    /**
     * Discount badge label, e.g. "-10%". Null when no discount.
     */
    public function getDiscountLabelAttribute(): ?string
    {
        if (! $this->has_discount) {
            return null;
        }
        $percent = $this->effective_discount_percent;

        return $percent !== null ? '-'.(int) round($percent).'%' : null;
    }

    /**
     * Effective discount percent for display (from compare-at when applicable, else stored discount_percent).
     */
    public function getEffectiveDiscountPercentAttribute(): ?float
    {
        if (! $this->has_discount) {
            return null;
        }
        $price = (float) $this->getRawOriginal('price');
        $compareAt = $this->compare_at_price ? (float) $this->compare_at_price : null;
        if ($compareAt !== null && $compareAt > $price && $compareAt > 0) {
            return (1 - $price / $compareAt) * 100;
        }
        $raw = $this->getRawOriginal('discount_percent');

        return $raw !== null ? (float) $raw : null;
    }

    public function isLowStock(): bool
    {
        if ($this->variants()->exists()) {
            return $this->variants()->whereRaw('stock <= low_stock_threshold')->exists();
        }

        return $this->stock <= $this->low_stock_threshold;
    }

    /**
     * Allowed size values for this product based on size_type.
     * Panjabi: 40, 42, 44. Standard: S, M, L, XL, XXL.
     */
    public static function allowedSizesFor(string $sizeType): array
    {
        return match ($sizeType) {
            'numeric_panjabi' => ['40', '42', '44'],
            'standard' => ['S', 'M', 'L', 'XL', 'XXL'],
            default => ['S', 'M', 'L', 'XL', 'XXL'],
        };
    }

    public function getAllowedSizesAttribute(): array
    {
        return self::allowedSizesFor($this->size_type ?? 'standard');
    }
}

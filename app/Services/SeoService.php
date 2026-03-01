<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;

class SeoService
{
    public function getDefaultTitle(): string
    {
        return Setting::get('seo_default_title', config('seo.default_title', config('app.name')));
    }

    public function getDefaultDescription(): string
    {
        return Setting::get('seo_default_description', config('seo.default_description', ''));
    }

    public function getSiteName(): string
    {
        return Setting::get('site_name', config('seo.site_name', config('app.name')));
    }

    public function productJsonLd(Product $product): array
    {
        $image = $product->primaryImage
            ? url('storage/' . $product->primaryImage->path)
            : null;
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description ?? $product->description,
            'image' => $image,
            'sku' => $product->sku,
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->price,
                'priceCurrency' => 'BDT',
                'availability' => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
    }

    public function websiteJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $this->getSiteName(),
            'url' => config('app.url'),
        ];
    }

    public function organizationJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->getSiteName(),
            'url' => config('app.url'),
        ];
    }

    public function breadcrumbJsonLd(array $items): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];
        $urls[] = ['loc' => url(route('home')), 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => url(route('shop.index')), 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => url(route('contact')), 'changefreq' => 'monthly', 'priority' => '0.5'];

        Category::whereNull('parent_id')->get()->each(function (Category $c) use (&$urls) {
            $urls[] = ['loc' => url(route('shop.category', $c->slug)), 'changefreq' => 'weekly', 'priority' => '0.8'];
        });

        Product::where('is_active', true)->get()->each(function (Product $p) use (&$urls) {
            $urls[] = ['loc' => url(route('product.show', $p->slug)), 'changefreq' => 'weekly', 'priority' => '0.7'];
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml .= '<url><loc>' . e($u['loc']) . '</loc><changefreq>' . $u['changefreq'] . '</changefreq><priority>' . $u['priority'] . '</priority></url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}

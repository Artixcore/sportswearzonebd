<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /checkout\n";
        $content .= "Disallow: /cart\n";
        $content .= "Sitemap: " . url(route('sitemap')) . "\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}

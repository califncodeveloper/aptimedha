<?php

namespace App\Http\Controllers\Frontend;

use App\Services\SitemapService;
use Illuminate\Http\Response;

/**
 * Serves the dynamic XML sitemap for search engines.
 */
class SitemapController
{
    /**
     * Return the cached or generated sitemap XML.
     */
    public function index(): Response
    {
        $xml = SitemapService::getCachedXml();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}


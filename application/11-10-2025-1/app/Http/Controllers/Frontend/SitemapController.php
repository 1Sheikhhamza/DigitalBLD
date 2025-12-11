<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;


class SitemapController extends BaseController
{
    
    public function index()
    {
        return response()->view('sitemap')->header('Content-Type', 'text/xml');
    }
}

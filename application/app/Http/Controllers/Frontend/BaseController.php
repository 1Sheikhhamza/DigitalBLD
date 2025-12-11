<?php

namespace App\Http\Controllers\Frontend;

// use App\Http\Controllers\Controller;

use App\Models\HomepageSection;
use App\Models\Page;
use App\Services\HomeService;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    public function __construct(HomeService $service)
    {
        $pages = $service->getPages();
        // $footerPages = $service->getFooterPages();
        $homePageContent = $service->homePageContent();
        $contactPage = $service->contactPage();

        // Footer section (single record containing all footer info)
        $footer = HomepageSection::where('section_type', 'Footer')->first();
        $footerData = $footer ? json_decode($footer->data, true) : [];

        // Resolve Discover & Quick Link menus
        $discoverMenus = !empty($footerData['discover_menu'])
            ? Page::whereIn('id', $footerData['discover_menu'])->get()
            : collect();

        $quickMenus = !empty($footerData['quick_link_menu'])
            ? Page::whereIn('id', $footerData['quick_link_menu'])->get()
            : collect();

        // dd($footerData);
        view()->share([
            'discoverMenus' => $discoverMenus,
            'quickMenus' => $quickMenus,
            'footerData' => $footerData,
            'pages' => $pages,
            // 'footerPages' => $footerPages,
            'homePageContent' => $homePageContent,
            'contactPage' => $contactPage,
        ]);
    }
}

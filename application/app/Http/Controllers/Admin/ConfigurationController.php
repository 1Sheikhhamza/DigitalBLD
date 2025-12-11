<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\BaseController;
use App\Models\HomepageSection;
use App\Models\Page;
use App\Services\ConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ConfigurationController extends BaseController
{
    protected ConfigurationService $service;

    public function __construct(ConfigurationService $service)
    {
        $this->service = $service;
    }

    // Update Company Profile
    public function company()
    {
        $config = $this->service->get();
        return view('admin.configuration.company', compact('config'));
    }

    public function updateCompany(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'white_label_name' => 'nullable|string|max:255',
            'white_label_url' => 'nullable|url',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // File upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        if ($request->hasFile('favicon')) {
            $data['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $this->service->update($data);

        return redirect()->back()->with('success', 'Configuration updated successfully.');
    }




    // Update Footer Profile
    public function footer()
    {
        // Fetch footer config (single record)
        $footer = HomepageSection::where('section_type', 'Footer')
            ->where('section_key', 'footer_main')
            ->first();

        // Decode data JSON if available
        $config = $footer ? json_decode($footer->data, true) : [];

        // Fetch menu data (for dropdowns)
        $discoverMenu = Page::select('id', 'title')->get();
        $quickMenu = Page::select('id', 'title')->get();

        return view('admin.configuration.footer', compact('config', 'discoverMenu', 'quickMenu'));
    }



    public function updateFooter(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ssl_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'about_us' => 'nullable|string',
            'contact_address' => 'nullable|string',
            'copy_right_text' => 'nullable|string',
            'discover_section' => 'nullable|string|max:255',
            'discover_menu' => 'nullable|array',
            'discover_menu.*' => 'nullable|integer|exists:pages,id',
            'quick_link' => 'nullable|string|max:255',
            'quick_link_menu' => 'nullable|array',
            'quick_link_menu.*' => 'nullable|integer|exists:pages,id',
        ]);

        $data = [
            'about_us' => $validated['about_us'] ?? null,
            'contact_address' => $validated['contact_address'] ?? null,
            'copy_right_text' => $validated['copy_right_text'] ?? null,
            'discover_section' => $validated['discover_section'] ?? null,
            'discover_menu' => $validated['discover_menu'] ?? [],
            'quick_link' => $validated['quick_link'] ?? null,
            'quick_link_menu' => $validated['quick_link_menu'] ?? [],
        ];


        if ($request->hasFile('company_logo') && $request->file('company_logo')->isValid()) {
            $data['company_logo'] = ImageUploadHelper::upload(
                $request->file('company_logo'),
                'uploads/configuration',
                'footer-logo',
                300,
                300,
            );
        }


        if ($request->hasFile('ssl_image') && $request->file('ssl_image')->isValid()) {
            $data['ssl_image'] = ImageUploadHelper::upload(
                $request->file('ssl_image'),
                'uploads/configuration',
                'ssl-logo',
                null,
                null,
            );
        }

        
        // Handle file uploads
        /* if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('footer', 'public');
        } elseif (!empty($request->old_company_logo)) {
            // Preserve old logo if no new file uploaded
            $data['company_logo'] = $request->old_company_logo;
        }

        if ($request->hasFile('ssl_image')) {
            $data['ssl_image'] = $request->file('ssl_image')->store('footer', 'public');
        } elseif (!empty($request->old_ssl_image)) {
            $data['ssl_image'] = $request->old_ssl_image;
        } */

        // Upsert into homepage_sections table
        HomepageSection::updateOrInsert(
            [
                'section_type' => 'Footer',
                'section_key' => 'footer_main',
            ],
            [
                'section_name' => 'Footer Configuration',
                'data' => json_encode($data),
                'status' => 1,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Footer updated successfully.');
    }


    // Homepage Section dynamic manage

    public function homepage()
    {
        $homepageSections = HomepageSection::where('section_type', 'Homepage')
            ->get()
            ->keyBy('section_key')
            ->map(function ($item) {
                $data = json_decode($item->data, true) ?? [];
                return [
                    'title' => $data['title'] ?? $item->section_name,
                    'position' => $item->position,
                    'display' => $data['display'] ?? $item->status,
                ];
            })
            ->toArray();
        return view('admin.configuration.homepage', compact('homepageSections'));
    }

    public function updateHomepage(Request $request)
    {
        // Validate input structure
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.position' => 'nullable|integer|min:0',
            'sections.*.display' => 'nullable|boolean',
        ]);

        $sections = $validated['sections'];

        foreach ($sections as $key => $sectionData) {
            $title = $sectionData['title'] ?? ucfirst(str_replace('-', ' ', $key));
            $position = $sectionData['position'] ?? 0;
            $display = isset($sectionData['display']) ? 1 : 0;

            // Prepare data JSON
            $data = [
                'title' => $title,
                'display' => $display,
            ];

            // Update or insert into homepage_sections table
            HomepageSection::updateOrInsert(
                [
                    'section_type' => 'Homepage',
                    'section_key' => $key,
                ],
                [
                    'section_name' => $title,
                    'position' => $position,
                    'data' => json_encode($data),
                    'status' => $display,
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Homepage sections updated successfully.');
    }
}

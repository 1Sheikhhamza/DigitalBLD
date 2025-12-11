<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Models\Banner;
use App\Models\Subscriber;
use App\Models\Package;
use App\Models\Service;
use App\Models\Career;
use App\Models\Client;
use App\Models\Photo;
use App\Models\Video;
use App\Models\ClientFeedback;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Team;
use App\Models\Inquiry;
use App\Models\OCRExtraction;
use App\Models\Subscription;
use App\Repositories\Contracts\HomeRepositoryInterface;

class HomeRepository implements HomeRepositoryInterface
{
    public function getPages()
    {
        return Page::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('status', 1)
                    ->orderBy('sequence'); // Order sub-pages by sequence
            }])
            ->where('menu_type', 'Main Menu')
            ->where('status', 1)
            ->orderBy('sequence') // Order top-level pages by sequence
            ->get();
    }

    public function getFooterPages()
    {
        return Page::whereNull('parent_id')
            ->where('menu_type', 'Footer Menu')
            ->where('status', 1)
            ->orderBy('sequence') // Order top-level pages by sequence
            ->get();
    }



    public function getHomepageLegalDecision()
    {
        return OCRExtraction::whereNotNull('volume_id')
            ->orderBy('id', "DESC")
            ->limit(6)
            ->get();
    }


    public function getHomePages()
    {
        return Page::where('homepage_display', 1)
            ->where('status', 1)
            ->orderBy('sequence')
            ->get();
    }

    public function homePageContent()
    {
        return Page::where('id', 1)
            ->where('parent_id', null)
            ->where('status', 1)
            ->first();
    }

    public function contactPage()
    {
        return Page::where('id', 7)
            ->where('parent_id', null)
            ->where('status', 1)
            ->first();
    }

    public function getWhyChoose()
    {
        return Page::where('why_choose', 1)
            ->where('status', 1)
            ->orderBy('sequence')
            ->limit(5)
            ->get();
    }



    public function getPagesBySlugs(string $slug, ?string $sslug = null): array
    {
        $parentPage = Page::where('slug', $slug)->firstOrFail();

        if ($sslug === null) {
            $othersPages = Page::select('title', 'slug')->whereNull('parent_id')->where('page_structure', 'Text')->get();
            $data = ['content' => $parentPage, 'parentMenu' => null, 'othersPages' => $othersPages];
        } else {
            $childPage = Page::where('slug', $sslug)->firstOrFail();
            $othersPages = Page::with('parent:id,title,slug')->select('id', 'parent_id', 'title', 'slug')->where('parent_id', $parentPage->id)->get();
            $data = ['content' => $childPage, 'parentMenu' => $parentPage->title, 'othersPages' => $othersPages];
        }

        return $data;
    }


    public function getBanner()
    {
        return Banner::latest()->get();
    }

    public function getSubscriber()
    {
        return Subscriber::all();
    }

    // Package
    public function getPackage($limit = 10)
    {
        $query = Package::with(['features' => function ($q) {
            $q->where('status', 1)->whereNull('deleted_at');
        }, 'features.modules' => function ($q) {
            $q->where('status', 1)->whereNull('deleted_at');
        }])
            ->where('status', 1)
            ->latest();

        if ($limit === 'all') {
            return $query->get();
        }

        return $query->paginate($limit);
    }


    public function getPackageDetails($slug)
    {
        return Package::where('slug', $slug)->firstOrFail();
    }

    public function getPackageByID($id)
    {
        return Package::where('id', $id)->firstOrFail();
    }


    // Service
    public function getService($limit)
    {
        return Service::all();
    }

    public function getServiceDetails($slug)
    {
        return Service::where('slug', $slug)->firstOrFail();
    }


    // Career
    public function getCareer($limit)
    {
        return Career::where('job_status', 'published')
            ->where('status', 1)
            ->orderByDesc('published_at')
            ->get();
    }

    public function getCareerDetails($slug)
    {
        return Career::where('slug', $slug)->firstOrFail();
    }


    public function getClient()
    {
        return Client::where('status', 1)->latest()->get();
    }

    public function getPhoto($limit = null)
    {
        if (isset($limit) && $limit != '') {
            return Photo::where('status', 1)->latest()->limit($limit)->get();
        } else {
            return Photo::where('status', 1)->latest()->paginate(12);
        }
    }

    public function getVideo()
    {
        return Video::where('status', 1)->latest()->get();
    }

    public function getFeedback()
    {
        return ClientFeedback::where('status', 1)->latest()->get();
    }

    // Blog
    public function getBlog($limit)
    {
        if (isset($limit) && $limit == 'all') {
            return Blog::where('status', 'published')->latest()->get();
        } else {
            return Blog::where('status', 'published')->latest()->paginate($limit);
        }
    }

    public function getBlogDetails($slug)
    {
        return Blog::where('slug', $slug)->firstOrFail();
    }


    public function getTeam()
    {
        return Team::where('status', 1)->latest()->get();
    }

    public function subscriptionSubmit(array $data)
    {
        return Subscription::create($data);
    }

    public function setInquiry(array $data)
    {
        return Inquiry::create($data);
    }

    public function getFaq($limit)
    {
        if (isset($limit) && $limit == 'all') {
            return Faq::where('status', 1)->latest()->get();
        } else {
            return Faq::where('status', 1)->latest()->paginate($limit);
        }

    }
}

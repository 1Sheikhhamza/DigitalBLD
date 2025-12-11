<?php

namespace App\Repositories\Eloquent;

use App\Models\Banner;
use App\Models\OCRExtraction;
use App\Models\Package;
use App\Models\Subscriber;
use App\Models\Volume;
use App\Repositories\Contracts\CommonRepositoryInterface;

class CommonRepository implements CommonRepositoryInterface
{

    public function getBanner()
    {
        return Banner::select('id', 'name')->get();
    }

    public function getVolume()
    {
        return Volume::withoutTrashed()->orderBy('number', 'asc')->pluck('number', 'id');
    }


    public function getSubscriber()
    {
        return Subscriber::pluck('name', 'id');
    }

    public function getPackages()
    {
        return Package::pluck('name', 'id');
    }

    public function getAdminSubscriber($limit)
    {
        return Subscriber::limit($limit)->get();
    }

    public function getJurisdiction()
    {
        return OCRExtraction::distinct()->pluck('jurisdiction');
    }
}

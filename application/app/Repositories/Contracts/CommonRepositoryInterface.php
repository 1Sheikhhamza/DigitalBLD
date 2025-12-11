<?php

namespace App\Repositories\Contracts;

interface CommonRepositoryInterface
{
    public function getBanner();
    public function getVolume();
    public function getSubscriber();
    public function getAdminSubscriber($limit);
    public function getPackages();
    public function getJurisdiction();
}

<?php

namespace App\Services;

use App\Repositories\Contracts\CommonRepositoryInterface;

class CommonService {

    protected $repository;

    public function __construct(CommonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getBanner(){
        return $this->repository->getBanner();
    }

    public function getSubscriber(){
        return $this->repository->getSubscriber();
    }

    public function getAdminSubscriber($limit){
        return $this->repository->getAdminSubscriber($limit);
    }

    public function getPackage(){
        return $this->repository->getPackages();
    }

    public function getVolume(){
        return $this->repository->getVolume();
    }

    public function getJurisdiction(){
        return $this->repository->getJurisdiction();
    }
}

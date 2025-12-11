<?php

namespace App\Services;

use App\Repositories\Contracts\HomeRepositoryInterface;

class HomeService
{
    protected $repository;

    public function __construct(HomeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPages()
    {
        return $this->repository->getPages();
    }

    public function getFooterPages()
    {
        return $this->repository->getFooterPages();
    }

    public function getHomepageLegalDecision()
    {
        return $this->repository->getHomepageLegalDecision();
    }

    public function homePageContent()
    {
        return $this->repository->homePageContent();
    }

    public function contactPage()
    {
        return $this->repository->contactPage();
    }

    public function getHomePages()
    {
        return $this->repository->getHomePages();
    }

    public function getWhyChoose()
    {
        return $this->repository->getWhyChoose();
    }

    public function getHowToBldWorks()
    {
        return $this->repository->getHowToBldWorks();
    }

    public function getPagesBySlugs(string $slug, ?string $sslug = null): array
    {
        return $this->repository->getPagesBySlugs($slug, $sslug);
    }


    public function getBanner()
    {
        return $this->repository->getBanner();
    }

    public function getSubscriber()
    {
        return $this->repository->getSubscriber();
    }

    public function getPackage($limit = null)
    {
        return $this->repository->getPackage($limit);
    }

    public function getPackageDetails($slug)
    {
        return $this->repository->getPackageDetails($slug);
    }

    public function getPackageByID($id)
    {
        return $this->repository->getPackageByID($id);
    }


    public function getService($limit = null)
    {
        return $this->repository->getService($limit);
    }

    public function getServiceDetails($slug)
    {
        return $this->repository->getServiceDetails($slug);
    }

    public function getCareer($limit = null)
    {
        return $this->repository->getCareer($limit);
    }

    public function getCareerDetails($slug)
    {
        return $this->repository->getCareerDetails($slug);
    }
    

    public function getClient()
    {
        return $this->repository->getClient();
    }

    public function getPhoto($limit = null)
    {
        return $this->repository->getPhoto($limit);
    }

    public function getVideo()
    {
        return $this->repository->getVideo();
    }

    public function getFeedback()
    {
        return $this->repository->getFeedback();
    }

    public function getBlog($limit = null)
    {
        return $this->repository->getBlog($limit);
    }

    public function getBlogDetails($slug)
    {
        return $this->repository->getBlogDetails($slug);
    }


    public function getTeam()
    {
        return $this->repository->getTeam();
    }

    public function subscriptionSubmit(array $data)
    {
        return $this->repository->subscriptionSubmit($data);
    }

    public function setInquiry(array $data)
    {
        return $this->repository->setInquiry($data);
    }

    public function getFaq($limit = null)
    {
        return $this->repository->getFaq($limit);
    }

    public function getUserManual($limit = null, $keywords = null)
    {
        return $this->repository->getUserManual($limit, $keywords);
    }
}

<?php

namespace App\Repositories\Contracts;

interface HomeRepositoryInterface
{
    public function getPages();
    public function getFooterPages();
    public function getHomepageLegalDecision();
    public function getHomePages();
    public function homePageContent();
    public function contactPage();
    public function getWhyChoose();
    public function getPagesBySlugs(string $slug, ?string $sslug = null): array;
    public function getBanner();
    public function getSubscriber();

    // Package
    public function getPackage(string $limit);
    public function getPackageDetails($slug);
    public function getPackageByID($id);

    // Service
    public function getService(string $limit);
    public function getServiceDetails($slug);

    // Career
    public function getCareer(string $limit);
    public function getCareerDetails($slug);

    // Blog
    public function getBlog(string $limit);
    public function getBlogDetails($slug);

    public function getClient();
    public function getPhoto($limit);
    public function getvideo();
    public function getFeedback();
    public function getTeam();
    public function subscriptionSubmit(array $data);
    public function setInquiry(array $data);
    public function getFaq(string $limit);
}

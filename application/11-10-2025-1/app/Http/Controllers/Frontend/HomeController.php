<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use App\Services\HomeService;

class HomeController extends BaseController
{
	protected $homeService;

	public function __construct(HomeService $homeService)
	{
		parent::__construct($homeService);
		$this->homeService = $homeService;
	}

	public function index()
	{
		$homepageData = [
			'banners'      => $this->homeService->getBanner(),
			'homePages'    => $this->homeService->getHomePages(),
			'legalDecision'    => $this->homeService->getHomepageLegalDecision(),
			'whyChooses'   => $this->homeService->getWhyChoose(),
			'subscribers'    => $this->homeService->getSubscriber(),
			'packages'     => $this->homeService->getPackage(3),
			'services'     => $this->homeService->getService(),
			'careers'      => $this->homeService->getCareer(),
			'usefulllinks' => $this->homeService->getClient(),
			'photos'       => $this->homeService->getPhoto(8),
			'videos'       => $this->homeService->getVideo(),
			'feedbacks'    => $this->homeService->getFeedback(),
			'blogs'        => $this->homeService->getBlog(2),
			'teams'        => $this->homeService->getTeam(),
			'faqs' 			=> $this->homeService->getFaq(5)
		];

		$homepageSections = HomepageSection::where('section_type', 'Homepage')
			->where('status', 1)
			->orderBy('position', 'asc')
			->get()
			->mapWithKeys(function ($section) {
				return [$section->section_key => json_decode($section->data, true)];
			});

	return view('frontend.home', compact('homepageData','homepageSections'));
	}

	public function content($slug, $sslug = null)
	{
		$contents = $this->homeService->getPagesBySlugs($slug, $sslug);
		$currentSlug = request()->segment(count(request()->segments()));
		return view('frontend.article', compact('contents', 'currentSlug'));
	}


	// Service
	public function services()
	{
		return view('frontend.services.index', [
			'services' => $this->homeService->getService()
		]);
	}

	public function serviceDetails($currentSlug)
	{
		$service = $this->homeService->getServiceDetails($currentSlug);
		$allServices = $this->homeService->getService();
		return view('frontend.services.show', compact('service', 'allServices', 'currentSlug'));
	}

	// Package
	public function packages()
	{
		return view('frontend.packages.index', [
			'packages' => $this->homeService->getPackage()
		]);
	}

	public function packageDetails($currentSlug)
	{
		$package = $this->homeService->getPackageDetails($currentSlug);
		$allPackages = $this->homeService->getPackage(6);
		return view('frontend.packages.show', compact('package','allPackages'));
	}

	// Blog
	public function blogs()
	{
		return view('frontend.blogs.index', [
			'blogs' => $this->homeService->getBlog(20)
		]);
	}

	public function blogDetails($currentSlug)
	{
		$blog = $this->homeService->getBlogDetails($currentSlug);
		$allBlogs = $this->homeService->getBlog('all');
		return view('frontend.blogs.show', compact('blog', 'currentSlug','allBlogs'));
	}

	// Career
	public function careers()
	{
		return view('frontend.careers.index', [
			'careers' => $this->homeService->getCareer()
		]);
	}

	public function careerDetails($slug)
	{
		$career = $this->homeService->getCareerDetails($slug);
		return view('frontend.career.show', compact('career'));
	}


	public function teams()
	{
		return view('frontend.teams', [
			'teams' => $this->homeService->getTeam()
		]);
	}

	public function feedbacks()
	{
		return view('frontend.feedbacks', [
			'feedbacks' => $this->homeService->getFeedback()
		]);
	}

	public function clients()
	{
		return view('frontend.clients.index', [
			'clients' => $this->homeService->getClient()
		]);
	}

	public function photos()
	{
		return view('frontend.photos', [
			'photos' => $this->homeService->getPhoto()
		]);
	}

	public function videos()
	{
		return view('frontend.gallery.videos', [
			'videos' => $this->homeService->getVideo()
		]);
	}

	public function submitInquiry(Request $request)
	{
		$validated = $request->validate([
			'name'    => 'required|string|max:255',
			'email'   => 'required|email|max:255',
			'phone'   => 'nullable|string|max:20',
			'subject' => 'nullable|string|max:255',
			'message' => 'required|string',
		]);

		$this->homeService->setInquiry($validated);

		return redirect()
			->back()
			->with('success', 'Inquiry submitted successfully.')
			->with('scroll_to', '#inquiryForm');
	}

	public function storePackageOwner(Request $request)
	{
		$validated = $request->validate([
			'name'        => 'required|string|max:255',
			'email'       => 'required|email|max:255',
			'phone'       => 'nullable|string|max:20',
			'address'     => 'nullable|string',
			'nid_number'  => 'nullable|string|max:50',
			'project_id'  => 'required|exists:projects,id',
		]);

		$this->homeService->setPackageOwner($validated);

		return redirect()->back()->with('success', 'Package owner added successfully.');
	}

	public function faq()
	{
		$homepageData = [
			'faqs' => $this->homeService->getFaq()
		];
		return view('frontend.faq', compact('homepageData'));
	}
}

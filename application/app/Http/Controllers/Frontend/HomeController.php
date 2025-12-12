<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use App\Services\HomeService;
use App\Models\OCRExtraction;
use Barryvdh\DomPDF\Facade\Pdf;

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
			'banners' => $this->homeService->getBanner(),
			'homePages' => $this->homeService->getHomePages(),
			'legalDecision' => $this->homeService->getHomepageLegalDecision(),
			'whyChooses' => $this->homeService->getWhyChoose(),
			'howToBldWorks' => $this->homeService->getHowToBldWorks(),
			'subscribers' => $this->homeService->getSubscriber(),
			'packages' => $this->homeService->getPackage(3),
			'services' => $this->homeService->getService(),
			'careers' => $this->homeService->getCareer(),
			'usefulllinks' => $this->homeService->getClient(),
			'photos' => $this->homeService->getPhoto(8),
			'videos' => $this->homeService->getVideo(),
			'feedbacks' => $this->homeService->getFeedback(),
			'blogs' => $this->homeService->getBlog(2),
			'teams' => $this->homeService->getTeam(),
			'faqs' => $this->homeService->getFaq(5)
		];

		$homepageSections = HomepageSection::where('section_type', 'Homepage')
			->where('status', 1)
			->orderBy('position', 'asc')
			->get()
			->mapWithKeys(function ($section) {
				return [$section->section_key => json_decode($section->data, true)];
			});

		// dd($homepageData['howToBldWorks']);
		return view('frontend.home', compact('homepageData', 'homepageSections'));
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
		return view('frontend.packages.show', compact('package', 'allPackages'));
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
		return view('frontend.blogs.show', compact('blog', 'currentSlug', 'allBlogs'));
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
			'name' => 'required|string|max:255',
			'email' => 'required|email|max:255',
			'phone' => 'nullable|string|max:20',
			'subject' => 'nullable|string|max:255',
			'message' => 'required|string',
		]);

		$this->homeService->setInquiry($validated);

		return redirect()
			->back()
			->with('success', 'Inquiry submitted successfully.')
			->with('scroll_to', '#inquiryForm');
	}

	public function faq()
	{
		$homepageData = [
			'faqs' => $this->homeService->getFaq()
		];
		return view('frontend.faq', compact('homepageData'));
	}

	public function userManual(Request $request)
	{
		$keywords = null;
		if ($request->has('keywords')) {
			$keywords = $request->keywords;
		}
		$user_manual = $this->homeService->getUserManual('all', $keywords);
		return view('frontend.user_manual', compact('user_manual'));
	}

	public function previewJudgment($id)
	{
		$data = OCRExtraction::where('id', $id)
			->where('homepage', 1) // strict check
			->with('volume:id,number,year')
			->firstOrFail();

		$judgmentText = $data->judgment ?? '';
		$judgmentTextFormatted = $this->judgmentText($judgmentText);

		return view('frontend.preview_judgment', compact('data', 'judgmentTextFormatted'));
	}

	public function previewPrint($id, $type = null)
	{
		$data = OCRExtraction::where('id', $id)
			->where('homepage', 1) // strict check
			->firstOrFail();

		$judgmentText = $data->judgment ?? '';
		$judgmentTextFormatted = $this->judgmentText($judgmentText);

		// For guests, no user notes
		$userNote = null;

		if (isset($type) && $type == 'download') {
			$metaData = true;
			// PDF output might need a different view or reuse existing one. 
			// Reuse existing one but careful about auth dependencies in view?
			// Existing print view uses `auth('subscriber')->id()` in view? No, controller passed $userNote.
			// But let's check profile.legal_decision_print content.
			// Assuming it's safe if we pass null userNote.

			$pdf = Pdf::loadView('auth.subscribers.profile.legal_decision_print', compact('data', 'metaData', 'userNote', 'judgmentTextFormatted'));
			return $pdf->download('legal-decision-' . $data->id . '.pdf');

		} else {
			$metaData = false;
		}

		return view('auth.subscribers.profile.legal_decision_print', compact('data', 'metaData', 'userNote', 'judgmentTextFormatted'));
	}

	private function judgmentText($text, $highlightKeywords = [])
	{
		// Normalize line breaks
		$text = str_replace(["\r\n", "\r"], "\n", $text);

		// Break text into lines
		$lines = explode("\n", $text);

		$paragraph = '';
		$paragraphs = [];

		foreach ($lines as $line) {
			$trimmed = trim($line);
			if ($trimmed === '') {
				if (!empty($paragraph)) {
					$paragraphs[] = $paragraph;
					$paragraph = '';
				}
			} else {
				$paragraph .= $trimmed . ' ';
			}
		}

		if (!empty($paragraph)) {
			$paragraphs[] = $paragraph;
		}

		$stopWords = ['the', 'of', 'and', 'a', 'an', 'in', 'is', 'was', 'that', 'it', 'to', 'for', 'on', 'are', 'as', 'with', 'at', 'by', 'this', 'from', 'or', 'be', 'no', 'not'];

		$judgmentTextFormatted = '';
		foreach ($paragraphs as $para) {
			$content = trim($para);
			if (!empty($highlightKeywords)) {
				foreach ($highlightKeywords as $word) {
					$cleanWord = trim($word);
					if ($cleanWord !== '' && !in_array(strtolower($cleanWord), $stopWords)) {
						$content = preg_replace("/\b(" . preg_quote($cleanWord, '/') . ")\b/i", '<span style="background-color: yellow;">$1</span>', $content);
					}
				}
			}
			$judgmentTextFormatted .= '<p>' . $content . '</p>';
		}

		return $judgmentTextFormatted;
	}
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Administration;
use App\Models\ClientFeedback;
use App\Models\OCRExtraction;
use App\Models\Package;
use App\Models\Service;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use App\Services\CommonService;

class AdminController extends BaseController
{

    protected CommonService $commonService;
    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
        //$this->middleware('auth:administration');
    }

    public function dashboard()
    {
        // Get last 5 subscribers
        $subscribers = $this->commonService->getAdminSubscriber(5);
        $latestSubscriptions = Subscription::with('subscriber') // eager load subscriber relation
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();


        // Counts
        $subscriberCount = Subscriber::count();
        $totalPayments = Subscription::sum('fee');
        $packageCount = Package::count();
        $volumeCount = Volume::count();
        $legalDecisionCount = OCRExtraction::count();
        $legalDecisionCount = OCRExtraction::count();
        $appellateDivisionCount = OCRExtraction::where('division', 'Appellate Division')->count();
        $highCourtDivisionCount = OCRExtraction::where('division', 'High Court Division')->count();
        $subscriptionCount = Subscription::count();
        $serviceCount = Service::count();        // Adjust model if needed
        $teamMemberCount = Team::count();  // Adjust model if needed
        $clientFeedbackCount = ClientFeedback::count();
        $userCount = User::count();
        

        // Monthly subscription counts
        $monthlySubscribers = Subscriber::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Monthly payment sums
        $monthlyPayments = Subscription::selectRaw('MONTH(created_at) as month, SUM(fee) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Prepare chart labels & datasets
        $months = [];
        $subscriberTotals = [];
        $paymentTotals = [];
        foreach (range(1, 12) as $m) {
            $months[] = date('F', mktime(0, 0, 0, $m, 1));
            $subscriberTotals[] = $monthlySubscribers[$m] ?? 0;
            $paymentTotals[] = $monthlyPayments[$m] ?? 0;
        }

        return view('admin.dashboard', compact(
            'subscribers',
            'latestSubscriptions',
            'subscriberCount',
            'totalPayments',
            'packageCount',
            'volumeCount',
            'months',
            'subscriberTotals',
            'paymentTotals',
            'legalDecisionCount',
            'appellateDivisionCount',
            'highCourtDivisionCount',
            'subscriptionCount',
            'serviceCount',
            'teamMemberCount',
            'clientFeedbackCount',
            'userCount'
        ));
    }
}

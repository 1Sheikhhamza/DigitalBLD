<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\BaseController;
use App\Models\Event;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscriber;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Services\HomeService;

class SubscriptionController extends BaseController
{

    protected $subscriberId;

    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
        $this->middleware(function ($request, $next) {
            $this->subscriberId = auth('subscriber')->id();
            return $next($request);
        });
    }

    public function form($id)
    {
        $package = Package::with('features')->where('id', $id)->first();
        /* $package = Package::with(['features' => function ($q) {
            $q->where('status', 1)->whereNull('deleted_at');
        }, 'features.modules' => function ($q) {
            $q->where('status', 1)->whereNull('deleted_at');
        }])
            ->where('status', 1)
            ->where('id', $id)
            ->first(); */

        // dd($package);
        return view('auth.subscribers.subscription', compact('package'));
    }

    public function subscriptionSubmit(Request $request, $packageId)
    {
        $request->validate([
            'terms' => 'accepted', // Laravel has built-in rule for this
        ], [
            'terms.accepted' => 'You must accept the Terms & Conditions before proceeding.'
        ]);

        $package = Package::where('id', $packageId)->first();
        $post_data = array();
        $post_data['total_amount'] = $request->amount; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid(); // tran_id must be unique
        $post_data['value_a'] = $packageId;
        $userData = Auth::guard('subscriber')->user();
        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $userData->name;
        $post_data['cus_email'] = $userData->email;
        $post_data['cus_add1'] = $userData->address;
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $userData->mobile;
        $post_data['cus_fax'] = "";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = $package->name;
        $post_data['product_category'] = "Service";
        $post_data['product_profile'] = "digital-goods";

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            $payment_options = array();
        }
    }


    public function success(Request $request)
    {
        $packageId = $request->input('value_a');
        $package = Package::where('id', $packageId)->first();

        $validated['package_id'] = $packageId;
        $validated['subscriber_id'] = $this->subscriberId;
        $validated['subscription_date'] = now();
        $validated['expire_date'] = now()->addDays($package->duration_in_days);
        $validated['fee'] = $request->amount;
        $validated['payment_method'] = "SSLCOMMERZ_" . $request->card_type;
        $validated['transaction_id'] = $request->tran_id;
        $validated['val_id'] = $request->val_id;
        $validated['bank_tran_id'] = $request->bank_tran_id;
        $validated['payment_payload'] = json_encode($request->all());
        $validated['payment_status'] = 'Paid';
        $validated['status'] = 1;
        Subscription::create($validated);

        return redirect()->route('subscriber.dashboard')->with('success', 'Successfylly Subscription done.');
    }

    public function fail(Request $request)
    {
        // $tran_id = $request->input('tran_id');
        return redirect()->back()->with('error', 'Transaction Failed.');
    }
    public function cancel(Request $request)
    {
        // $tran_id = $request->input('tran_id');
        return redirect()->back()->with('error', 'Transaction Cancelled.');
    }
}

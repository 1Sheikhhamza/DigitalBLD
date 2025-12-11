<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Frontend\BaseController;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Services\HomeService;
use Illuminate\Support\Facades\Hash;

class DashboardController extends BaseController
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

    public function dashboard()
    {
        $subscriber = auth('subscriber')->user();
        $bookmarkedDecisions = $subscriber->bookmarks()
    ->with(['decision' => function ($query) {
        $query->select('id', 'parties', 'case_no', 'division', 'judgment')
              ->whereNull('deleted_at'); // only include not deleted
    }])
    ->limit(4)
    ->get()
    ->pluck('decision')
    ->filter(); // remove null values

        $events = Event::where('user_id', $this->subscriberId)->limit(4)->latest()->get();
        $folders = $subscriber->folders()->limit(8)->get();

        return view('auth.subscribers.profile.dashboard', compact('events', 'folders', 'bookmarkedDecisions'));
    }

    public function profile()
    {
        $userProfile = Subscriber::where('id', $this->subscriberId)->first();
        return view('auth.subscribers.profile.my_account', compact('userProfile'));
    }

    public function edit()
    {
        // $user = auth('subscriber')->user();
        $userProfile = Subscriber::where('id', $this->subscriberId)->first();
        // dd($userProfile->photo);
        return view('auth.subscribers.profile.edit_profile', compact('userProfile'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'         => 'required|email|unique:subscribers,email,' . $this->subscriberId,
            'mobile'        => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'registration_as' => 'required|in:Judiciary Person,Lawyer,Student,Other',
            'dob'       => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:512',
            'gender'    => 'nullable|in:Male,Female,Other'
        ]);


        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $validated['photo'] = ImageUploadHelper::upload(
                $request->file('profile_image'),
                'uploads/subscriber/profile',
                'subscriber',
                300,
                300
            );
        }

        unset($validated['profile_image']);
        $validated['name'] = $validated['first_name'].' '.$validated['last_name'];
        Subscriber::where('id', $this->subscriberId)->update($validated);
        return redirect()->route('subscriber.profile.edit')->with('success', 'Profile updated successfully!');
    }


    public function editPassword()
    {
        return view('auth.subscribers.profile.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|min:8|confirmed',
        ]);

        $user = auth('subscriber')->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update to new password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    public function mySubscription()
    {
        $subscriptions = auth('subscriber')->user()->subscriptions()->with('package')->latest()->get();

        return view('auth.subscribers.profile.my_subscription', compact('subscriptions'));
    }
}

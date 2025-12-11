<?php

namespace App\Traits;

use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;

trait UserActivePackage
{
    public function loadSubscriptionData()
    {
        $subscriber = Auth::guard('subscriber')->user();

        if (!$subscriber) {
            return null;
        }

        // Eager load the relation on the existing model instance:
        $subscriber->load('activeSubscription');

        return $subscriber;
    }
}

<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SubscriptionComposer
{
    public function compose(View $view)
    {
        $subscriber = Auth::guard('subscriber')->user();

        if (!$subscriber) {
            return null;
        }

        // Eager load the relation on the existing model instance:
        $hasAnySubscription = $subscriber->load('activeSubscription');
        // Share with view as variable
        $view->with('hasAnySubscription', $hasAnySubscription);
    }
}
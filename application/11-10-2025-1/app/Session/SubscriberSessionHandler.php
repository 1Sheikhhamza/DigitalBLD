<?php

namespace App\Session;

use Illuminate\Session\DatabaseSessionHandler;

class SubscriberSessionHandler extends DatabaseSessionHandler
{
    public function write($sessionId, $data): bool
    {
        // Update subscriber_id if logged in
        if (auth()->guard('subscriber')->check()) {
            $subscriberId = auth()->guard('subscriber')->id();
            $this->getQuery()->where('id', $sessionId)
                ->update(['subscriber_id' => $subscriberId]);
        }

        return parent::write($sessionId, $data);
    }
}

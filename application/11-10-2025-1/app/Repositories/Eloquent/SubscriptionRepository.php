<?php

namespace App\Repositories\Eloquent;

use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    ////////////////// Admin ////////
    public function index()
    {
        return Subscription::with('subscriber', 'package')->orderBy('id', 'desc')->paginate(50);
    }

    public function create(array $data)
    {
        return Subscription::create([
            'subscriber_id'     => $data['subscriber_id'] ?? null,
            'package_id'        => $data['package_id'] ?? null,
            'subscription_date' => $data['subscription_date'] ?? null,
            'expire_date'       => $data['expire_date'] ?? null,
            'fee'               => $data['fee'] ?? 0,
            'payment_method'    => $data['payment_method'] ?? null,
            'transaction_id'    => $data['transaction_id'] ?? null,
            'status'            => $data['status'] ?? 1,  // default active
            'remarks'           => $data['remarks'] ?? null,
        ]);
    }

    public function find($id)
    {
        return Subscription::with('subscriber', 'package')->find($id);
    }

    public function update($id, array $data)
    {
        $subscription = Subscription::findOrFail($id);

        $updateData = [
            'subscriber_id'     => $data['subscriber_id'] ?? $subscription->subscriber_id,
            'package_id'        => $data['package_id'] ?? $subscription->package_id,
            'subscription_date' => $data['subscription_date'] ?? $subscription->subscription_date,
            'expire_date'       => $data['expire_date'] ?? $subscription->expire_date,
            'fee'               => $data['fee'] ?? $subscription->fee,
            'payment_method'    => $data['payment_method'] ?? $subscription->payment_method,
            'transaction_id'    => $data['transaction_id'] ?? $subscription->transaction_id,
            'status'            => $data['status'] ?? $subscription->status,
            'remarks'           => $data['remarks'] ?? $subscription->remarks,
            'updated_at'        => now(),
        ];

        $subscription->update($updateData);

        return true;
    }


    public function delete($id)
    {
        $patientData = Subscription::find($id);

        if (!$patientData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return $patientData->delete(); // Soft delete the record
    }



    public function restore($id)
    {
        $patientData = Subscription::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->restore(); // Restore the record

        return response()->json(['message' => 'Subscription member restored successfully', 'data' => $patientData]);
    }

    public function forceDelete($id)
    {
        $patientData = Subscription::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->forceDelete(); // Permanently delete the record

        return response()->json(['message' => 'Subscription member permanently deleted']);
    }
}

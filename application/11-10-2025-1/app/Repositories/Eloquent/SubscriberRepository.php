<?php

namespace App\Repositories\Eloquent;

use App\Models\Subscriber;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriberRepository implements SubscriberRepositoryInterface
{
    ////////////////// Admin ////////
    public function index()
    {
        return Subscriber::orderBy('id', 'desc')->paginate(50);
    }

    public function create(array $data)
    {
        $subscriberData = [
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'registration_as' => $data['registration_as'] ?? null,
            'dob' => isset($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => isset($data['password']) ? bcrypt($data['password']) : null,
        ];

        $updateData['name'] = $updateData['first_name'].' '.$updateData['last_name'];
        // Handle profile image upload if present
        if (isset($data['profile_image']) && $data['profile_image']->isValid()) {
            $subscriberData['photo'] = $this->uploadProfileImage($data['profile_image']);
        }

        return Subscriber::create($subscriberData);
    }



    public function find($id)
    {
        return Subscriber::find($id);
    }




    public function update($id, array $data)
    {
        $subscriber = Subscriber::findOrFail($id);

        $updateData = [
            'first_name' => $data['first_name'] ?? $subscriber->first_name,
            'last_name' => $data['last_name'] ?? $subscriber->last_name,
            'email' => $data['email'] ?? $subscriber->email,
            'mobile' => $data['mobile'] ?? $subscriber->mobile,
            'registration_as' => $data['registration_as'] ?? $subscriber->registration_as,
            'dob' => isset($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : $subscriber->dob,
            'gender' => $data['gender'] ?? $subscriber->gender,
            'address' => $data['address'] ?? $subscriber->address,
            'updated_at' => now(),
        ];

        $updateData['name'] = $updateData['first_name'].' '.$updateData['last_name'];
        if (!empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        // Handle profile image upload if present
        if (isset($data['profile_image']) && $data['profile_image']->isValid()) {
            $updateData['photo'] = $this->uploadProfileImage($data['profile_image']);
        }

        $subscriber->update($updateData);
        return true;
    }

    /**
     * Handle profile image upload and return filename
     */
    protected function uploadProfileImage($imageFile)
    {
        $filename = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move(public_path('uploads/subscriber/profile'), $filename);
        return $filename;
    }


    public function delete($id)
    {
        $patientData = Subscriber::find($id);

        if (!$patientData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return $patientData->delete(); // Soft delete the record
    }



    public function restore($id)
    {
        $patientData = Subscriber::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->restore(); // Restore the record

        return response()->json(['message' => 'Subscriber member restored successfully', 'data' => $patientData]);
    }

    public function forceDelete($id)
    {
        $patientData = Subscriber::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->forceDelete(); // Permanently delete the record

        return response()->json(['message' => 'Subscriber member permanently deleted']);
    }
}

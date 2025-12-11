<?php

namespace App\Repositories\Eloquent;

use App\Models\Inquiry;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use Illuminate\Support\Str;

class InquiryRepository implements InquiryRepositoryInterface
{
    public function index()
    {
        return Inquiry::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Inquiry::create([
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'] ?? '',
            'status' => $data['status'] ?? 0,
        ]);
    }


    public function find($id)
    {
        return Inquiry::find($id);
    }

    public function update($id, array $data)
    {
        $inquiry = Inquiry::findOrFail($id);

        $inquiry->update([
            'name' => $data['name'] ?? $inquiry->name,
            'email' => $data['email'] ?? $inquiry->email,
            'phone' => $data['phone'] ?? $inquiry->phone,
            'subject' => $data['subject'] ?? $inquiry->subject,
            'message' => $data['message'] ?? $inquiry->message,
            'status' => $data['status'] ?? $inquiry->status,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Inquiry::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Inquiry::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Inquiry::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}

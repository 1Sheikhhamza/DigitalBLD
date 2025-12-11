<?php

namespace App\Repositories\Eloquent;

use App\Models\ClientFeedback;
use App\Repositories\Contracts\ClientFeedbackRepositoryInterface;
use Illuminate\Support\Str;

class ClientFeedbackRepository implements ClientFeedbackRepositoryInterface
{
    public function index()
    {
        return ClientFeedback::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return ClientFeedback::create([
            'client_name'      => $data['client_name'] ?? '',
            'client_position'  => $data['client_position'] ?? null,
            'feedback'         => $data['feedback'] ?? '',
            'client_photo'     => $data['client_photo'] ?? null,
            'rating'           => $data['rating'] ?? 5,
            'company'          => $data['company'] ?? null,
            'website'          => $data['website'] ?? null,
            'status'           => $data['status'] ?? 1,
        ]);
    }



    public function find($id)
    {
        return ClientFeedback::find($id);
    }

    // Update method for ClientFeedback
    public function update($id, array $data)
    {
        $feedback = ClientFeedback::findOrFail($id);

        $feedback->update([
            'client_name'      => $data['client_name'] ?? $feedback->client_name,
            'client_position'  => $data['client_position'] ?? $feedback->client_position,
            'feedback'         => $data['feedback'] ?? $feedback->feedback,
            'client_photo'     => $data['client_photo'] ?? $feedback->client_photo,
            'rating'           => $data['rating'] ?? $feedback->rating,
            'company'          => $data['company'] ?? $feedback->company,
            'website'          => $data['website'] ?? $feedback->website,
            'status'           => $data['status'] ?? $feedback->status,
        ]);

        return true;
    }

    public function delete($id)
    {
        $package = ClientFeedback::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = ClientFeedback::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = ClientFeedback::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}

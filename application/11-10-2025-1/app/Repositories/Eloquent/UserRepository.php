<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    ////////////////// Admin ////////
    public function index()
    {
        return User::with('roles:id,name')->orderBy('id', 'desc')->paginate(50);
    }

    public function create($data)
    {
        $user = User::create([
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'dob' => $data['date_of_birth'] ?? null,
            'email' => $data['email'] ?? '',
            'mobile' => $data['phone_no'] ?? '',
            'gender' => $data['gender'] ?? null,
            'status' => 1,
            'password' => bcrypt('patient-1234'),
            'user_type' => $data['user_type'],
            'created_by' => $data['created_by'] ?? 1,
            'updated_by' => $data['updated_by'] ?? null,
        ]);
        $user->syncRoles($data['roles']);

        return $user;
    }



    public function find($id)
    {
        return User::find($id);
    }




    public function update($id, array $data)
    {
        $patientData = User::findOrFail($id);

        $menuUpdate = [
            'first_name'    => $data['first_name'] ?? '',
            'last_name'     => $data['last_name'] ?? '',
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'email'         => $data['email'] ?? '',
            'phone_no'      => $data['phone_no'] ?? '',
            'gender'        => $data['gender'] ?? null,
            'status'        => $data['status'] ?? 1,
            'updated_at'    => now()
        ];

        // First update the user
        $patientData->update($menuUpdate);

        // Then sync roles after update
        $patientData->syncRoles($data['roles']);

        return true;
    }


    public function deleteUser($id)
    {
        $patientData = User::find($id);

        if (!$patientData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return $patientData->delete(); // Soft delete the record
    }



    public function restoreUser($id)
    {
        $patientData = User::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->restore(); // Restore the record

        return response()->json(['message' => 'User member restored successfully', 'data' => $patientData]);
    }

    public function forceDeleteUser($id)
    {
        $patientData = User::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->forceDelete(); // Permanently delete the record

        return response()->json(['message' => 'User member permanently deleted']);
    }
}

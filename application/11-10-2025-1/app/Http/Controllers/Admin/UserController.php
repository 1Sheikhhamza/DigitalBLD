<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Services\CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    public function __construct(
        protected UserService $service,
        protected CommonService $commonService
    ) {}

    public function index()
    {
        $listData = $this->service->index();
        return view('admin.user.index', compact('listData'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.user.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();
        /* $validatedData = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone_no'      => 'required|unique:users,mobile',
            'email'         => 'required|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'gender'        => 'required|in:Male,Female,Other,Not Declare',
            'password'      => 'required|min:6',
        ]); */

        // Hash the password before saving
        $validatedData['password'] = Hash::make($validatedData['password']);

        $data = $request->except(['_token', '_method']);
        $data = array_merge($data, $validatedData);

        $this->service->store($data);

        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    public function show(int $id)
    {
        return response()->json($this->service->find($id));
    }

    public function edit(int $id)
    {
        $user = $this->service->find($id);
        $roles = Role::all();
        $hasRoles = $user->roles->pluck('id');
        return view('admin.user.edit', compact('user', 'roles', 'hasRoles'));
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone_no'      => 'required|string|max:20|unique:users,mobile,' . $id,
            'email'         => 'required|email|unique:users,email,' . $id,
            'date_of_birth' => 'required|date',
            'gender'        => 'required|in:Male,Female,Other,Not Declare',
            'password'      => 'nullable|string|min:6', // Optional password field
        ]);

        $data = $request->except(['_token', '_method']);

        // Hash password if it's present
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Avoid updating password if left empty
        }

        $data['mobile'] = $data['phone_no']; // if your DB uses 'mobile'
        unset($data['phone_no']);

        $data = array_merge($data, $validatedData);

        $this->service->update($id, $data);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\UserManual;
use Exception;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;



class UserManualController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $alluser_manual = UserManual::orderBy('id', 'desc')->paginate(20);
        return view('admin.user_manual.index', compact('alluser_manual'));
    }


    public function create()
    {
        return view('admin.user_manual.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:100',
            'question' => 'nullable|string|max:250',
            'answer' => 'nullable|string',
            'sequence' => 'nullable|integer',
            'status' => 'nullable|integer',
        ]);

        $user_manual = new UserManual();
        $user_manual->topic = $request->input('topic');
        $user_manual->question = $request->input('question');
        $user_manual->answer = $request->input('answer');
        $user_manual->sequence = $request->input('sequence');
        $user_manual->status = $request->input('status');
        $user_manual->save();

        return redirect()->route('user_manual.index')->with('success', 'FAQ created successfully.');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $user_manual = UserManual::find($id);
        return view('admin.user_manual.edit', compact('user_manual'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'topic' => 'required|string|max:100',
            'question' => 'nullable|string|max:250',
            'answer' => 'nullable|string',
            'sequence' => 'nullable|integer',
            'status' => 'nullable|integer',
        ]);

        $user_manual = UserManual::findOrFail($id);
        $user_manual->topic = $request->input('topic');
        $user_manual->question = $request->input('question');
        $user_manual->answer = $request->input('answer');
        $user_manual->sequence = $request->input('sequence');
        $user_manual->status = $request->input('status');
        $user_manual->save();

        return redirect()->route('user_manual.index')->with('success', 'FAQ updated successfully.');
    }


    public function destroy($id)
    {
        $menuItem = UserManual::find($id);
        $menuItem->delete();
        return redirect('admin/user_manual');
    }

    public function imageResize($file, $path, $filename, $width, $height)
    {
        $img = Image::make($file);
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->resizeCanvas($width, $height, 'center', false, array(255, 255, 255, 0));
        $img->save($path);
    }
}

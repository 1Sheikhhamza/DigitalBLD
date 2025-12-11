<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Faq;
use Exception;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;



class FaqController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $allfaq = Faq::orderBy('id', 'desc')->paginate(20);
        return view('admin.faq.index', compact('allfaq'));
    }


    public function create()
    {
        return view('admin.faq.create');
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

        $faq = new Faq();
        $faq->topic = $request->input('topic');
        $faq->question = $request->input('question');
        $faq->answer = $request->input('answer');
        $faq->sequence = $request->input('sequence');
        $faq->status = $request->input('status');
        $faq->save();

        return redirect()->route('faq.index')->with('success', 'FAQ created successfully.');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $faq = Faq::find($id);
        return view('admin.faq.edit', compact('faq'));
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

        $faq = Faq::findOrFail($id);
        $faq->topic = $request->input('topic');
        $faq->question = $request->input('question');
        $faq->answer = $request->input('answer');
        $faq->sequence = $request->input('sequence');
        $faq->status = $request->input('status');
        $faq->save();

        return redirect()->route('faq.index')->with('success', 'FAQ updated successfully.');
    }


    public function destroy($id)
    {
        $menuItem = Faq::find($id);
        $menuItem->delete();
        return redirect('admin/faq');
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

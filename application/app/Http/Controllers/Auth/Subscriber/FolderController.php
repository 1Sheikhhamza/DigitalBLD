<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\DecisionShare;
use App\Models\Folder;
use App\Models\FolderFile;
use App\Models\LegalDecisionUserNote;
use App\Models\OCRExtraction;
use App\Models\UserFolderDecision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\HomeService;
use Illuminate\Validation\Rule;
use ZipArchive;

class FolderController extends BaseController
{

    protected $subscriberId;

    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
        $this->middleware(function ($request, $next) {
            $this->subscriberId = auth('subscriber')->id();
            return $next($request);
        });
    }

    public function index(Request $request)
    {


        $query = Folder::withCount(['userFolderDecisions as decision_count' => function ($query) {
            $query->where('user_id', $this->subscriberId);
        }])->where('user_id', $this->subscriberId);

        if (isset($request->folder)) {
            $query->where('name', $request->folder);
        }

        $folders = $query->get();

        $sharedDecisions = DecisionShare::where('receiver_id', $this->subscriberId)->count();
        return view('auth.subscribers.profile.my_folders', compact('folders', 'sharedDecisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('folders')
                    ->where(function ($query) {
                        return $query->whereNull('deleted_at')
                            ->where('user_id', $this->subscriberId);
                    }),
            ],
        ]);

        Folder::create([
            'name' => $request->name,
            'user_id' => $this->subscriberId,
        ]);

        return back()->with('success', 'Folder created successfully!');
    }


    public function update(Request $request, $id)
    {
        $folder = Folder::findOrFail($id);
        if ($folder->user_id !== $this->subscriberId) abort(403);

        $request->validate(['name' => 'required|string|unique:folders,name,' . $folder->id . ',id,user_id,' . $this->subscriberId]);

        $folder->update(['name' => $request->name]);

        return redirect()->route('subscriber.myFolder')->with('success', 'Folder updated successfully!');
    }

    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        if ($folder->user_id !== $this->subscriberId) abort(403);

        $folder->delete();
        return back()->with('success', 'Folder deleted!');
    }

    public function downloadFolder($id)
    {
        $id = Crypt::decrypt($id);
        $folder = Folder::where('id', $id)
            ->where('user_id', $this->subscriberId)
            ->with('files')
            ->firstOrFail();

        if ($folder->files->isEmpty()) {
            return back()->withErrors(['folder' => 'This folder has no files.']);
        }

        $zip = new ZipArchive();
        $zipFileName = $folder->name . '_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($folder->files as $file) {
                $filePath = storage_path('app/' . $file->path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->name);
                }
            }
            $zip->close();
        } else {
            return back()->withErrors(['folder' => 'Could not create zip file.']);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function copyDecisionToFolder(Request $request)
    {
        $request->validate([
            'decision_id' => 'required|exists:ocr_extractions,id',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $userId = auth('subscriber')->id();
        $exists = UserFolderDecision::where('user_id', $userId)
            ->where('decision_id', $request->decision_id)
            ->where('folder_id', $request->folder_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Already in this folder.');
        }

        UserFolderDecision::create([
            'user_id' => $userId,
            'decision_id' => $request->decision_id,
            'folder_id' => $request->folder_id,
        ]);

        $decision = OcrExtraction::find($request->decision_id);
        LegalDecisionUserNote::updateOrCreate(
            [
                'user_id' => $userId,
                'decision_id' => $request->decision_id,
            ],
            [
                'notes' => $decision->judgment ?? 'No content available.',
            ]
        );

        return back()->with('success', 'Decision copied to folder and note saved successfully.');
    }

    public function removeDecisionFromFolder(Request $request)
    {
        $request->validate([
            'decision_id' => 'required|exists:ocr_extractions,id',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $deleted = UserFolderDecision::where('user_id', $this->subscriberId)
            ->where('decision_id', $request->decision_id)
            ->where('folder_id', $request->folder_id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message'   => 'Decision removed from folder successfully.',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message'   => 'Decision not found in this folder.',
            ], 422);
        }
    }
}

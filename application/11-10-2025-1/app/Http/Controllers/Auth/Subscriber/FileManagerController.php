<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\DecisionShare;
use App\Models\Folder;
use App\Models\FolderFile;
use App\Models\OCRExtraction;
use App\Models\UserFolderDecision;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use App\Services\HomeService;
use ZipArchive;

class FileManagerController extends BaseController
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

    public function index($encryptedFolderId)
    {
        $folderId = Crypt::decrypt($encryptedFolderId);
        $folder = Folder::where('id', $folderId)->where('user_id', $this->subscriberId)->firstOrFail();
        $copiedDecisions = UserFolderDecision::where('user_id', $this->subscriberId)->where('folder_id', $folder->id)
            ->with('decision', 'folder')
            ->latest()
            ->get();

        return view('auth.subscribers.profile.my_folder_files', compact('folder', 'copiedDecisions', 'encryptedFolderId'));
    }

    public function sharedDecision()
    {
        $sharedDecisions = DecisionShare::where('receiver_id', $this->subscriberId)
            ->with('decision', 'sender')
            ->latest()
            ->get();

        return view('auth.subscribers.profile.my_shared_decisions', compact('sharedDecisions'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,svg,webp,doc,docx,xls,xlsx,zip,rar',
                'mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,
            application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
            application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
            application/zip,application/x-rar-compressed',
                'max:20480',
            ],
            'folder_id' => 'required|exists:folders,id',
        ]);

        $folder = Folder::where('id', $request->folder_id)
            ->where('user_id', $this->subscriberId)
            ->firstOrFail();

        $originalName = $request->file->getClientOriginalName();
        $extension = $request->file->getClientOriginalExtension();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        // Check for same name in folder
        $fileName = $originalName;
        $counter = 1;

        while ($folder->files()->where('name', $fileName)->exists()) {
            $fileName = $baseName . " ($counter)." . $extension;
            $counter++;
        }

        // Save physical file with unique hashed name (for security)
        $storageName = uniqid() . '_' . time() . '.' . $extension;
        $path = $request->file('file')->storeAs('secure_uploads', $storageName, 'private');

        // Save DB record
        $fileModel = new FolderFile();
        $fileModel->user_id = $this->subscriberId;
        $fileModel->folder_id = $folder->id;
        $fileModel->name = $fileName; // The final unique name shown to user
        $fileModel->path = $path;
        $fileModel->size = $request->file->getSize();
        $fileModel->type = $request->file->getClientMimeType();
        $fileModel->save();

        return back()->with('success', 'File uploaded successfully!');
    }





    public function move(Request $request)
    {
        $file = FolderFile::where('id', $request->file_id)->where('user_id', $this->subscriberId)->firstOrFail();
        $file->folder_id = $request->target_folder_id;
        $file->save();

        return back()->with('success', 'File moved!');
    }

    public function copy(Request $request)
    {
        $file = FolderFile::where('id', $request->file_id)->where('user_id', $this->subscriberId)->firstOrFail();
        $newFile = $file->replicate();
        $newFilePath = 'uploads/copies/' . time() . '_' . $file->name;
        Storage::copy($file->path, $newFilePath);
        $newFile->path = $newFilePath;
        $newFile->folder_id = $request->target_folder_id;
        $newFile->save();

        return back()->with('success', 'File copied!');
    }

    public function duplicate(Request $request)
    {
        $file = FolderFile::where('id', $request->file_id)->where('user_id', $this->subscriberId)->firstOrFail();
        $newFile = $file->replicate();
        $newFilePath = 'uploads/copies/' . time() . '_' . $file->name;
        Storage::copy($file->path, $newFilePath);
        $newFile->path = $newFilePath;
        $newFile->save();

        return back()->with('success', 'File duplicated in same folder!');
    }

    public function destroy($id)
    {
        $file = FolderFile::where('id', $id)->where('user_id', $this->subscriberId)->firstOrFail();
        Storage::delete($file->path);
        $file->delete();

        return back()->with('success', 'File deleted!');
    }

    public function downloadFile($id)
    {
        $file = FolderFile::where('id', $id)
            ->where('user_id', $this->subscriberId)
            ->firstOrFail();

        $path = storage_path('app/' . $file->path);

        return response()->download($path, $file->name);
    }

    public function downloadMultiple(Request $request)
    {
        $fileIds = $request->input('file_ids', []);

        $files = FolderFile::whereIn('id', $fileIds)
            ->where('user_id', $this->subscriberId)
            ->get();

        if ($files->isEmpty()) {
            return back()->withErrors(['files' => 'No files selected or files not found.']);
        }

        $zip = new ZipArchive();
        $zipFileName = 'files_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $filePath = storage_path('app/' . $file->path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->name);
                }
            }
            $zip->close();
        } else {
            return back()->withErrors(['files' => 'Could not create zip file.']);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:folder_files,id',
        ]);

        $files = FolderFile::whereIn('id', $request->file_ids)
            ->where('user_id', $this->subscriberId)
            ->get();

        foreach ($files as $file) {
            // Delete file from storage
            Storage::delete($file->path);

            // Delete DB record
            $file->delete();
        }

        return back()->with('success', 'Selected files deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\VolumeRequest;
use App\Services\VolumeService;
use Illuminate\Http\Request;
use App\Jobs\ProcessOCRExtraction;
use Illuminate\Support\Facades\Storage;

class VolumeController extends Controller
{
    protected VolumeService $volumeService;

    public function __construct(VolumeService $volumeService)
    {
        $this->volumeService = $volumeService;
    }

    public function index()
    {
        $volumes = $this->volumeService->index();
        return view('admin.volumes.index', compact('volumes'));
    }

    public function create()
    {
        return view('admin.volumes.create');
    }

    /*public function store(VolumeRequest $request)
    {
        // Start from here
        $validated = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/volume',
                'volumes',
                300,
                null,
            );
        }

        // Handle index_file (pdf) upload
        if ($request->hasFile('index_file') && $request->file('index_file')->isValid()) {
            $pdfFile = $request->file('index_file');
            $fileName = uniqid('volume_') . '.' . $pdfFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/volume/pdfs');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $pdfFile->move($destinationPath, $fileName);
            $validated['index_file'] = $fileName;
        }

        // Handle OCR document upload and add metadata
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $documentFile = $request->file('document');
            $documentPath = $documentFile->store('documents');
            $validated['document_path'] = $documentPath;
            $validated['document_name'] = $documentFile->getClientOriginalName();
            $validated['document_size'] = $documentFile->getSize();
            $validated['document_mimetype'] = $documentFile->getMimeType();
        } else {
            return response()->json(['errors' => ['document' => ['Document is required.']]], 422);
        }
        
        // Create volume record
        $volume = $this->volumeService->create($validated);
        $message = "OCR processing completed successfully.";
        // Dispatch OCR job with volume id and document info
        ProcessOCRExtraction::dispatch($volume->id, $validated['year'], $validated['number'], $validated['document_path'], $documentFile->getClientOriginalExtension(), $message);

        // Return success response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Volume created and OCR processing started!'
        ]);
    }*/
    
    
    public function store(VolumeRequest $request)
    {
        // Start from here
        $validated = $request->validated();
        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/volume',
                'volumes',
                300,
                null,
            );
        }

        // Handle index_file (pdf) upload
        if ($request->hasFile('index_file') && $request->file('index_file')->isValid()) {
            $pdfFile = $request->file('index_file');
            $fileName = uniqid('volume_index_') . '.' . $pdfFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/volume/pdfs');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $pdfFile->move($destinationPath, $fileName);
            $validated['index_file'] = $fileName;
        }

        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $documentFile = $request->file('document');

            // Capture metadata before moving
            $originalName = $documentFile->getClientOriginalName();
            $mimeType     = $documentFile->getMimeType();
            $fileSize     = $documentFile->getSize();

            $fileName = uniqid('doc_') . '.' . $documentFile->getClientOriginalExtension();
            $destinationPath = storage_path('app/documents'); // outside public

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move the file
            $documentFile->move($destinationPath, $fileName);

            // Save validated values
            $validated['document_path'] = "documents/{$fileName}";
            $validated['document_name'] = $originalName;
            $validated['document_size'] = $fileSize;
            $validated['document_mimetype'] = $mimeType;
        } else {
            return response()->json(['errors' => ['document' => ['Document is required.']]], 422);
        }

        // Create volume record
        $volume = $this->volumeService->create($validated);
        $message = "OCR processing completed successfully.";

        // Instant dispatchSync OCR data except queue jobs for testing only
        // ProcessOCRExtraction::dispatchSync($volume->id, $validated['year'], $validated['number'], $validated['document_path'], $documentFile->getClientOriginalExtension(), $message);
        
        
        ProcessOCRExtraction::dispatch($volume->id, $validated['year'], $validated['number'], $validated['document_path'], $documentFile->getClientOriginalExtension(), $message);
        return response()->json([
            'success' => true,
            'message' => 'Volume created and OCR processing started!'
        ]);
    }



    public function show($id)
    {
        $volume = $this->volumeService->find($id);
        return view('admin.volumes.show', compact('volume'));
    }

    public function edit($id)
    {
        $volume = $this->volumeService->find($id);
        return view('admin.volumes.edit', compact('volume'));
    }

    public function update(VolumeRequest $request, $id)
    {
        $currentVolume = $this->volumeService->find($id);
        $validated = $request->validated();

        // Handle image update
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/volume',
                'volumes',
                300,
                null,
                $currentVolume->image
            );
        }

        // Handle PDF update
        if ($request->hasFile('index_file') && $request->file('index_file')->isValid()) {
            $pdfFile = $request->file('index_file');
            $fileName = uniqid('volume_') . '.' . $pdfFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/volume/pdfs');

            // Create directory if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $pdfFile->move($destinationPath, $fileName);

            // Remove old PDF file if exists
            if (!empty($currentVolume->index_file) && file_exists(public_path($currentVolume->index_file))) {
                unlink(public_path($currentVolume->index_file));
            }

            // Save new relative path
            $validated['index_file'] = $fileName;
        }

        $this->volumeService->update($id, $validated);

        return redirect()->route('volumes.index')->with('success', 'Volume updated successfully.');
    }


    public function destroy($id)
    {
        $this->volumeService->delete($id);
        return redirect()->route('volumes.index')->with('success', 'Volume soft deleted.');
    }

    public function restore($id)
    {
        $this->volumeService->restore($id);
        return redirect()->route('volumes.index')->with('success', 'Volume restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->volumeService->forceDelete($id);
        return redirect()->route('volumes.index')->with('success', 'Volume permanently deleted.');
    }

    public function downloadDocument($path, $originalName)
    {
        $filePath = $path;

        if (!Storage::exists($filePath)) {
            abort(404, 'File not found.');
        }

        // Use Storage::download with custom filename
        return Storage::download($filePath, $originalName);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OCRExtractionRequest;
use App\Http\Requests\OCRExtractionUpdateRequest;
use App\Services\CommonService;
use App\Services\OCRExtractionService;
use Illuminate\Http\Request;
use App\Jobs\ProcessOCRExtraction;
use App\Models\OCRExtraction;
use Illuminate\Support\Facades\DB;
use Laravel\Reverb\Loggers\Log;

class OCRExtractionController extends Controller
{
    protected OCRExtractionService $ocrExtractionService;
    protected CommonService $commonService;

    public function __construct(OCRExtractionService $ocrExtractionService, CommonService $commonService)
    {
        $this->ocrExtractionService = $ocrExtractionService;
        $this->commonService = $commonService;
    }

    /* public function index()
    {
        $ocr_extractions = $this->ocrExtractionService->index();
        $volumeList = $this->commonService->getVolume();
        return view('admin.ocr_extractions.index', compact('ocr_extractions','volumeList'));
    } */

    public function index(Request $request)
    {
        $filters = [
            'case_no' => $request->get('case_no'),
            'parties' => $request->get('parties'),
            'division' => $request->get('division'),
            'volume_id' => $request->get('volume_id'),
            'published_year' => $request->get('published_year'),
            'judgename' => $request->get('judgename'),
            'homepage' => $request->get('homepage'),
            'division_type' => $request->get('division_type'),
        ];

        $ocr_extractions = $this->ocrExtractionService->index($filters);
        $volumeList = $this->commonService->getVolume();

        return view('admin.ocr_extractions.index', compact('ocr_extractions', 'volumeList', 'filters'));
    }



    public function create()
    {
        $volumeList = $this->commonService->getVolume();
        return view('admin.ocr_extractions.create', compact('volumeList'));
    }

    public function createScob()
    {
        $volumeList = $this->commonService->getVolume();
        return view('admin.ocr_extractions.create_scob', compact('volumeList'));
    }

    public function store(OCRExtractionUpdateRequest $request)
    {
        $validated = $request->validated();

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

        // Debug logging for file_path
        if (isset($validated['file_path'])) {
            \Illuminate\Support\Facades\Log::info("OCRExtraction Store: file_path present: " . $validated['file_path']);
        } else {
            \Illuminate\Support\Facades\Log::warning("OCRExtraction Store: file_path MISSING in validated data");
            if ($request->has('file_path')) {
                \Illuminate\Support\Facades\Log::info("OCRExtraction Store: file_path present in Request but not Validated: " . $request->input('file_path'));
                // Manually add it back if validation stripped it (shouldn't if rule exists)
                $validated['file_path'] = $request->input('file_path');
            } else {
                \Illuminate\Support\Facades\Log::error("OCRExtraction Store: file_path completely missing from input");
            }
        }

        $this->ocrExtractionService->create($validated);

        // Redirect back to the list with success message
        return redirect()->route('ocr_extractions.index')
            ->with('success', 'OCRExtraction created successfully.');
    }

    /* 
    public function store(OCRExtractionRequest $request)
    {
        $file = $request->file('document');
        $path = $file->store('documents');
        $extension = $file->getClientOriginalExtension();

        ProcessOCRExtraction::dispatch(1, $path, $extension);

        return redirect()->route('ocr_extractions.index')
            ->with('success', 'File uploaded successfully! OCR is processing in the background.');
    } */

    public function show($id)
    {
        $ocrExtraction = $this->ocrExtractionService->find($id);
        $judgmentTextFormatted = $this->judgmentText($ocrExtraction->judgment);
        return view('admin.ocr_extractions.show', compact('ocrExtraction', 'judgmentTextFormatted'));
    }

    private function judgmentText($text)
    {

        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Break text into lines
        $lines = explode("\n", $text);

        $paragraph = '';
        $paragraphs = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                // If empty line (rare), consider it a paragraph separator
                if (!empty($paragraph)) {
                    $paragraphs[] = $paragraph;
                    $paragraph = '';
                }
            } else {
                // Append line with a space
                $paragraph .= $trimmed . ' ';
            }
        }

        // Add last paragraph
        if (!empty($paragraph)) {
            $paragraphs[] = $paragraph;
        }

        // Now wrap each paragraph
        $judgmentTextFormatted = '';
        foreach ($paragraphs as $para) {
            // $judgmentTextFormatted .= '<p>' . e(trim($para)) . '</p>';
            $judgmentTextFormatted .= '<p>' . trim($para) . '</p>';
        }

        return $judgmentTextFormatted;
    }

    public function edit($id)
    {
        $ocrExtraction = $this->ocrExtractionService->find($id);
        $volumeList = $this->commonService->getVolume();
        return view('admin.ocr_extractions.edit', compact('ocrExtraction', 'volumeList'));
    }

    public function update(OCRExtractionUpdateRequest $request, $id)
    {
        $validated = $request->validated();

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

        $this->ocrExtractionService->update($id, $validated);
        return redirect()->route('ocr_extractions.index')->with('success', 'OCRExtraction updated successfully.');
    }

    public function destroy($id)
    {
        $this->ocrExtractionService->delete($id);
        return redirect()->route('ocr_extractions.index')->with('success', 'OCRExtraction soft deleted.');
    }

    public function restore($id)
    {
        $this->ocrExtractionService->restore($id);
        return redirect()->route('ocr_extractions.index')->with('success', 'OCRExtraction restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->ocrExtractionService->forceDelete($id);
        return redirect()->route('ocr_extractions.index')->with('success', 'OCRExtraction permanently deleted.');
    }

    public function uploadScobPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // 10MB max
        ]);

        try {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $file = $request->file('file');
                $filename = uniqid('scob_') . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('storage/scob'); // Ensure this matches Ingest command path or standard path

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);
                $filePath = $destinationPath . '/' . $filename;
                $publicPath = 'storage/scob/' . $filename;

                // Extract Text
                // Note: We need to use extractText from Service, which handles PDF parsing
                // Since extractText is public in Service, we can use it.
                // Assuming extension is 'pdf'
                $text = $this->ocrExtractionService->extractText($filePath, 'pdf');

                // Extract Metadata
                // Use AI for better extraction
                $metadata = $this->ocrExtractionService->extractMetadataWithAi($text);

                return response()->json([
                    'success' => true,
                    'file_path' => $publicPath,
                    'judgment_text' => $text,
                    'metadata' => $metadata
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("SCOB Upload Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => false, 'message' => 'Upload failed'], 400);
    }
}


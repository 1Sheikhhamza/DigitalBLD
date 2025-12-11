<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\Bookmark;
use App\Models\DecisionComment;
use App\Models\DecisionShare;
use App\Models\Folder;
use App\Models\LegalDecisionUserNote;
use App\Models\OCRExtraction;
use App\Models\Subscriber;
use App\Models\UserFolderDecision;
use App\Models\Volume;
use App\Services\CommonService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\HomeService;
use Illuminate\Support\Facades\DB;

class LegalSearchController extends BaseController
{
    protected CommonService $commonService;
    protected $subscriberId;

    public function __construct(CommonService $commonService, HomeService $homeService)
    {
        parent::__construct($homeService);
        // $this->middleware('auth:subscriber');
        $this->commonService = $commonService;
        $this->middleware(function ($request, $next) {
            $this->subscriberId = auth('subscriber')->id();
            return $next($request);
        });
    }

    public function leagalSearch(Request $request)
    {
        // If URL has `?new=1`, clear session
        if ($request->has('new') && $request->new == 1) {
            session()->forget('search_inputs');
        }

        // Get old inputs from session (if any)
        $inputs = session('search_inputs', []);
        $fillingYears = OCRExtraction::selectRaw("DISTINCT RIGHT(case_no, 4) as year")
            ->whereRaw("RIGHT(case_no, 4) REGEXP '^[0-9]{4}$'")
            ->whereBetween(DB::raw("RIGHT(case_no, 4)"), ['1900', '2099'])
            ->orderBy('year', 'ASC')
            ->pluck('year');

        $judgmentYear = OCRExtraction::selectRaw("DISTINCT REGEXP_SUBSTR(decided_on, '(19|20)[0-9]{2}') as year")
            ->whereRaw("decided_on REGEXP '(19|20)[0-9]{2}'")
            ->orderBy('year', 'ASC')
            ->pluck('year');


        $volumeList = $this->commonService->getVolume();
        $getJurisdiction = $this->commonService->getJurisdiction();
        return view('auth.subscribers.profile.legal_search', compact('inputs', 'fillingYears', 'judgmentYear', 'volumeList', 'getJurisdiction'));
    }


    public function handleSearch(Request $request)
    {
        session(['search_inputs' => $request->except('_token')]);
        return redirect()->route('subscriber.showResults');
    }

    public function showResults(Request $request)
    {

        $results = $this->searchResult($request);
        $inputs = session('search_inputs', []);
        if (isset($inputs['volume_number']) && $inputs['volume_number'] != '') {
            $inputs['volume_number'] = Volume::where('id', $inputs['volume_number'])->value('number');
        }
        $searchInputParams = $this->getInputParams($inputs);

        if (!$results) {
            return redirect()->route('auth.subscribers.profile.legal_search')->with('message', 'Please perform a search first.');
        }

        return view('auth.subscribers.profile.search_result', compact('results', 'searchInputParams'));
    }

    private function searchResult(Request $request)
    {
        $criteria = session('search_inputs', []);
        $query = OcrExtraction::query();

        // Keyword search
        if (!empty($criteria['searchKeyword'])) {
            $keyword = $criteria['searchKeyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('judgment', 'LIKE', "%{$keyword}%")
                    ->orWhere('key_words', 'LIKE', "%{$keyword}%")
                    ->orWhere('subject', 'LIKE', "%{$keyword}%");
            });
        }

        if (!empty($criteria['division'])) {
            $query->where('division', $criteria['division']);
        }

        if (!empty($criteria['filling_year'])) {
            $query->whereRaw("REGEXP_SUBSTR(case_no, '(19|20)[0-9]{2}') = ?", [$criteria['filling_year']]);
        }

        // Judgment Year filter (from decided_on)

        if (!empty($criteria['judgment_year'])) {
            $query->whereRaw("REGEXP_SUBSTR(decided_on, '(19|20)[0-9]{2}') = ?", [$criteria['judgment_year']]);
        }

        if (!empty($criteria['published_year'])) {
            $query->where('published_year', $criteria['published_year']);
        }

        if (!empty($criteria['section_subsection'])) {
            $query->where('sections_subsections', 'LIKE', '%' . $criteria['section_subsection'] . '%');
        }

        if (!empty($criteria['judges'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('judge_name', 'LIKE', '%' . $criteria['judges'] . '%');
            });
        }


        if (!empty($criteria['judgment_month'])) {
            $query->where('published_month', $criteria['judgment_month']);
        }

        if (!empty($criteria['keywords'])) {
            $query->where('key_words', 'LIKE', '%' . $criteria['keywords'] . '%');
        }

        if (!empty($criteria['act_rule_name'])) {
            $query->where('related_act_order_rule', 'LIKE', '%' . $criteria['act_rule_name'] . '%');
        }

        /* if (!empty($criteria['petitioners'])) {
            $query->where('petitioners', 'LIKE', '%' . $criteria['petitioners'] . '%');
        }

        if (!empty($criteria['respondent'])) {
            $query->where('respondent', 'LIKE', '%' . $criteria['respondent'] . '%');
        } */


        if (!empty($criteria['council'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('petitioners', 'LIKE', '%' . $criteria['council'] . '%')
                    ->orWhere('respondent', 'LIKE', '%' . $criteria['council'] . '%');
            });
        }

        if (!empty($criteria['parties'])) {
            $query->where('parties', 'LIKE', '%' . $criteria['parties'] . '%');
        }

        if (!empty($criteria['volume_number'])) {
            $query->where('volume_id', $criteria['volume_number']);
        }

        if (!empty($criteria['subject'])) {
            $query->where('subject', 'LIKE', '%' . $criteria['subject'] . '%');
        }

        if (!empty($criteria['case_number'])) {
            $query->where('case_no', 'LIKE', '%' . $criteria['case_number'] . '%');
        }

        if (!empty($criteria['jurisdiction'])) {
            $query->where('jurisdiction', $criteria['jurisdiction']);
        }

        if (!empty($criteria['page_number'])) {
            $page = (int)$criteria['page_number'];
            $query->where(function ($q) use ($page) {
                $q->where('starting_page_no', '<=', $page)
                    ->where('ending_page_no', '>=', $page);
            });
        }

        /* if (!empty($criteria['publication_year_range'])) {
            [$startPubYear, $endPubYear] = explode('-', $criteria['publication_year_range']);
            $query->whereBetween('published_year', [(int)$startPubYear, (int)$endPubYear]);
        } */

        // Paginate and keep query string
        return $query->paginate(30)->withQueryString();
    }

    private function getInputParams($criteria)
    {
        $filtered = array_filter($criteria, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return $filtered;
        /* $formatted = [];
        foreach ($filtered as $key => $value) {
            $formatted[] = "$key: $value";
        }
        
        // Convert to comma-separated string
        $resultString = implode(', ', $formatted);
        
        // If you want JSON object
        // $resultJson = json_encode($filtered);
        return $resultString; */
    }

    public function singleDecision($id, $returnParam = null, Request $request)
    {
        $returnParamString = Crypt::decrypt($returnParam);
        $page = $request->query('page');

        $isBookmarked = Bookmark::where('user_id', auth('subscriber')->id())
            ->where('decision_id', $id)
            ->exists();

        $data = OCRExtraction::with('volume:id,number,year')->findOrFail($id);
        $allUsers = Subscriber::where('id', '!=', auth('subscriber')->id())->get();
        $decisionComment = DecisionComment::where('decision_id', $id)->get();
        $folders = Folder::where('user_id', auth('subscriber')->id())->get();

        $judgmentText = $data->judgment ?? '';

        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        $previousDecision = OCRExtraction::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();

        $nextDecision = OCRExtraction::where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        
        $returnToVolume = $data->division == 'Appellate Division' ? 'legalDecisionAppellate' : 'legalDecisionHighCourt';
        $returnParamString = $returnParamString == $returnToVolume ? $returnParamString . '/' . $data->volume_id : $returnParamString;
        if ($page) {
            $returnParamString .= '?page=' . $page;
        }
        return view('auth.subscribers.profile.single_legal_decision', compact(
            'data',
            'judgmentTextFormatted',
            'previousDecision',
            'nextDecision',
            'isBookmarked',
            'decisionComment',
            'allUsers',
            'folders',
            'returnParamString'
        ));
    }


    public function myDecision($id)
    {
        $id = Crypt::decrypt($id);
        $myNotes = UserFolderDecision::join('legal_decision_user_notes', function ($join) {
            $join->on('user_folder_decisions.user_id', '=', 'legal_decision_user_notes.user_id')
                ->on('user_folder_decisions.decision_id', '=', 'legal_decision_user_notes.decision_id');
        })
            ->where('user_folder_decisions.user_id', auth('subscriber')->id())
            ->where('user_folder_decisions.id', $id)
            ->select('user_folder_decisions.*', 'legal_decision_user_notes.notes', 'legal_decision_user_notes.id AS noteId') // Select specific columns from join
            ->first();

        if (!$myNotes) {
            return redirect()->back()->with('error', 'No notes found.');
        }

        $data = OCRExtraction::with('volume:id,number,year')->find($myNotes->decision_id);
        if (!$data) {
            return redirect()->back()->with('error', 'Decision data not found.');
        }

        $checkSharedComment = DecisionShare::where('receiver_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->exists();

        $getAllSharedUser = DecisionShare::with('receiver:id,name,email')
            ->where('sender_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->get();

        $sharedUserIds = $getAllSharedUser->pluck('receiver.id')->toArray();

        $allUsers = Subscriber::where('id', '!=', auth('subscriber')->id())
            ->whereNotIn('id', $sharedUserIds)
            ->select('id', 'name', 'email')
            ->get();

        // $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)->get();
        $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)
            /* ->where(function ($query) use ($sharedUserIds) {
                $query->where('user_id', $this->subscriberId)
                    ->orWhereIn('user_id', $sharedUserIds);
            }) */
            ->where('request_id', $id)
            ->get();

        $judgmentText = $myNotes->notes ?? '';
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        $sharedDecision = false;

        $decisionFolderId = $id;
        return view('auth.subscribers.profile.my_legal_decision', compact(
            'data',
            'myNotes',
            'sharedDecision',
            'judgmentTextFormatted',
            'decisionComment',
            'allUsers',
            'getAllSharedUser',
            'decisionFolderId',
            'checkSharedComment'
        ));
    }



    public function sharedDecision($id)
    {
        $id = Crypt::decrypt($id);
        $sharedDecision = DecisionShare::where('receiver_id', $this->subscriberId)
            ->where('id', $id)
            ->first();

        $myNotes = UserFolderDecision::join('legal_decision_user_notes', function ($join) {
            $join->on('user_folder_decisions.user_id', '=', 'legal_decision_user_notes.user_id')
                ->on('user_folder_decisions.decision_id', '=', 'legal_decision_user_notes.decision_id');
        })
            ->where('user_folder_decisions.id', $sharedDecision->request_id)
            ->select('user_folder_decisions.*', 'legal_decision_user_notes.notes', 'legal_decision_user_notes.id AS noteId') // Select specific columns from join
            ->first();

        if (!$myNotes) {
            return redirect()->back()->with('error', 'No notes found.');
        }

        $data = OCRExtraction::find($myNotes->decision_id);
        if (!$data) {
            return redirect()->back()->with('error', 'Decision data not found.');
        }

        $checkSharedComment = DecisionShare::where('receiver_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->exists();

        $getAllSharedUser = DecisionShare::with('receiver:id,name,email')
            ->where('sender_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->get();

        $sharedUserIds = $getAllSharedUser->pluck('receiver.id')->toArray();

        $allUsers = Subscriber::where('id', '!=', auth('subscriber')->id())
            ->whereNotIn('id', $sharedUserIds)
            ->select('id', 'name', 'email')
            ->get();

        // $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)->get();
        $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)
            /* ->where(function ($query) use ($sharedUserIds) {
                $query->where('user_id', $this->subscriberId)
                    ->orWhereIn('user_id', $sharedUserIds);
            }) */
            ->where('request_id', $sharedDecision->request_id)
            ->get();

        $judgmentText = $myNotes->notes ?? '';
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        return view('auth.subscribers.profile.my_legal_decision', compact(
            'data',
            'sharedDecision',
            'judgmentTextFormatted',
            'decisionComment',
            'allUsers',
            'myNotes',
            'getAllSharedUser',
            'checkSharedComment'
        ));
    }


    public function downloadPdf($id)
    {
        $data = OCRExtraction::findOrFail($id);
        $userNote = LegalDecisionUserNote::where('user_id', auth('subscriber')->id())
            ->where('decision_id', $id)
            ->first();

        $judgmentText = $userNote && $userNote->notes
            ? $userNote->notes
            : ($data->judgment ?? '');
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        $metaData = true;
        
        $pdf = Pdf::loadView('auth.subscribers.profile.legal_decision_print', compact('data', 'metaData', 'userNote', 'judgmentTextFormatted'));
        return $pdf->download('legal-decision-' . $data->id . '.pdf');
    }

    public function printView($id)
    {
        $data = OcrExtraction::findOrFail($id);
        $userNote = LegalDecisionUserNote::where('user_id', auth('subscriber')->id())
            ->where('decision_id', $id)
            ->first();

        $judgmentText = $userNote && $userNote->notes
            ? $userNote->notes
            : ($data->judgment ?? '');
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        $metaData = false;
        return view('auth.subscribers.profile.legal_decision_print', compact('data', 'metaData', 'userNote', 'judgmentTextFormatted'));
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


    public function bldVolume(Request $request)
    {
        $volumeList = $this->commonService->getVolume();
        $query = Volume::where('status', 1)->orderByRaw('CAST(number AS UNSIGNED) ASC');
        if (isset($request->volume)) {
            $query->where('id', $request->volume);
        }
        $volume_list = $query->paginate(24);
        return view('auth.subscribers.profile.bld_volume', compact('volume_list', 'volumeList'));
    }

    /* public function legalDecision($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        // Query for Appellate Division
        $appellateDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'Appellate Division')
            ->where('volume_id', $volume_id)
            ->orderBy('id', 'DESC')
            ->paginate(30, ['*'], 'appellate_page');

        // Query for High Court Division
        $highCourtDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'High Court Division')
            ->where('volume_id', $volume_id)
            ->orderBy('id', 'DESC')
            ->paginate(30, ['*'], 'highcourt_page');

        return view('auth.subscribers.profile.legal_decision_grid', compact('volumeData', 'appellateDecisions', 'highCourtDecisions'));
    } */


    public function legalDecisionIndex($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        return view('auth.subscribers.profile.index', compact('volumeData'));
    }

    public function legalDecisionAppellate($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        $appellateDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'Appellate Division')
            ->where('volume_id', $volume_id)
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('auth.subscribers.profile.appellate', compact('volumeData', 'appellateDecisions'));
    }

    public function legalDecisionHighCourt($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        $highCourtDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'High Court Division')
            ->where('volume_id', $volume_id)
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('auth.subscribers.profile.highcourt', compact('volumeData', 'highCourtDecisions'));
    }
}

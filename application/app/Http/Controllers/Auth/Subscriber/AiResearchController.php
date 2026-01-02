<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Frontend\BaseController;
use App\Models\OCRExtraction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Services\HomeService;

class AiResearchController extends BaseController
{
    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }

    public function index()
    {
        return view('auth.subscribers.ai_research');
    }

    public function ask(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:1000',
        ]);

        $userQuery = $request->input('query');
        set_time_limit(120);

        \Illuminate\Support\Facades\Log::info("AI Research Query: " . $userQuery);

        // 1. Search Database for Context (RAG) - Hybrid Approach
        $searchResults = collect([]);
        $searchSource = "None";
        try {
            // Attempt 1: Full Text Search via Scout (Meilisearch)
            $searchResults = OCRExtraction::search($userQuery)->take(7)->get();

            // Check for SCOB presence
            $hasScob = $searchResults->contains(function ($item) {
                return $item->division === 'SCOB';
            });

            // If no SCOB results found in top hits, try to fetch some explicitly via SQL to ensure diversity
            if (!$hasScob && $searchResults->count() > 0) {
                \Illuminate\Support\Facades\Log::info("No SCOB results in Scout. Attempting SQL injection for SCOB.");
                $scobResults = OCRExtraction::where('division', 'SCOB')
                    ->where(function ($q) use ($userQuery) {
                        $q->where('judgment', 'LIKE', "%{$userQuery}%")
                            ->orWhere('parties', 'LIKE', "%{$userQuery}%")
                            ->orWhere('key_words', 'LIKE', "%{$userQuery}%");
                    })
                    ->inRandomOrder()
                    ->take(3)
                    ->get();

                if ($scobResults->count() > 0) {
                    \Illuminate\Support\Facades\Log::info("Injected " . $scobResults->count() . " SCOB results.");
                    $searchResults = $searchResults->merge($scobResults);
                }
            }

            if ($searchResults->count() > 0) {
                $searchSource = "Scout/Meilisearch + SQL Filter";
                \Illuminate\Support\Facades\Log::info("Search Success: " . $searchResults->count() . " results.");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Scout Search Failed (likely offline): " . $e->getMessage());
        }

        // Attempt 2: SQL Fallback (Smart Metadata Match)
        if ($searchResults->isEmpty()) {
            $searchSource = "SQL/Fallback";
            \Illuminate\Support\Facades\Log::info("Switching to SQL Fallback for query: " . $userQuery);

            // 1. Clean the Query (Intent Extraction)
            // Remove common conversational filler to extract the potential Legal Entity
            $cleanedQuery = $this->cleanSearchQuery($userQuery);
            \Illuminate\Support\Facades\Log::info("Cleaned Query: " . $cleanedQuery);

            // Create a "Fuzzy" version of the query for Parties/Titles
            // Replaces "vs", "v.", "&", "and" with wildcard % to match any variation
            // e.g. "Safar Ali vs State" becomes "Safar Ali%State" matching "Safar Ali v. The State"
            $fuzzyQuery = preg_replace('/(\s+vs\.?\s+|\s+v\.?\s+|\s+and\s+|\s+&\s+)/i', '%', $cleanedQuery);
            \Illuminate\Support\Facades\Log::info("Fuzzy Query: " . $fuzzyQuery);


            $searchResults = OCRExtraction::where(function ($q) use ($cleanedQuery, $fuzzyQuery) {
                // High Priority: Metadata Exact/Partial Matches
                $q->where('case_no', 'LIKE', "%{$cleanedQuery}%")
                    ->orWhere('parties', 'LIKE', "%{$cleanedQuery}%")
                    ->orWhere('parties', 'LIKE', "%{$fuzzyQuery}%") // Add Fuzzy Match
                    ->orWhere('book_volume', 'LIKE', "%{$cleanedQuery}%")
                    ->orWhere('published_year', 'LIKE', "%{$cleanedQuery}%")
                    ->orWhere('judges', 'LIKE', "%{$cleanedQuery}%")

                    // Medium Priority: Keywords & Subject
                    ->orWhere('key_words', 'LIKE', "%{$cleanedQuery}%")
                    ->orWhere('subject', 'LIKE', "%{$cleanedQuery}%");
            })
                // Low Priority: Full Judgment Text (expensive, but necessary if no metadata match)
                ->orWhere('judgment', 'LIKE', "%{$userQuery}%")
                ->take(5)
                ->get();

            \Illuminate\Support\Facades\Log::info("SQL Fallback Results: " . $searchResults->count());
        }

        $context = "";
        $sources = [];

        foreach ($searchResults as $result) {
            // Truncate text to avoid token limits (approx 1000 words per case context)
            $textSnippet = Str::limit(strip_tags($result->judgment), 2000);

            $caseTitle = $result->parties ?? "Case #" . $result->id;
            $caseYear = $result->published_year ?? "Unknown Year";
            $volume = $result->book_volume ?? "Unknown Vol";
            $page = $result->starting_page_no ?? "Unknown Page";
            $caseNo = $result->case_no ?? "Unknown Case No";

            // Additional Metadata requested by user
            $decidedOn = $result->decided_on ?? "Unknown Date";
            $judges = $result->judges ?? "Unknown Judges";
            $division = $result->division ?? "Unknown Division";
            $keywords = $result->key_words ?? "";
            $subject = $result->subject ?? "";
            $acts = $result->related_act_order_rule ?? "";

            $fileLink = $result->file_path ? asset($result->file_path) : null;
            $isScob = !empty($result->file_path) || $result->division === 'SCOB';

            // Citation Logic
            if ($isScob) {
                $citation = "SCOB: " . ($result->case_no ?? "Unknown Case") . "; " . ($result->parties ?? "Unknown Parties");
            } else {
                $citation = "{$volume} BLD {$page}";
            }

            // Structured Context for AI - HIGH DETAIL
            $context .= "Source ID: {$result->id}\n";
            $context .= "Parties: {$caseTitle}\n";
            $context .= "Case No: {$caseNo}\n";
            $context .= "Citation: {$citation}\n";
            if ($fileLink) {
                $context .= "PDF Link: {$fileLink}\n";
            }
            $context .= "Year: {$caseYear}\n";
            $context .= "Decided On: {$decidedOn}\n";
            $context .= "Division: {$division}\n";
            $context .= "Judges: {$judges}\n";
            $context .= "Subject: {$subject}\n";
            $context .= "Keywords: {$keywords}\n";
            $context .= "Related Acts: {$acts}\n";
            $context .= "Content/Judgment Snippet: {$textSnippet}\n\n---\n\n";

            $sources[] = [
                'id' => $result->id,
                'title' => $caseTitle,
                'year' => $caseYear,
                'citation' => $citation,
                'link' => $fileLink // Add link for frontend
            ];
        }

        if (empty($context)) {
            $context = "No specific relevant case laws were found in the database for this query.";
        }

        // 2. Build Prompt
        $systemPrompt = "You are a specialized legal research assistant for Bangladesh Law. 
        
        Your Task:
        1. Answer the user's question accurately based on the provided Contextual Documents.
        2. IF search matches a specific Case Number/Party, prioritize that case.
        
        CRITICAL OUTPUT FORMATTING RULES:
        - Use standard Markdown list syntax for structure.
        - You MUST use the exact structure below for your response.
        - Present the Main Case(s) found in this layout:

        - **Case [N]: [Citation]**
          * **Case Number**: [Case No]
          * **Parties**: [Parties Name]
          * **Decided On**: [Date]
          * **Subject**: [Subject]
           
        **Source**: [Citation]

        [If multiple cases, repeat the block above for each one]

        Important Notes:
        - Replace [Bracketed Text] with actual data. 
        - For BLD cases, Citation format: Vol [Volume] BLD [Page] [Division]
        - For SCOB/PDF cases, Citation format: SCOB: [Case No]; [Parties]
        - If 'Page' assumes a range (e.g. 168 to 196), use it. otherwise just use the starting page.
        - Ensure 'Parties' are UPPERCASE.
        - Do not add any other introductory text.
        - If no records found, state: 'No specific records were found in the database.'";

        $fullPrompt = "Contextual Documents:\n" . $context . "\n\nUser Query: " . $userQuery;

        // 3. Call OpenRouter AI
        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'AI Service Config Missing'], 500);
        }

        try {
            $response = Http::timeout(120)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => url('/'),
                'X-Title' => config('app.name'),
            ])->post("https://openrouter.ai/api/v1/chat/completions", [
                        'model' => 'google/gemini-2.0-flash-exp:free',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $fullPrompt]
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['choices'][0]['message']['content'] ?? 'No response generated.';

                return response()->json([
                    'success' => true,
                    'answer' => $answer, // Send Raw Markdown for frontend parsing
                    'sources' => $sources
                ]);
            } else {
                return response()->json(['error' => 'AI Service Error: ' . $response->status()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Removes common conversational filler from the query to extract the likely legal entity.
     */
    private function cleanSearchQuery($query)
    {
        // 1. Extract Quoted Text (Highest Priority)
        if (preg_match('/["\'](.*?)["\']/', $query, $matches)) {
            \Illuminate\Support\Facades\Log::info("Extracted Quoted Intent: " . $matches[1]);
            return trim($matches[1]);
        }

        // 2. Extract Legal Citation Patterns (Smart Extraction)
        // Looks for: "W.P. 832 of 1981", "Civil Appeal 50/2020", "Suit 50 of 99"
        // Pattern: (Word/Abbr) + Number + (of|/) + Year
        $citationPattern = '/([a-zA-Z\.]+\s*)?\d+\s*(of|\/)\s*\d{4}/i';
        if (preg_match($citationPattern, $query, $matches)) {
            // If we find a strong citation pattern, use it and ignore the rest of the "fint this case" noise
            \Illuminate\Support\Facades\Log::info("Extracted Citation Intent: " . $matches[0]);
            return trim($matches[0]);
        }

        // 3. Extract Party Names (e.g. "Hossain v. Humayun")
        // Pattern: [Words] (v.|vs|versus) [Words]
        // This handles "HOSSAIN v. HUMAYUN & ORS --- find this" -> extracts "HOSSAIN v. HUMAYUN & ORS"
        $partyPattern = '/([a-zA-Z0-9&\.\s]+)\s+(v\.|vs\.?|versus)\s+([a-zA-Z0-9&\.\s]+)/i';
        if (preg_match($partyPattern, $query, $matches)) {
            // We need to be careful not to match just "v." in isolation, but the pattern requires words around it.
            // Also need to stop before hitting "find this" suffix if possible, but the next step strips noise anyway.
            // Actually, safer to extract it if it looks like a party title.
            \Illuminate\Support\Facades\Log::info("Extracted Party Intent: " . $matches[0]);
            return trim($matches[0]);
        }


        // 4. Lowercase for processing
        $clean = strtolower($query);

        // 5. Remove conversational patterns (Enhanced for typos like "fint")
        // We assume end-of-sentence instructions often come after the query
        $patterns = [
            // Prefixes
            '/^(could|can|would|will|do)\s+(you|we|i)\s+(find|search|show|get|tell|give|bring|look|know|analy(z|s)e).+?(like|about|for|that)?/i',
            '/^(please|kindly)?\s*(find|search|show|display|get|give).+?(case|judgment|decision|result|record)(s)?\s*(like|about|for|regarding|related to)?/i',
            '/^(i\s+want|im\s+looking\s+for|search\s+for|result\s+for|case\s+like)/i',

            // Suffixes (instructions usually at the end) e.g. "fint this case", "analyze this"
            '/\s+(find|fint|search|check|analy(z|s)e)\s+(this|the)\s+(case|judgment|data|record|db).*$/i',
            '/\s+(in|from)\s+(database|db|record).*$/i'
        ];

        foreach ($patterns as $pattern) {
            $clean = preg_replace($pattern, '', $clean);
        }

        // 5. Remove common non-legal punctuation
        $clean = preg_replace('/[?!,;"]/', '', $clean);

        // 6. Trim extra whitespace
        $clean = trim($clean);

        return !empty($clean) ? $clean : $query;
    }
}

<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\OCRExtraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function summarizeJudgment(Request $request)
    {
        try {
            $id = $request->input('id');
            $judgment = OCRExtraction::findOrFail($id);

            // Clean up the text - limit to approx 15k tokens.
            $rawText = $judgment->judgment ?? '';
            if (empty($rawText)) {
                return response()->json(['error' => 'No judgment text found to summarize.'], 400);
            }
            $text = substr(strip_tags($rawText), 0, 30000);

            // AI generation can take time, increase limit to 120 seconds
            set_time_limit(120);

            $apiKey = env('OPENROUTER_API_KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key not configured'], 500);
            }

            $prompt = "You are a legal expert assistant. Please summarize the following legal judgment into the exact markdown format below. 
            
            IMPORTANT: Do not include any introductory text, validatory remarks, or conversational filler (like 'Here is the summary'). Output ONLY the markdown content starting with '**Case Overview**:'.
            
            Format:
            **Case Overview**: (1 sentence summary)
            
            **Facts of the Case**: (Brief bullet points)
            
            **Key Legal Issues**: (What was being decided?)
            
            **Court's Reasoning**: (Why did they decide this way?)
            
            **Final Decision**: (The verdict)

            Here is the judgment text:
            " . $text;

            $response = Http::timeout(120)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => url('/'), // Optional: For OpenRouter rankings
                'X-Title' => config('app.name'), // Optional: For OpenRouter rankings
            ])->post("https://openrouter.ai/api/v1/chat/completions", [
                        'model' => 'mistralai/devstral-2512:free', // Using the model user provided
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiText = $data['choices'][0]['message']['content'] ?? 'Could not generate summary.';

                // Simple formatting to make it look nice in HTML if raw markdown isn't parsed
                $formattedText = nl2br($aiText);

                return response()->json([
                    'success' => true,
                    'summary' => $aiText // sending raw markdown/text for frontend to handle
                ]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json(['error' => 'Failed to reach AI service'], 500);
            }

        } catch (\Exception $e) {
            Log::error('AI Summary Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong processing the request.'], 500);
        }
    }
}

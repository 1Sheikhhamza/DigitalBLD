<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScobSearchController extends Controller
{
    public function index()
    {
        return view('auth.subscribers.profile.scob_search');
    }

    public function proxy(Request $request)
    {
        try {
            $baseUrl = 'https://supremecourt.gov.bd/web/';
            
            // Forward all query parameters
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Referer' => 'https://supremecourt.gov.bd/web/',
            ])->withoutVerifying()->get($baseUrl, $request->all());

            return response($response->body())
                ->header('Content-Type', $response->header('Content-Type'));
        } catch (\Exception $e) {
            return response('Error fetching data: ' . $e->getMessage(), 500);
        }
    }

    public function proxyPdf(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response('Invalid URL', 400);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ])->withoutVerifying()->get($url);

            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="document.pdf"');
        } catch (\Exception $e) {
            return response('Error fetching PDF: ' . $e->getMessage(), 500);
        }
    }
}

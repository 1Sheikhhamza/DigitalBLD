<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\Dictionary;
use Illuminate\Http\Request;
use App\Services\HomeService;

class DictionaryController extends BaseController
{

    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }

     public function index(Request $request)
    {
        $query = $request->get('q');

        $words = Dictionary::when($query, function ($q) use ($query) {
            $q->where('word', 'like', "%$query%");
        })->orderBy('word', 'asc')->paginate(20);

        return view('auth.subscribers.profile.dictionary.index', compact('words', 'query'));
    }
}

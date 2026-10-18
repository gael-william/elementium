<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:150']]);

        Subscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return response()->json(['message' => 'Merci, vous êtes bien inscrit(e) à notre newsletter !']);
    }
}

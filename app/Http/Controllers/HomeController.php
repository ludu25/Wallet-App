<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Transaction;

class HomeController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $transactions = Transaction::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('home', compact('transactions'));
    }

    public function validatePhoneNumber()
    {
        $phone = auth()->user()->phone;

        $apiUrl = 'https://apilayer.net/api/validate';

        $response = Http::get($apiUrl, [
            'access_key' => '24214f5e27f60b834de9a6afda61f0ea',
            'number' => '55' . $phone,
        ]);


        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data);
        }

        return response()->json(['message' => 'Erro ao validar o número de celular.'], 400);
    }

    public function convertToUSD()
    {
        $phone = auth()->user()->wallet->balance || 0;

        $apiUrl = 'https://v6.exchangerate-api.com/v6/677cc98a9150032ba21d00bc/latest/BRL';

        $response = Http::get($apiUrl);


        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data);
        }

        return response()->json(['message' => 'Erro ao converter BRL para USD'], 400);
    }
}

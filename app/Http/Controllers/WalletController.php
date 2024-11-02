<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class WalletController extends Controller
{
    public function update(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();

        if (!$user->wallet) {
            $user->wallet()->create([
                'user_id' => $user->id,
                'balance' => 0
            ]);
        }

        $user->wallet->balance = $user->wallet->balance + $request->input('amount');
        $user->wallet->save();

        return redirect()->back()->with('success', 'Carteira recarregada com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'recipient_email' => 'required|email',
            'amount' => 'required|numeric|min:1',
        ]);

        $sender = auth()->user();
        $receiver = User::where('email', $request->recipient_email)->first();

        if ($sender->wallet->balance < $request->amount) {
            return redirect()->back()->with('transfer_error', 'Saldo insuficiente, recarregue sua carteira!');
        }

        if (!$receiver) { 
           return redirect()->back()->with('transfer_error', 'Este usuário não existe, tente outro email!');
        }

        DB::beginTransaction();

        try {
            // Atualiza o saldo do usuário que enviou o dinheiro
            $sender->wallet->balance = $sender->wallet->balance - $request->amount;
            $sender->wallet->save();

            // Atualiza o saldo do usuário que recebeu o dinheiro
            $receiver->wallet->balance = $receiver->wallet->balance + $request->amount;
            $receiver->wallet->save();

            Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $request->amount,
                'status' => 'completed'
            ]);

            DB::commit();
            return redirect()->back()->with('transfer_success', 'Transferência realizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('transfer_error', 'Erro na tranferência!');
        }
    }
}

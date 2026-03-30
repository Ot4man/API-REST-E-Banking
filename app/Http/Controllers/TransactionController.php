<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Get transaction history for a specific account.
     */
    public function index($accountId): JsonResponse
    {
        $account = auth()->user()->accounts()->find($accountId);

        if (!$account) {
            return response()->json(['message' => 'Account not found or access denied'], 404);
        }

        $transactions = $account->transactions()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'account' => $account->type . " (" . $account->balance . ")",
            'transactions' => $transactions
        ]);
    }
}

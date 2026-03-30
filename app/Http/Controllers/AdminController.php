<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * Block an account.
     */
    public function block($accountId): JsonResponse
    {
        $account = Account::findOrFail($accountId);
        $account->update(['status' => 'BLOCKED']);

        return response()->json(['message' => 'Account blocked successfully', 'account' => $account]);
    }

    /**
     * Unblock an account.
     */
    public function unblock($accountId): JsonResponse
    {
        $account = Account::findOrFail($accountId);
        $account->update(['status' => 'ACTIVE']);

        return response()->json(['message' => 'Account unblocked successfully', 'account' => $account]);
    }

    /**
     * Close an account.
     */
    public function close($accountId): JsonResponse
    {
        $account = Account::findOrFail($accountId);
        $account->update(['status' => 'CLOSED']);

        return response()->json(['message' => 'Account closed successfully', 'account' => $account]);
    }

    /**
     * List all accounts (admin view).
     */
    public function index(): JsonResponse
    {
        $accounts = Account::with('users')->get();
        return response()->json($accounts);
    }
}

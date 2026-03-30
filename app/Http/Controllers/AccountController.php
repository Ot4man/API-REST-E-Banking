<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Services\AccountService;
use Exception;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    protected $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    /**
     * Create a new account for the current authenticated user.
     */
    public function store(StoreAccountRequest $request): JsonResponse
    {
        try {
            $account = $this->service->createAccount(
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'message' => 'Account created successfully',
                'account' => $account
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * List user accounts for the current authenticated user.
     */
    public function index(): JsonResponse
    {
        $accounts = auth()->user()->accounts()->get();
        return response()->json($accounts);
    }

    /**
     * Get account details for the current authenticated user.
     */
    public function show($id): JsonResponse
    {
        $account = auth()->user()->accounts()->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found or access denied'], 404);
        }

        return response()->json($account->load(['users', 'guardian']));
    }
}

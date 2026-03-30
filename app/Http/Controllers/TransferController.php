<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Services\TransferService;
use Exception;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    protected $service;

    public function __construct(TransferService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the transfer of funds.
     */
    public function transfer(StoreTransferRequest $request): JsonResponse
    {
        try {
            $result = $this->service->transfer(
                $request->validated(),
                auth()->user()
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}

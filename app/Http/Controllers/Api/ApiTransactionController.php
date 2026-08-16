<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiTransactionController extends Controller
{
    public function __construct(private readonly TransactionService $service) {}

    public function index(Request $request): JsonResponse
    {
        $transactions = $request->user()->transactions()
            ->with(['account', 'category', 'transferToAccount'])
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->input('from'), fn ($q, $from) => $q->whereDate('transaction_date', '>=', $from))
            ->when($request->input('to'), fn ($q, $to) => $q->whereDate('transaction_date', '<=', $to))
            ->latest('transaction_date')
            ->paginate(15);

        return TransactionResource::collection($transactions);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->service->create(array_merge($request->validated(), [
            'user_id' => $request->user()->id,
        ]));

        return (new TransactionResource($transaction->load(['account', 'category', 'transferToAccount'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);

        return new TransactionResource($transaction->load(['account', 'category', 'transferToAccount']));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);

        $transaction = $this->service->update($transaction, $request->validated());

        return new TransactionResource($transaction->load(['account', 'category', 'transferToAccount']));
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);

        $this->service->delete($transaction);

        return response()->json(['message' => 'تم حذف المعاملة بنجاح']);
    }
}

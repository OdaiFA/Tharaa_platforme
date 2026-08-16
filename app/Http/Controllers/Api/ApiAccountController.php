<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accounts = $request->user()->accounts()
            ->withCount('transactions')
            ->paginate(15);

        return AccountResource::collection($accounts);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = $request->user()->accounts()->create(array_merge(
            $request->validated(),
            ['balance' => $request->input('initial_balance', 0)],
        ));

        return (new AccountResource($account))->response()->setStatusCode(201);
    }

    public function show(Request $request, Account $account): JsonResponse
    {
        $this->authorize('view', $account);

        return new AccountResource($account->load('transactions'));
    }

    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $this->authorize('update', $account);

        $account->update($request->validated());

        return new AccountResource($account->fresh());
    }

    public function destroy(Request $request, Account $account): JsonResponse
    {
        $this->authorize('delete', $account);

        $account->delete();

        return response()->json(['message' => 'تم حذف الحساب بنجاح']);
    }
}

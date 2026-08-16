<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContributeGoalRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Http\Resources\GoalResource;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiGoalController extends Controller
{
    public function __construct(private readonly GoalService $goalService) {}

    public function index(Request $request): JsonResponse
    {
        $goals = $request->user()->goals()
            ->withCount('contributions')
            ->with('contributions')
            ->paginate(15);

        return GoalResource::collection($goals);
    }

    public function store(StoreGoalRequest $request): JsonResponse
    {
        $goal = $request->user()->goals()->create($request->validated());

        return (new GoalResource($goal))->response()->setStatusCode(201);
    }

    public function show(Request $request, Goal $goal): JsonResponse
    {
        $this->authorize('view', $goal);

        return new GoalResource($goal->load('contributions'));
    }

    public function contribute(ContributeGoalRequest $request, Goal $goal): JsonResponse
    {
        $this->authorize('contribute', $goal);

        try {
            $contribution = $this->goalService->contribute(
                $goal,
                (float) $request->input('amount'),
                $request->input('account_id'),
                $request->input('note'),
                $request->input('contribution_date'),
            );
        } catch (\DomainException|\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'تمت إضافة المساهمة بنجاح',
            'contribution' => $contribution,
            'goal' => new GoalResource($goal->fresh()),
        ], 201);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): JsonResponse
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return new GoalResource($goal->fresh());
    }

    public function destroy(Request $request, Goal $goal): JsonResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return response()->json(['message' => 'تم حذف الهدف بنجاح']);
    }
}

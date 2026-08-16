<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiProfileController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->safe()->except(['avatar']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        $this->authService->assignAgeGroup($user);

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => new UserResource($user->fresh('ageGroup')),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(User $user): JsonResponse
    {
        return response()->json([
            'message' => 'User roles retrieved successfully.',
            'data' => [
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function store(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|string|exists:roles,name',
        ]);

        $user->syncRoles($validated['roles']);

        return response()->json([
            'message' => 'User roles updated successfully.',
            'data' => [
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }
}

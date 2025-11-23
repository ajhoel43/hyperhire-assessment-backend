<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getRecommendations(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'This is a placeholder response for recommendations.'
        ]);
    }

    public function likeAccount(Request $request, $accountId): JsonResponse
    {
        // TODO: When an account has like > 50, send notification to admin
        return response()->json([
            'message' => "Account with ID {$accountId} has been liked."
        ]);
    }

    public function dislikeAccount(Request $request, $accountId): JsonResponse
    {
        return response()->json([
            'message' => "Account with ID {$accountId} has been disliked."
        ]);
    }

    public function getLikedAccounts(Request $request): JsonResponse
    {
        return response()->json([
            'message' => "This is a placeholder response for liked accounts."
        ]);
    }
}

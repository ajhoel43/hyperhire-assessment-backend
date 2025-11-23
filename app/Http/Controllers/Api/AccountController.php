<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\LikeCounter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getRecommendations(Request $request): JsonResponse
    {
        $dataPerPage = $request->input('limit', 10);
        $data = Account::withLikeCounts()->simplePaginate($dataPerPage);

        return response()->json([
            'message' => 'This is a placeholder response for recommendations.',
            'peoples' => $data,
        ]);
    }

    public function likeAccount(Request $request, $accountId): JsonResponse
    {
        try {
            $hasLike = LikeCounter::where('account_id', $accountId)->first();
            if ($hasLike) {
                // Update Existing
                $hasLike->increment('like_count');
            } else {
                // Create New
                LikeCounter::create([
                    'account_id'    => $accountId,
                    'like_count'    => 1,
                    'dislike_count' => 0
                ]);
            }

            // TODO: When an account has like > 50, send notification to admin
            return response()->json([
                'message' => "Account with ID {$accountId} has been liked."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cannot Like this account right now.',
                'error'   => $th->getMessage()
            ]);
        }
    }

    public function dislikeAccount(Request $request, $accountId): JsonResponse
    {
        try {
            $hasLike = LikeCounter::where('account_id', $accountId)->first();
            if ($hasLike) {
                // Update Existing
                $hasLike->increment('dislike_count');
            } else {
                // Create New
                LikeCounter::create([
                    'account_id'    => $accountId,
                    'like_count'    => 0,
                    'dislike_count' => 1
                ]);
            }

            return response()->json([
                'message' => "Account with ID {$accountId} has been disliked."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cannot Dislike this account right now.',
                'error'   => $th->getMessage()
            ]);
        }
    }

    public function getLikedAccounts(Request $request): JsonResponse
    {
        return response()->json([
            'message' => "This is a placeholder response for liked accounts."
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AccountCheckpointReachedMail;
use App\Models\Account;
use App\Models\LikeCounter;
use App\Models\LikeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            $account = Account::find($accountId);
            if (!$account) {
                throw new \Exception("Account not found.");
            }

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

            // Log
            LikeLog::create([
                'account_id' => $accountId,
                'action'     => 'like'
            ]);

            // When an account has reach 50 likes, send notification to admin
            if ($hasLike && $hasLike->like_count + 1 === 50) {
                // Send Email to Admin
                Mail::to(env("MAIL_DUMMY_TARGET", "dummy@ajulity.com"))->send(new AccountCheckpointReachedMail($account));
            }

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
            $account = Account::find($accountId);
            if (!$account) {
                throw new \Exception("Account not found.");
            }

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

            // Log
            LikeLog::create([
                'account_id' => $accountId,
                'action'     => 'like'
            ]);

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
        $likedAccounts = Account::likedAccounts()->get();
        return response()->json([
            'message' => "This is a placeholder response for liked accounts.",
            'peoples' => $likedAccounts
        ]);
    }
}

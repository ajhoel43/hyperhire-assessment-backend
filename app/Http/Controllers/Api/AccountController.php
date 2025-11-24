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
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="PeopleItem",
 * title="People Data",
 * description="Data Schema of People",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Kenton Hirthe"),
 * @OA\Property(property="email", type="string", format="email", example="k.hirthe@example.com"),
 * @OA\Property(property="age", type="integer", example=33),
 * @OA\Property(property="pictures", type="string", format="url", description="Profile Picture URL"),
 * @OA\Property(property="location", type="string", example="South Davin, Alaska"),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time"),
 * @OA\Property(property="like_count", type="integer", nullable=true, description="Like Counter"),
 * @OA\Property(property="dislike_count", type="integer", nullable=true, description="Dislike Counter")
 * )
 * 
 * @OA\Schema(
 * schema="PeoplePaginated",
 * title="Peoples Data (Paginated)",
 * description="Data Schema of Peoples (Paginated)",
 * @OA\Property(property="current_page", type="integer", example=1),
 * @OA\Property(property="current_page_url", type="string", format="url"),
 * @OA\Property(property="data", type="array", 
 * @OA\Items(ref="#/components/schemas/PeopleItem")
 * ),
 * @OA\Property(property="first_page_url", type="string", format="url"),
 * @OA\Property(property="from", type="integer", example=1),
 * @OA\Property(property="next_page_url", type="string", format="url", nullable=true),
 * @OA\Property(property="path", type="string", format="url"),
 * @OA\Property(property="per_page", type="integer", example=10),
 * @OA\Property(property="prev_page_url", type="string", format="url", nullable=true),
 * @OA\Property(property="to", type="integer", example=10),
 * @OA\Property(property="total", type="integer", example=50)
 * )
 */

class AccountController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/people/recommendation",
     * tags={"Account"},
     * summary="List all recommended people + pagination",
     * @OA\Parameter(
     * name="limit",
     * in="query",
     * description="Number of data per page",
     * required=false,
     * @OA\Schema(type="integer", default=10)
     * ),
     * 
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="This is a placeholder response for recommendations."),
     * @OA\Property(property="peoples", ref="#/components/schemas/PeoplePaginated")
     * )
     * ),
     * @OA\Response(response=404, description="Fail operation")
     * )
     * )
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $dataPerPage = $request->input('limit', 10);
        $data = Account::withLikeCounts()->simplePaginate($dataPerPage);

        return response()->json([
            'message' => 'This is a placeholder response for recommendations.',
            'peoples' => $data,
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/people/{accountId}/like",
     * tags={"Account"},
     * summary="Like an Account",
     * description="Increase like counter of account base on {accountId}, when counter has reach 50, it will send email to admin",
     * 
     * @OA\Parameter(
     * name="accountId",
     * in="path",
     * description="ID of an account",
     * required=true,
     * @OA\Schema(type="integer", example=99)
     * ),
     * 
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Account with ID {$accountId} has been liked.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Fail operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Cannot like this account right now."),
     * @OA\Property(property="error", type="string", example="Some error occurred.")
     * )
     * )
     * )
     */
    public function postLikeAccount(Request $request, $accountId): JsonResponse
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
                // Mail::to(env("MAIL_DUMMY_TARGET", "dummy@ajulity.com"))->queue(new AccountCheckpointReachedMail($account));
                Mail::to(env("MAIL_DUMMY_TARGET", "dummy@ajulity.com"))->send(new AccountCheckpointReachedMail($account)); // direct send for testing
            }

            return response()->json([
                'message' => "Account with ID {$accountId} has been liked."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cannot Like this account right now.',
                'error'   => $th->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Post(
     * path="/api/people/{accountId}/dislike",
     * tags={"Account"},
     * summary="Dislike an Account",
     * description="Increase dislike counter of account base on {accountId}",
     * @OA\Parameter(
     * name="accountId",
     * in="path",
     * description="ID of an account",
     * required=true,
     * @OA\Schema(type="integer", example=99)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Account with ID {$accountId} has been disliked."),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Fail operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Cannot dislike this account right now."),
     * @OA\Property(property="error", type="string", example="Some error occurred.")
     * )
     * )
     * )
     */
    public function postDislikeAccount(Request $request, $accountId): JsonResponse
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
                'action'     => 'dislike'
            ]);

            return response()->json([
                'message' => "Account with ID {$accountId} has been disliked."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cannot Dislike this account right now.',
                'error'   => $th->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     * path="/api/people/liked",
     * tags={"Account"},
     * summary="List all liked people + pagination",
     * @OA\Parameter(
     * name="limit",
     * in="query",
     * description="Number of data per page",
     * required=false,
     * @OA\Schema(type="integer", default=10)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Liked Peoples."),
     * @OA\Property(property="peoples", ref="#/components/schemas/PeoplePaginated")
     * )
     * ),
     * @OA\Response(response=404, description="Fail operation")
     * )
     * )
     */
    public function getLikedAccounts(Request $request): JsonResponse
    {
        $likedAccounts = Account::likedAccounts()->get();
        return response()->json([
            'message' => "Liked Peoples.",
            'peoples' => $likedAccounts
        ]);
    }
}

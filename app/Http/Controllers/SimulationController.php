<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimulationRequest;
use App\Mail\AccountCheckpointReachedMail;
use App\Models\Account;
use App\Models\LikeCounter;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SimulationController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/simulate/likeReached",
     * tags={"Simulation"},
     * summary="Simulate an account to reach 50 Likes, and sent notification into provided email",
     * 
     * @OA\RequestBody(
     * description="Simulated Data",
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="accountId", type="integer", example=100),
     * @OA\Property(property="targetEmail", type="string", example=L5_SWAGGER_CONST_TARGET)
     * )
     * ),
     * 
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Simulation successfull, email has been sent to admin.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Fail operation",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Cannot simulate 50 likes right now."),
     * @OA\Property(property="error", type="string", example="Some error occurred.")
     * )
     * )
     * )
     */
    public function postSimulateLike50(SimulationRequest $request)
    {
        try {
            $reqData = $request->all();
            $account = Account::find($reqData['accountId']);
            if (!$account) {
                throw new NotFoundHttpException("Account not found.");
            }

            $hasLike = LikeCounter::where('account_id', $reqData['accountId'])->first();
            if ($hasLike) {
                $hasLike->update(['like_count' => 50]);
            } else {
                // Create New
                LikeCounter::create([
                    'account_id'    => $reqData['accountId'],
                    'like_count'    => 50,
                    'dislike_count' => 0
                ]);
            }

            Mail::to($reqData['targetEmail'])->send(new AccountCheckpointReachedMail($account));

            return response()->json([
                'message' => sprintf("Simulation successful, email has been sent to %s. Please also check your spam folder.", $reqData['targetEmail'])
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'message' => 'Cannot simulate 50 likes right now.',
                'error'   => $th->getMessage()
            ]);
        }
    }
}

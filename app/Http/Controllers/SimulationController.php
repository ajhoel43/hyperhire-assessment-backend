<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimulationRequest;
use App\Mail\AccountCheckpointReachedMail;
use App\Models\Account;
use App\Models\LikeCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SimulationController extends Controller
{
    public function postSimulateLike50(SimulationRequest $request)
    {
        try {
            $reqData = $request->all();
            // dd($reqData);

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
                'message' => 'Simulation successfull, Email sent to admin.'
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'message' => 'Cannot simulate 50 likes right now.',
                'error'   => $th->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'age',
        'pictures',
        'location',
    ];

    protected $casts = [
        'age' => 'integer'
    ];

    public function likeCounter()
    {
        return $this->hasOne(LikeCounter::class, 'account_id', 'id');
    }

    public function scopeWithLikeCounts($query)
    {
        return $query->leftJoin('like_counters AS lc', 'accounts.id', '=', 'lc.account_id')
            ->select('accounts.*', 'lc.like_count', 'lc.dislike_count');
    }

    public function scopeLikedAccounts($query)
    {
        $likedQuery = DB::table('like_logs')
            ->select('account_id')
            ->where('action', 'like')
            ->distinct();

        return $query->joinSub($likedQuery, 'll', function($join) {
            $join->on('accounts.id', '=', 'll.account_id');
        })->select('accounts.*');
    }

    public function scopeDislikedAccounts($query)
    {
        $likedQuery = DB::table('like_logs')
            ->select('account_id')
            ->where('action', 'dislike')
            ->distinct();

        return $query->joinSub($likedQuery, 'll', function($join) {
            $join->on('accounts.id', '=', 'll.account_id');
        })->select('accounts.*');
    }
}

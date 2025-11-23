<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikeLog extends Model
{
    protected $fillable = ['account_id', 'action'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeCounter extends Model
{
    use HasFactory;

    protected $primaryKey = 'account_id';
    public $incrementing = false;


    protected $fillable = [
        'account_id',
        'like_count',
        'dislike_count'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class UserLogin extends Model
{


    protected $fillable = ['user_id', 'ip', 'os', 'browser', 'token', 'login_at', 'logout_at', 'location'];

    protected $casts = ['location' => 'object'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}

<?php

namespace Modules\Quiz\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class QuizTest extends Model
{


    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(QuizTestDetails::class, 'quiz_test_id');
    }

    public function quiz()
    {
        return $this->belongsTo(OnlineQuiz::class, 'quiz_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

<?php

namespace Modules\Quiz\Entities;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class QuestionBankMuOption extends Model
{


    protected $fillable = [];

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id')->withDefault();
    }
}

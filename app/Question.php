<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = ['id'];

    public function jobs()
    {
        $this->belongsToMany(Job::class, 'job_questions');
    }

    public function answers()
    {
        return $this->hasMany(JobApplicationAnswer::class);
    }

   
}

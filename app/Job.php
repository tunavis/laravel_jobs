<?php

namespace App;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use Sluggable;

    protected $dates = ['end_date', 'start_date'];

    protected $casts = [
        'required_columns' => 'array',
        'meta_details' => 'array',
        'section_visibility' => 'array'
    ];

    protected $appends = [
        'active'
    ];
    
    public function applications()
    {
        return $this->belongsToMany(JobApplication::class);
    }

    public function category(){
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function location(){
        return $this->belongsTo(JobLocation::class, 'location_id');
    }

    public function skills(){
        return $this->hasMany(JobSkill::class, 'job_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'location.location']
            ]
        ];
    }

    public static function activeJobs(){
        return Job::where('status', 'active')
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->get();
    }

    public static function activeJobsCount(){
        return Job::where('status', 'active')
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->count();
    }

    public function getActiveAttribute()
    {
        return $this->status === 'active' && $this->start_date <= Carbon::now()->format('Y-m-d') && $this->end_date >= Carbon::now()->format('Y-m-d');
    }

    public function questions(){
        return $this->belongsToMany(Question::class, 'job_questions');
    }

    public function workExperience(){
        return $this->belongsTo(WorkExperience::class, 'work_experience_id');
    }

    public function jobType(){
        return $this->belongsTo(JobType::class, 'job_type_id');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Job;
use App\Skill;
use App\Company;
use App\JobType;
use App\JobSkill;
use App\Question;
use App\JobCategory;
use App\JobLocation;
use App\Helper\Reply;
use App\JobApplication;
use App\WorkExperience;
use App\ApplicationStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreJob;
use App\Http\Requests\UpdateJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\NewJobOpening;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;

class AdminJobsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.jobs');
        $this->pageIcon = 'icon-badge';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        abort_if(!$this->user->cans('view_jobs'), 403);
        $this->companies = Company::all();
        $this->totalJobs = Job::count();
        $this->locations = JobLocation::all();
        $this->activeJobs = Job::where('status', 'active')->count();
        $this->inactiveJobs = Job::where('status', 'inactive')->count();

        return view('admin.jobs.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->cans('add_jobs'), 403);
        $this->job = (request()['duplicate_job'] ? Job::with(['category', 'skills', 'questions'])->findOrFail(request()['duplicate_job']) : null);
        if (!is_null($this->job)) {
            $this->jobQuestion = $this->job->questions->pluck('id')->toArray();
            $this->skills = Skill::where('category_id', $this->job->category_id)->get();
        }
        $this->categories = JobCategory::all();
        $this->locations = JobLocation::all();
        $this->jobTypes = JobType::all();
        $this->workExperiences = WorkExperience::all();
        $this->questions = Question::all();
        $this->companies = Company::all();
        return view('admin.jobs.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJob $request)
    {
        abort_if(!$this->user->cans('add_jobs'), 403);

        $required_columns = [
            'gender' => false,
            'dob' => false,
            'country' => false
        ];

        foreach ($required_columns as $key => $value) {
            if ($request->has($key)) {
                $required_columns[$key] = true;
            }
        }

        $section_visibility = [
            'profile_image' => 'no',
            'resume' => 'no',
            'cover_letter' => 'no',
            'terms_and_conditions' => 'no'
        ];

        foreach ($section_visibility as $key => $value) {
            if ($request->has($key)) {
                $section_visibility[$key] = 'yes';
            }
        }

        $job = new Job();
        $job->slug = null;
        $job->company_id = $request->company;
        $job->title = $request->title;
        $job->job_description = $request->job_description;
        $job->job_requirement = $request->job_requirement;
        $job->total_positions = $request->total_positions;
        $job->location_id = $request->location_id;
        $job->category_id = $request->category_id;
        $job->start_date = $request->start_date;
        $job->end_date = $request->end_date;
        $job->status = $request->status;
        $job->job_type_id = $request->job_type_id;
        $job->work_experience_id = $request->work_experience_id;
        $job->pay_type = $request->pay_type;
        $job->pay_according = $request->pay_according;
        $job->starting_salary = $request->starting_salary;
        $job->maximum_salary = $request->maximum_salary;
        $job->required_columns = $required_columns;
        $job->section_visibility = $section_visibility;
        $job->meta_details = [
            'title' => $request->meta_title ?: $request->title,
            'description' => $request->meta_description ?: strip_tags(Str::substr(html_entity_decode($request->job_description), 0, 150))
        ];

        if ($request->show_job_type == 'yes') {
            $job->show_job_type = true;
        }

        if ($request->show_work_experience == 'yes') {
            $job->show_work_experience = true;
        }

        if ($request->show_salary == 'yes') {
            $job->show_salary = true;
        }

        $job->save();

        if (!is_null($request->skill_id)) {
            JobSkill::where('job_id', $job->id)->delete();

            foreach ($request->skill_id as $skill) {
                $jobSkill = new JobSkill();
                $jobSkill->skill_id = $skill;
                $jobSkill->job_id = $job->id;
                $jobSkill->save();
            }
        }

        // Save Question for job
        $job->questions()->sync($request->question);

        return Reply::redirect(route('admin.jobs.index'), __('menu.jobs') . ' ' . __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->job = Job::find($id);
        return $this->job;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_jobs'), 403);
        $this->job = Job::find($id);
        $this->categories = JobCategory::all();
        $this->locations = JobLocation::all();
        $this->skills = Skill::where('category_id', $this->job->category_id)->get();
        $this->jobQuestion = $this->job->questions->pluck('id')->toArray();
        $this->questions = Question::all();
        $this->companies = Company::all();
        $this->jobTypes = JobType::all();
        $this->workExperiences = WorkExperience::all();

        return view('admin.jobs.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreJob $request, $id)
    {
        abort_if(!$this->user->cans('edit_jobs'), 403);

        $required_columns = [
            'gender' => false,
            'dob' => false,
            'country' => false
        ];

        foreach ($required_columns as $key => $value) {
            if ($request->has($key)) {
                $required_columns[$key] = true;
            }
        }

        $section_visibility = [
            'profile_image' => 'no',
            'resume' => 'no',
            'cover_letter' => 'no',
            'terms_and_conditions' => 'no'
        ];

        foreach ($section_visibility as $key => $value) {
            if ($request->has($key)) {
                $section_visibility[$key] = 'yes';
            }
        }

        $job = Job::find($id);
        $job->title = $request->title;
        $job->job_description = $request->job_description;
        $job->job_requirement = $request->job_requirement;
        $job->total_positions = $request->total_positions;
        $job->location_id = $request->location_id;
        $job->category_id = $request->category_id;
        $job->start_date = $request->start_date;
        $job->end_date = $request->end_date;
        $job->status = $request->status;
        $job->job_type_id = $request->job_type_id;
        $job->work_experience_id = $request->work_experience_id;
        $job->pay_type = $request->pay_type;
        $job->pay_according = $request->pay_according;
        $job->starting_salary = $request->starting_salary;
        $job->maximum_salary = $request->maximum_salary;
        $job->required_columns = $required_columns;
        $job->section_visibility = $section_visibility;
        $job->meta_details = [
            'title' => $request->meta_title ?: $job->title,
            'description' => $request->meta_description ?: strip_tags(Str::substr(html_entity_decode($job->job_description), 0, 150))
        ];

        if ($request->show_job_type == 'yes') {
            $job->show_job_type = true;
        }else{
            $job->show_job_type = false;
        }

        if ($request->show_work_experience == 'yes') {
            $job->show_work_experience = true;
        }else{
            $job->show_work_experience = false;
        }

        if ($request->show_salary == 'yes') {
            $job->show_salary = true;
        }else{
            
            $job->show_salary = false;
        }
        
        $job->save();

        if (!is_null($request->skill_id)) {
            JobSkill::where('job_id', $job->id)->delete();

            foreach ($request->skill_id as $skill) {
                $jobSkill = new JobSkill();
                $jobSkill->skill_id = $skill;
                $jobSkill->job_id = $job->id;
                $jobSkill->save();
            }
        }
        // Update Question for job
        $job->questions()->sync($request->question);

        return Reply::redirect(route('admin.jobs.index'), __('menu.jobs') . ' ' . __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->cans('delete_jobs'), 403);

        Job::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function data()
    {
        abort_if(!$this->user->cans('view_jobs'), 403);

        $categories = Job::where('id', '>', '0');

        if (\request('filter_company') != "") {
            $categories->where('company_id', \request('filter_company'));
        }

        if (\request('filter_status') != "") {
            $categories->where('status', \request('filter_status'));
        }
        if (\request('filter_location') != "") {
            $categories->where('location_id', \request('filter_location'));
        }

        $categories->get();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if ($this->user->cans('edit_jobs')) {
                    $action .= '<a href="' . route('admin.jobs.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" onclick="this.blur()" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                $action .= ' <a href="javascript:;" class="btn btn-info btn-circle open-url"
                      data-toggle="tooltip" onclick="this.blur()" data-row-open-url="' . route('jobs.jobDetail', [$row->slug]) . '" data-original-title="' . __('app.copyUrl') . '"><i class="fa fa-copy" aria-hidden="true"></i></a>';

                if ($this->user->cans('delete_jobs')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" onclick="this.blur()" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                if ($row->status == 'expired') {
                    $action .= ' <a href="javascript:;" class="btn btn-circle expire_modal" style="background: #FF8C00; color: white;"
                      data-toggle="tooltip" onclick="this.blur()" data-row-id="' . $row->id . '" data-original-title="' . __('app.refresh') . '"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
                }

                $action .= ' <a href="' . route('admin.jobs.create') . '?duplicate_job=' . $row->id . '" class="btn btn-circle duplicate_job" style="background: #e1b32c; color: white;"
                data-toggle="tooltip" onclick="this.blur()" data-original-title="' . __('app.duplicate') . '"><i class="fa fa-clone" aria-hidden="true"></i></a>';
                return $action;
            })
            ->editColumn('title', function ($row) {
                return ucfirst($row->title);
            })
            ->editColumn('company_id', function ($row) {
                return ucwords($row->company->company_name);
            })
            ->editColumn('location_id', function ($row) {
                return ucfirst($row->location->location) . ' (' . $row->location->country->country_code . ')';
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date->format('d M, Y');
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date->format('d M, Y');
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'active') {
                    return '<label class="badge bg-success">' . __('app.active') . '</label>';
                }
                if ($row->status == 'inactive') {
                    return '<label class="badge bg-danger">' . __('app.inactive') . '</label>';
                }
                if ($row->status == 'expired') {
                    return '<label class="badge" style="background: #FF8C00;">' . __('app.expired') . '</label>';
                }
            })
            ->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function sendEmail(Request $request)
    {
        abort_if(!$this->user->cans('add_jobs'), 403);

        $this->boardColumns = ApplicationStatus::where('status', '!=', 'hired')->get();
        // dd($this->boardColumns);
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->skills = Skill::all();

        return view('admin.jobs.send-email', $this->data);
    }

    public function applicationData(Request $request)
    {
        abort_if(!$this->user->cans('view_job_applications'), 403);
        return DataTables::of($this->filterJobApplications($request))
            ->editColumn('full_name', function ($row) {
                return '<a href="javascript:;" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' . $row->id . '">' . ucwords($row->full_name) . '</a>';
            })
            ->editColumn('title', function ($row) {
                return ucfirst($row->title);
            })
            ->editColumn('location', function ($row) {
                return ucwords($row->location);
            })
            ->editColumn('status', function ($row) {
                return '<span>' . ucwords($row->status) . '</span>
                 <span class="badge badge-pill badge-primary text-white" style= "margin-bottom: -3px; height: 15px; background:' . $row->color . '"> </span>';
            })
            ->addColumn('mail_status', function ($row) use ($request) {
                return $row->jobs()->where('job_id', $request->jobId)->count() == 0 ? '<label class="badge bg-danger">' . __('modules.newJobEmail.mailNotSent') . '</label>' : '<label class="badge bg-success">' . __('modules.newJobEmail.mailSent') . '</label>';
            })
            ->addColumn('checkbox', function ($row) {
                return '
                    <div class="checkbox form-check">
                        <input id="' . $row->id . '" type="checkbox" value="' . $row->id . '" class="form-check-input mail-sent" >
                        <label for="' . $row->id . '"></label>
                    </div>
                ';
            })
            ->rawColumns(['action', 'resume', 'full_name', 'checkbox', 'mail_status', 'status'])
            ->addIndexColumn()
            ->make(true);
    }


    public function sendEmails(Request $request)
    {
        // Please select job for which the emails will be sent
        if ($request->allSelected == 'false') {
            if (!$request->has('selectedIds')) {
                return Reply::error(__('messages.selectApplicantsForEmail'));
            }

            $jobApplications = JobApplication::whereIn('id', $request->selectedIds)->with('jobs');
        } else {
            $jobApplications = JobApplication::with('jobs');
        }
        // dd($request->job_for_email);

        // get jobApplication
        $job = Job::findOrFail($request->job_for_email);


        if ($request->excludeSent == 'true') {
            $jobApplicationsCopy = clone $jobApplications;


            $jobApplicationIds = $jobApplicationsCopy->whereHas('jobs', function ($q) use ($request) {
                $q->where('job_id', $request->job_for_email);
            })->get()->map(function ($jobApplication) {
                return $jobApplication->id;
            })->toArray();

            $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationIds);
        }

        $jobApplications = $jobApplications->get();

        $jobApplicationIds = $jobApplications->map(function ($jobApplication) {
            return $jobApplication->id;
        })->toArray();

        $job->applications()->syncWithoutDetaching($jobApplicationIds);

        $uniqueEmailJobs = $jobApplications->unique(function ($job) {
            return $job['email'];
        });

        if ($jobApplications->count() > 0) {
            Notification::send($uniqueEmailJobs, new NewJobOpening($job));
        }

        return Reply::success(__('messages.emailsSentSuccessfully'));
    }

    public function filterJobApplications($request)
    {
        
        $jobApplicationRec = JobApplication::select('job_applications.id as application')
            ->with('jobs')
            ->where('job_applications.status_id', '!=', ApplicationStatus::where('status', 'hired')->first()->id)
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
            ->distinct()
            ->where('job_applications.job_id', $request->jobId)
            ->where('application_status.status', '!=', 'rejected')
            ->pluck('application')->toArray();

            
        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.email', 'jobs.title', 'job_locations.location', 'application_status.status', 'application_status.color')
            ->with('jobs')
            ->where('job_applications.status_id', '!=', ApplicationStatus::where('status', 'hired')->first()->id)
        
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
            ->distinct();

        // Filter by status
        if ($request->status != 'all' && $request->status != '') {
            $jobApplications = $jobApplications->where('job_applications.status_id', $request->status);
        }

        // Filter By jobs
        if ($request->jobId != 'all' && $request->jobId != '') {
            $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationRec);
        }

        // Filter By skills
        if ($request->skill != 'all' && $request->skill != '') {
            $jobApplications = $jobApplications->whereIn('job_skills.skill_id', gettype($request->skill) == 'array' ? $request->skill : explode(',', $request->skill));
        }

        // Filter by location
        if ($request->location != 'all' && $request->location != '') {
            $jobApplications = $jobApplications->where('jobs.location_id', $request->location);
        }

        // Filter by StartDate
        if ($request->startDate != null && $request->startDate != '') {
            $jobApplications = $jobApplications->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', "$request->startDate");
        }

        // Filter by EndDate
        if ($request->endDate != null && $request->endDate != '') {
            $jobApplications = $jobApplications->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', "$request->endDate");
        }

        // Filter by MailStatus
        if ($request->mailStatus != null && $request->mailStatus != '') {
            if ($request->mailStatus !== 'all') {
                if ($request->mailStatus == 'sent') {
                    $jobApplications = $jobApplications->whereHas('jobs', function ($q) use ($request) {
                        $q->where('job_id', $request->jobId);
                    });
                } else {
                    $jobApplicationsCopy = clone $jobApplications;

                    $jobApplicationIds = $jobApplicationsCopy->whereHas('jobs', function ($q) use ($request) {
                        $q->where('job_id', $request->jobId);
                    })->get()->map(function ($jobApplication) {
                        return $jobApplication->id;
                    })->toArray();

                    $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationIds);
                }
            }
        }

        $jobApplications = $jobApplications->groupBy('job_applications.id');

        return $jobApplications;
    }

    // Refresh Expire Date
    public function refreshDate(UpdateJob $request)
    {
        $job = Job::find($request->id);
        $job->start_date = $request->start_date;
        $job->end_date = $request->end_date;
        $job->status = 'active';
        $job->save();
        return Reply::success(__('messages.updatedSuccessfully'));
    }
}

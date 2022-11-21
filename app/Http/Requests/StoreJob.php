<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

class StoreJob extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [

            'title' => 'required',
            'job_description' => 'required',
            'job_requirement' => 'required',
            'location_id' => 'required',
            'category_id' => 'required',
            'total_positions' => 'required|numeric',
            'start_date' => 'required|date',
            'company' => 'required',
            'end_date' => 'required|date',
            'job_type_id' => 'required',
            'work_experience_id' => 'required',
            'pay_type' => 'required',
            'starting_salary' => 'required',
            'pay_according' => 'required',
        ];
        if (request('pay_type') == 'Range') {
            $rules['maximum_salary'] = 'gt:starting_salary';
        }

        return $rules;
    }

    public function messages()
    {
        $msg =  [
            'category_id.required' => __('menu.jobCategories').' '.__('errors.fieldRequired'),
            'location_id.required' => __('menu.locations').' '.__('errors.fieldRequired'),
            'job_type_id.required' => __('modules.jobs.jobType').' '.__('errors.fieldRequired'),
            'work_experience_id.required' => __('modules.jobs.workExperience').' '.__('errors.fieldRequired'),
        ];

        if (request('pay_type') == 'Range') {
            $msg['starting_salary.required'] = __('modules.jobs.startingSalary').' '.__('errors.fieldRequired');
            $msg['maximum_salary.gt'] = __('modules.jobs.maximumSalary').' '.__('errors.fieldRequired');
        } elseif (request('pay_type') == 'Starting') {
            $msg['starting_salary.required'] = __('modules.jobs.startingSalary').' '.__('errors.fieldRequired');
        } elseif (request('pay_type') == 'Maximum') {
            $msg['starting_salary.required'] = __('modules.jobs.maximumSalary').' '.__('errors.fieldRequired');
        } elseif (request('pay_type') == 'Exact Amount') {
            $msg['starting_salary.required'] =__('modules.jobs.exactSalary').' '.__('errors.fieldRequired');
        }

        return $msg;

    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkill extends CoreRequest
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
        return [
            'category_id' => 'required',
            // 'name' => 'required',
            // 'name.0' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => __('menu.jobCategories').' '.__('errors.fieldRequired'),
            'name' => __('errors.addSkills')
        ];
    }
}

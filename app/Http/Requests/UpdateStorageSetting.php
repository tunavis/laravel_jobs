<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStorageSetting extends FormRequest
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
        if( $this->storage == 'aws'){
        
            return [
                
                'aws_key' => 'required',
                'aws_secret' => 'required',
                'aws_region' => 'required',
                'aws_bucket' => 'required',
            ];
            }      
        return [
            //
        ];
    }
}

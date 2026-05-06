<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Override;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>['sometimes','min:10','string'],
            'description'=>['sometimes','string']
        ];
    }

    #[Override]
    protected function passedValidation()
    {
        $allowed = ['title','description'];

        $extra = array_diff(array_keys($this->all()),$allowed);
        if(!empty($extra)){
            throw new HttpResponseException(response()->json([
                'message'=>'Invalid Fields Provided',
                'extra_field'=>$extra
            ],422));
        }
    }


    public function messages(): array
    {
        return [
            'title.required'=>'title is Required',
            'description'=>'description is Required',
        ];
    }
}

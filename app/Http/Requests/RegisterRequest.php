<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use Override;

class RegisterRequest extends FormRequest
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
        $passwordPolicy = ['required',
            Password::min(8)->
                letters()->
                    mixedCase()->
                        numbers()->uncompromised()];
        return [
            'name'=>['required','string','min:10'],
            'email'=>'required|email|unique:users,email',
            'password'=>$passwordPolicy
        ];
    }

    #[Override]
    protected function passedValidation()
    {
        $allowed = ['email','password','name'];
        $extra = array_diff(array_keys($this->all()),$allowed);

        if(!empty($extra)){
            throw new HttpResponseException(response()->json([
                'message'=>'Invalid Fields Provided',
                'extra_fields'=>$extra
            ],422));
        }
    }

    public function messages(): array
    {
        return [
            'email.required'=>'Email is Required',
            'email.email'=>'Invalide Email formate',
            'password.required'=>'password is required'
        ];
    }
}

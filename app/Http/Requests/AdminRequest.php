<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_unless($this->user()->is_admin, 403);
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
            //
        ];
    }

    //覆盖ValidatesWhenResolvedTrait的源码的同名方法，先validation再authorization
    public function validate()
    {
        $this->prepareForValidation();

        $instance = $this->getValidatorInstance();

        if (! $instance->passes()) {
            $this->failedValidation($instance);
        } elseif (! $this->passesAuthorization()) {
            $this->failedAuthorization();
        }
    }
}

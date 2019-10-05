<?php
namespace Viauco\Base\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Validator;

abstract class Base extends FormRequest
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
            //
        ];
    }

    protected function failedValidation(Validator $validator) 
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'code'    => 422,
                'type'    => 'invalid_params',
                //'message' => 'The params invalid',
                'params'  => request()->all(),
                'error'   => $validator->errors()
            ])
        ); 
    }

    protected function failedAuthorization() 
    {
        throw new AuthorizationException(
            response()->json([
                'success' => false,
                'code'    => 503,
                'type'    => 'access_denied',
                'message' => 'Access denied',
                'params'  => request()->all(),
                'error'   => null 
            ])
        ); 
    }
}

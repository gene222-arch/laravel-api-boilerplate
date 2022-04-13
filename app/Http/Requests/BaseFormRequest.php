<?php

namespace App\Http\Requests;

use App\Traits\HasApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    use HasApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->check();
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->wantsJson())
        {
            $errors = [];
            $errorMessages = $validator->errors()->getMessages();

            foreach ($errorMessages as $key => $value) {
                $errors[$key] = $value[array_key_first($value)];
            }

            throw new HttpResponseException(
                $this->error($errors, 422)
            );
        }
    }
}

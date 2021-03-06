<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class AnswerPostRequest extends FormRequest
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
        Log::debug($this);
        return [
            'body' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image'
        ];
    }

    public function messages() {
        return [
            'images.*.image' => 'Each uploaded file must be an image.'
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = array();
        foreach ($validator->errors()->toArray() as $key => $value) {
            Arr::set($errors, $key, $value);
        }

        throw new HttpResponseException(response()->json(['errors' => $errors], 422));
    }
}

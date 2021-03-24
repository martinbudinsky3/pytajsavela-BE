<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class QuestionPutRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'deleted_tags' => 'nullable|array',
            'deleted_tags.*' => 'integer',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ];
    }

    public function messages() {
        return [
            'tags.*.exists' => 'Each tag must already exist.',
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

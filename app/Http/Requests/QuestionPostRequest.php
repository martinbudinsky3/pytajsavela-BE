<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionPostRequest extends FormRequest
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
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'images' => 'nullable|array',
            'images.*' => 'image'
        ];
    }

    public function messages() {
        return [
            'tags.*.exists' => 'Each tag must already exists.',
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

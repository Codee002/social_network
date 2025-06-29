<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'caption' => 'nullable|string',
            'rule'    => 'required',
            'type'    => 'required',
            'media'   => 'nullable',
            'media.*' => 'file|mimes:jpeg,jpg,png,mp4,webm',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute không được trống',
            'file'     => ':attribute sai định dạng',
            'mimes'    => ':attribute phải là ảnh hoặc video',
        ];
    }

    public function attributes()
    {
        return [
            'caption' => 'Caption',
            'rule'    => 'Quyền riêng tư',
            'type'    => 'Loại bài viết',
            'media'   => 'Phương tiện',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

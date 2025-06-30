<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreConversationRequest extends FormRequest
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
            'thumb' => 'nullable|file|mimes:jpeg,jpg,png',
            'name'  => 'nullable|string',
            'type'  => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute không được trống',
            'file'     => ':attribute sai định dạng',
            'mimes'    => ':attribute phải là ảnh',
        ];
    }

    public function attributes()
    {
        return [
            'thumb' => 'Ảnh nhóm',
            'name'  => 'Tên nhóm',
            // 'type'  => '',
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

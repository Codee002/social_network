<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePaswordRequest extends FormRequest
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
            'oldpass'               => 'required',
            'password'              => 'required|min:8|max:20|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'oldpass.required'               => 'Mật khẩu cũ không được để trống',
            'password.required'              => 'Mật khẩu mới không được để trống',
            'password.min'                   => 'Mật khẩu mới phải từ 8 - 20 ký tự',
            'password.confirmed'             => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required' => 'Mật khẩu xác nhận không được để trống',
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

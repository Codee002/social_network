<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePasswordRequest extends FormRequest
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
            'password' => ['required', 'min:8', 'max:20', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'password.min'       => 'Mật khẩu phải có độ dài từ 8 - 20 ký tự',
            'password.max'       => 'Mật khẩu phải có độ dài từ 8 - 20 ký tự',
            'password.confirmed' => "Mật khẩu nhập lại không trùng khớp",
        ];
    }

    public function attributes()
    {
        return [
            'password'              => 'Mật khẩu',
            'password_confirmation' => 'Mật khẩu',
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

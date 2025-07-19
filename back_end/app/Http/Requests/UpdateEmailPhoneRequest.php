<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmailPhoneRequest extends FormRequest
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
        $user = request()->user();
        $profile = $user->profile;
        return [
            "email" => "required|email|unique:users,email," . $user['id'],
            "phone" => ["required", "regex:/^(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b$/"], "unique:profiles,phone," . $profile['id'],
        ];
    }

    public function messages()
    {
        return [
            "required"     => ':attribute không được rỗng',
            "phone.regex"  => "Số điện thoại không hợp lệ",
            "email.unique" => 'Email đã tồn tại',
            "phone.unique" => 'Số điện thoại đã tồn tại',
        ];
    }

    public function attributes()
    {
        return [
            "phone" => "Số điện thoại",
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

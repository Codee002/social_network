<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBirthdayRequest extends FormRequest
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
            'birthday' => 'required|date|before:today',
        ];
    }

    public function messages()
    {
        return [
            "required"        => ':attribute không được rỗng',
            "birthday.date"   => "Ngày sinh không hợp lệ",
            "birthday.before" => "Ngày sinh phải bé hơn ngày hiện tại",
        ];
    }

    public function attributes()
    {
        return [
            "birthday" => "Ngày sinh",
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

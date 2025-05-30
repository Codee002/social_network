<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Đăng ký
    public function register(StoreAccountRequest $request)
    {
        $request['status'] = "actived";
        $request['role']   = 'user';
        $user              = User::query()->create($request->all());

        return response()->json([
            "success" => true,
            'message' => "Đăng ký thành công",
            "data"    => $user,
        ], 200);

    }

    // Đăng nhập
    public function login(Request $request)
    {
        if (Auth::attemptWhen([
            "username" => $request['username'],
            "password" => $request['password'],
        ], function (User $user) {
            return $user->isNotBanned();
        })) {
            /**
             * @var User $user
             */
            $user  = Auth::user();
            $token = $user->createToken("auth_token")->plainTextToken;
            return response()->json([
                'success' => true,
                "message" => "Đăng nhập thành công",
                "data"    => [
                    'token' => $token,
                    'user'  => $user,
                ],
            ], 200);
        };
        return response()->json([
            "success" => false,
            'message' => "Đăng nhập thất bại",
            'errors' => [
                'message' => "Tên đăng nhập hoặc mật khẩu không đúng"
            ]
        ], 400);
    }
}

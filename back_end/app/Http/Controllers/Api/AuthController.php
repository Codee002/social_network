<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Đăng ký
    public function register(StoreAccountRequest $request)
    {
        $user = null;
        try {
            DB::transaction(function () use ($request, $user) {
                $request['status'] = "actived";
                $request['role']   = 'user';
                $user              = User::query()->create($request->all());

                Profile::create([
                    'user_id' => $user['id'],
                    'name'    => $request['name'],
                ]);
            });
            return response()->json([
                "success" => true,
                'message' => "Đăng ký thành công",
                "user"    => $user,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi đăng ký",
                "data"    => $th->getMessage(),
            ], 400);
        }

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
                'success'     => true,
                "message"     => "Đăng nhập thành công",
                'auth_token'  => $token,
                'token_type ' => 'Bearer',
                'user'        => $user,
            ], 200);
        };
        return response()->json([
            "success" => false,
            'message' => "Đăng nhập thất bại",
            'errors'  => [
                'message' => "Tên đăng nhập hoặc mật khẩu không đúng",
            ],
        ], 400);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => "Đăng xuất thành công",
        ], 200);
    }
}

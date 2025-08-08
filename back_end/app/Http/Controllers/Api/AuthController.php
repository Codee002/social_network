<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FindAccountRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        if (Auth::attempt([
            "username" => $request['username'],
            "password" => $request['password'],
        ])) {
            /**
             * @var User $user
             */
            $user = Auth::user();

            // Kiểm tra xác thực 2 lớp
            if ($user['two_step_auth'] == '0') {
                $token = $user->createToken("auth_token")->plainTextToken;
                return response()->json([
                    'success'       => true,
                    "two_step_auth" => 0,
                    "type"          => "Đăng nhập thành công",
                    'auth_token'    => $token,
                    'token_type '   => 'Bearer',
                    'user'          => $user->load("profile"),
                ], 200);
            } else {
                Auth::logout();
                // Tạo token ngẫu nhiên
                $token = Str::random(60);
                $link  = env('CLIENT_URL') . "auth/checkToken" . '/twoStepAuth/' . $user['id'] . '/' . $token;
                $name  = "Xác thực 2 bước";

                // Gửi gmail
                $data = [
                    'content' => 'Có phải bạn đang đăng nhập? <br>Để đăng nhập hãy xác thực bằng link dưới đây:',
                    'token'   => $link,
                    'user'    => $user,
                    'name'    => $name,
                ];

                try {
                    Mail::send('auth.emailView', $data, function ($message) use ($user) {
                        $message->from(env("MAIL_FROM_ADDRESS"), 'Instories');
                        $message->to($user['email']);
                        $message->subject('Xác thực 2 bước');
                    });
                    DB::transaction(function () use ($user, $token) {
                        /**
                         * @var User $user
                         */
                        $user->update([
                            'email_token' => $token,
                        ]);
                    });
                } catch (\Throwable $th) {
                    return response()->json([
                        "success" => false,
                        'message' => $th->getMessage(),
                    ], 400);
                }
                return response()->json([
                    "success"       => true,
                    'message'       => "Vui lòng kiểm tra email để hoàn thành xác thực 2 bước",
                    "two_step_auth" => 1,
                ], 200);

            }
        }
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

    // Quên mật khẩu
    public function forgot(FindAccountRequest $request)
    {
        $user = User::query()->where("username", $request['username'])
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => "Tài khoản không tồn tại",
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => "Tìm kiếm tài khoản thành công",
            'user'    => $user,
        ], 200);
    }

    // Lấy thông tin tài khoản cần lấy lại mật khẩu
    public function getResetUser(Request $request)
    {
        if (! $request["user_id"]) {
            return response()->json([
                'success' => false,
                'message' => "Chưa nhập thông tin từ trang quên mật khẩu",
            ], 401);
        }

        $user = User::find($request['user_id'])->load('profile');
        return response()->json([
            'success' => true,
            'message' => "Lấy thông tin tài khoản thành công",
            'user'    => $user,
        ], 200);
    }

    // Gui Token
    public function getTokenForgot(Request $request)
    {
        $user = User::find($request['user_id']);

        if (! $user['email']) {
            return response()->json([
                "success" => false,
                "message" => "Tài khoản chưa có email",
            ], 400);
        }
        if ($user['email_active'] == 0) {
            return response()->json([
                "success" => false,
                "message" => "Email chưa kích hoạt",
            ], 400);
        }

        // Tạo token ngẫu nhiên
        $token = Str::random(60);
        $link  = env('CLIENT_URL') . "auth/checkToken" . '/changePassword/' . $user['id'] . '/' . $token;
        $name  = "Lấy lại mật khẩu";

        // Gửi gmail
        $data = [
            'content' => 'Để lấy lại mật khẩu, vui lòng vào link dưới đây:',
            'token'   => $link,
            'user'    => $user,
            'name'    => $name,
        ];
        try {
            Mail::send('auth.emailView', $data, function ($message) use ($user) {
                $message->from(env("MAIL_FROM_ADDRESS"), 'Instories');
                $message->to($user['email']);
                $message->subject('Lấy lại mật khẩu');
            });
            DB::transaction(function () use ($user, $token) {
                /**
                 * @var User $user
                 */
                $user->update([
                    'email_token' => $token,
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage(),
            ], 400);
        }

        return response()->json([
            "success" => true,
            "message" => "Đã gửi thành công! Vui lòng kiểm tra email",
        ], 200);
    }

    // Kiểm tra token
    public function checkToken(Request $request)
    {
        $userId = $request['user_id'];
        $token  = $request['token'];
        $user   = User::query()->find($userId);
        if ($user['email_token'] == $token) {
            try {
                DB::transaction(function () use ($user) {
                    /**
                     * @var User $user
                     */
                    $user->update([
                        'email_token' => null,
                    ]);
                });

                return response()->json([
                    "success" => true,
                    "message" => "Kiểm tra thành công",
                ], 200);

            } catch (\Throwable $th) {
                return response()->json([
                    "success" => false,
                    "message" => $th->getMessage(),
                ], 400);
            }
        } else {
            return response()->json([
                "success" => false,
                "message" => "Không tìm thấy token hoặc user",
            ], 400);
        }
    }

    // Lấy thông tin người dùng
    public function getAccount(Request $request)
    {
        $user = User::find($request['user_id'])->load("profile");

        return response()->json([
            "success" => true,
            "message" => "Lấy thông tin người dùng thành công",
            "user"    => $user,
        ], 200);
    }

    // Đổi mật khẩu
    public function handleReset(UpdatePasswordRequest $request)
    {
        $user = User::find($request['user_id']);

        $password = Hash::make($request['password']);
        $user->update([
            'password'      => $password,
            'two_step_auth' => "0", // Tắt xác thực 2 bước
        ]);

        return response()->json([
            "success" => true,
            "message" => "Đổi mật khẩu thành công",
        ], 200);
    }

    // Gửi email token active
    public function getTokenActive(Request $request)
    {
        $user = User::find($request['user_id']);

        if (! $user['email']) {
            return response()->json([
                "success" => false,
                "message" => "Tài khoản chưa có email",
            ], 400);
        }

        if ($user['email_active'] == 1) {
            return response()->json([
                "success" => false,
                "message" => "Email đã được kích hoạt",
            ], 400);
        }

        // Tạo token ngẫu nhiên
        $token = Str::random(60);
        $link  = env('CLIENT_URL') . "auth/checkToken" . '/emailActive/' . $user['id'] . '/' . $token;
        $name  = "Xác thực email";

        // Gửi gmail
        $data = [
            'content' => 'Để xác thực email, vui lòng vào link dưới đây:',
            'token'   => $link,
            'user'    => $user,
            'name'    => $name,
        ];
        try {
            Mail::send('auth.emailView', $data, function ($message) use ($user) {
                $message->from(env("MAIL_FROM_ADDRESS"), 'Instories');
                $message->to($user['email']);
                $message->subject('Xác thực email');
            });
            DB::transaction(function () use ($user, $token) {
                /**
                 * @var User $user
                 */
                $user->update([
                    'email_token' => $token,
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage(),
            ], 400);
        }

        return response()->json([
            "success" => true,
            "message" => "Đã gửi thành công! Vui lòng kiểm tra email",
        ], 200);
    }

    // Kích hoạt email
    public function activeEmail(Request $request)
    {
        $user = User::find($request['user_id']);
        /**
         * @var User $user
         */
        $user->update([
            'email_active' => '1',
        ]);

        return response()->json([
            "success" => true,
            "message" => "Kích hoạt email thành công",
        ], 200);
    }

    // Kiểm tra token login
    public function checkLoginToken(Request $request)
    {
        $userId = $request['user_id'];
        $token  = $request['token'];
        $user   = User::query()->find($userId);
        if ($user['email_token'] == $token) {
            try {
                DB::transaction(function () use ($user) {
                    /**
                     * @var User $user
                     */
                    $user->update([
                        'email_token' => null,
                    ]);
                });
                Auth::login($user);
                $token = $user->createToken("auth_token")->plainTextToken;
                return response()->json([
                    'success'       => true,
                    "two_step_auth" => 0,
                    "message"          => "Đăng nhập thành công",
                    'auth_token'    => $token,
                    'token_type '   => 'Bearer',
                    'user'          => $user->load("profile"),
                ], 200);

            } catch (\Throwable $th) {
                return response()->json([
                    "success" => false,
                    "message" => $th->getMessage(),
                ], 400);
            }
        } else {
            return response()->json([
                "success" => false,
                "message" => "Không tìm thấy token hoặc user",
            ], 400);
        }
    }
}

<?php
namespace App\Http\Controllers\Api\User;

use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdateBirthdayRequest;
use App\Http\Requests\UpdateEmailPhoneRequest;
use App\Http\Requests\UpdateNameRequest;
use App\Models\Notification;
use App\Models\Relation;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function getOwner(Request $request)
    {
        return response()->json([
            "success" => true,
            'message' => "Lấy thông tin user thành công",
            "user"    => $request->user()->load("profile"),
        ], 200);
    }

    public function getUser(Request $request, String $id)
    {
        $user = User::find($id)->load("profile");
        // $user['friends'] = Relation::getFriends($user['id']);
        return response()->json([
            "success" => true,
            'message' => "Lấy thông tin user thành công",
            "user"    => $user,
        ], 200);
    }

    public function getFriends(String $id)
    {
        $friendList = Relation::getFriends($id);
        return response()->json([
            "success"    => true,
            "message"    => "Lấy bạn bè thành công",
            "friendList" => $friendList,
        ], 200);
    }

    public function getInvited(String $id)
    {
        $listInvited = Relation::getInvited($id);
        return response()->json([
            "success"     => true,
            "message"     => "Lấy lời mời kết bạn thành công",
            "listInvited" => $listInvited,
        ], 200);
    }

    public function getRelation(String $ownerId, String $userId)
    {
        $relation = Relation::getRelationShip($ownerId, $userId);

        if (! $relation) {
            $relation = "stranger";
        }

        return response()->json([
            "success"  => true,
            "message"  => 'Lấy quan hệ thành công',
            "relation" => $relation,
        ], 200);

    }

    public function getPosts(Request $request, $userId)
    {
        $user = User::find($userId);

        $posts = $user->posts()
            ->with("medias")->orderBy("created_at", 'desc')
            ->with('user.profile')->get();

        $views  = [];
        $likes  = [];
        $shares = [];
        foreach ($posts as $post) {
            $likes[$post->id]    = $post->likes()->pluck("user_id")->all();
            $views[$post->id]    = $post->watches()->pluck("user_id")->all();
            $comments[$post->id] = $post->comments()->pluck("user_id")->all();
            $shares[$post->id]   = $post->shares()->pluck("user_id")->all();
        }

        return response()->json([
            "success"  => true,
            "message"  => 'Lấy bài viết thành công',
            "posts"    => $posts,
            "views"    => $views,
            "likes"    => $likes,
            "comments" => $comments,
            "shares"   => $shares,
        ], 200);
    }

    public function getStories(Request $request)
    {
        $user    = $request->user();
        $stories = $user->stories()
            ->with(['user.profile', 'media'])
            ->orderBy("created_at", 'desc')
            ->get();

        $views = [];
        foreach ($stories as $story) {
            $views[$story->id] = $story->watches()->pluck("user_id")->all();
        }

        return response()->json([
            "success" => true,
            'message' => "Lấy tin thành công",
            "stories" => $stories,
            "views"   => $views,
        ], 200);
    }

    public function getNotifications($userId)
    {
        $user          = User::find($userId);
        $notifications = $user->receivedNotifications()->orderBy("created_at", 'desc')->get();

        foreach ($notifications as $noti) {
            $noti['userName']   = $noti->sender->profile->name;
            $noti['userAvatar'] = $noti->sender->profile->avatar;
        }

        return response()->json([
            "success"       => true,
            "message"       => 'Lấy thông báo thành công',
            "notifications" => $notifications,
        ], 200);
    }

    public function readNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        $notification->update([
            'status' => "seen",
        ]);

        return response()->json([
            "success"      => true,
            "message"      => 'Đọc thông báo thành công',
            "notification" => $notification,
        ], 200);
    }

    public function storeAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|file|mimes:jpg,jpeg,png',
            ]);

            $user = $request->user();
            if ($request->has('avatar')) {
                $path = $request['avatar']->store("user/avatars", 'public');
                $url  = Storage::url($path);
            }

            $user->profile()->update([
                'avatar' => $url,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật avatar thành công',
                'user'    => $user,
                'avatar'  => $url,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

    }

    public function storeThumb(Request $request)
    {
        try {
            $request->validate([
                'thumb' => 'required|file|mimes:jpg,jpeg,png',
            ]);

            $user = $request->user();
            if ($request->has('thumb')) {
                $path = $request['thumb']->store("user/thumbs", 'public');
                $url  = Storage::url($path);
            }

            $user->profile()->update([
                'thumb' => $url,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ảnh bìa thành công',
                'user'    => $user,
                'thumb'   => $url,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

    }

    public function changeEmailAndPhone(UpdateEmailPhoneRequest $request)
    {
        $user = $request->user()->load("profile");
        try {
            if ($request['email'] != $user['email']) {
                $user->update([
                    'email'         => $request['email'],
                    "email_active"  => '0',
                    'two_step_auth' => 0,
                ]);
            }
            if ($request['phone'] != $user->profile['phone']) {
                $user->profile()->update([
                    'phone' => $request['phone'],
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật email số điện thoại thành công',
                'email'   => $request['email'],
                'phone'   => $request['phone'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật thông tin user",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function changeName(UpdateNameRequest $request)
    {
        $user = $request->user()->load("profile");
        try {
            if ($request['name'] != $user->profile['name']) {
                $user->profile()->update([
                    'name' => $request['name'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tên thành công',
                'name'    => $request['name'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật tên",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function changeBirthday(UpdateBirthdayRequest $request)
    {
        $user = $request->user()->load("profile");
        try {
            if ($request['birthday'] != $user->profile['birthday']) {
                $user->profile()->update([
                    'birthday' => $request['birthday'],
                ]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Cập nhật ngày sinh thành công',
                'birthday' => $request['birthday'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật ngày sinh",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function changeGender(Request $request)
    {
        $user = $request->user()->load("profile");
        try {
            if ($request['gender'] != $user->profile['gender']) {
                $user->profile()->update([
                    'gender' => $request['gender'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giới tính thành công',
                'gender'  => $request['gender'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật giới tính",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function changeAddress(UpdateAddressRequest $request)
    {
        $user = $request->user()->load("profile");
        try {
            if ($request['address'] != $user->profile['address']) {
                $user->profile()->update([
                    'address' => $request['address'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật mật khẩu thành công',
                'address' => $request['address'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật mật khẩu",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        if (! Hash::check($request['oldpass'], $user->password)) {
            $errors = [
                "oldpass" => [
                    "Mật khẩu không đúng",
                ],
            ];
            return response()->json([
                "success" => false,
                'message' => "Mật khẩu không đúng",
                "errors"  => $errors,
            ], 400);
        }

        $newPass = Hash::make($request['password']);
        try {
            $user->update([
                'password' => $newPass,
            ]);
            return response()->json([
                'success'  => true,
                'message'  => 'Cập nhật mật khẩu thành công',
                'password' => $request['password'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật mật khẩu",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function getFavoritePosts(Request $request)
    {
        $user  = $request->user();
        $posts = [];
        foreach ($user->likes as $like) {
            $posts[] = $like->post()->with("medias")->orderBy("created_at", 'desc')
                ->with('user.profile')->first();
        }

        $views  = [];
        $likes  = [];
        $shares = [];
        foreach ($posts as $post) {
            $likes[$post->id]    = $post->likes()->pluck("user_id")->all();
            $views[$post->id]    = $post->watches()->pluck("user_id")->all();
            $comments[$post->id] = $post->comments()->pluck("user_id")->all();
            $shares[$post->id]   = $post->shares()->pluck("user_id")->all();
        }

        return response()->json([
            "success"  => true,
            "message"  => 'Lấy bài viết thành công',
            "posts"    => $posts,
            "views"    => $views,
            "likes"    => $likes,
            "comments" => $comments,
            "shares"   => $shares,
        ], 200);
    }

    public function changeBio(Request $request)
    {
        $user = $request->user()->load("profile");
        try {
            if ($request['bio'] != $user->profile['bio']) {
                $user->profile()->update([
                    'bio' => $request['bio'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tiểu sử thành công',
                'bio'     => $request['bio'],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật tiểu sử thành công",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    // Tố cáo tài khoản
    public function reportAccount(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $report = Report::create([
                    'user_id'     => $request['user_id'],
                    'received_id' => $request['received_id'],
                    'score'       => $request['score'],
                    'content'     => $request['content'],
                ]);
                return $report;
            });

            // Tạo thông báo
            $admin        = User::getAdmin();
            $contentNotif = "đã tố cáo người dùng";
            $typeNotif    = "user";
            $notification = Notification::createNotification($result['user_id'], $admin->id,
                $contentNotif, $typeNotif, $result['received_id']);
            broadcast(new NewNotificationEvent($notification))->toOthers();

            return response()->json([
                'success' => true,
                'message' => "Tố cáo người dùng thành công",
                'result'  => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tố cáo người dùng",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

}

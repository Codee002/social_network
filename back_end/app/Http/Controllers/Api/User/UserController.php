<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Http\Request;
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
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

    }

}

<?php
namespace App\Http\Controllers\Api\User;

use App\Events\NewCommentRequest;
use App\Events\NewLikeRequest;
use App\Events\NewNotificationEvent;
use App\Events\NewPostRequest;
use App\Events\NewShareRequest;
use App\Events\NewViewRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StorePostRequest;
use App\Models\Comment;
use App\Models\CommentMedia;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Relation;
use App\Models\Report;
use App\Models\Share;
use App\Models\User;
use App\Models\Watch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(StorePostRequest $request)
    {
        try {
            $owner  = $request->user();
            $result = DB::transaction(function () use ($request, $owner) {
                $request['user_id'] = $owner['id'];
                $post               = Post::query()->create(
                    $request->all(),
                );

                Log::info('Reqeust', [$request->medias]);

                if ($request->has("media")) {
                    foreach ($request->media as $media) {
                        $path = $media->store('posts', 'public');
                        $url  = Storage::url($path);
                        Log::info($media->getMimeType());
                        Log::info($media);
                        Log::info('Mime TYPE', [$media->getMimeType()]);

                        PostMedia::create([
                            'post_id' => $post->id,
                            'path'    => $url,
                            'type'    => str_starts_with($media->getMimeType(), 'video/') ? 'video' : 'image',
                        ]);
                    }
                }

                return $post->load('medias');
            });

            broadcast(new NewPostRequest($result))->toOthers();

            return response()->json([
                "success" => true,
                'message' => "Tạo bài viết thành công",
                "post"    => $result,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tạo bài viết",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    public function getPostDetail($postId)
    {
        $post = Post::find($postId)
            ->load("medias")
            ->load('user.profile');

        $post['likes']    = $post->likes()->pluck("user_id")->all();
        $post['views']    = $post->watches()->pluck("user_id")->all();
        $post['comments'] = $post->comments()->orderBy("created_at", 'desc')->get();
        $post['shares']   = $post->shares()->orderBy("created_at", 'desc')->get();

        foreach ($post->comments as $comment) {
            $user                   = User::find($comment['user_id']);
            $comment['user_name']   = $user->profile->name;
            $comment['user_avatar'] = $user->profile->avatar;
            $comment['medias']      = $comment->medias;
        }

        return response()->json([
            "success" => true,
            "message" => 'Lấy bài viết thành công',
            "post"    => $post,
        ], 200);
    }

    public function storeComment(StoreCommentRequest $request)
    {
        // $owner              = $request->user();
        // $request['user_id'] = $owner->id;
        try {
            $result = DB::transaction(function () use ($request) {
                $comment = Comment::create($request->all());

                if ($request->has('media')) {
                    foreach ($request->media as $media) {
                        $path = $media->store("comments", 'public');
                        $url  = Storage::url($path);

                        CommentMedia::create([
                            'comment_id' => $comment->id,
                            'path'       => $url,
                            'type'       => str_starts_with($media->getMimeType(), 'video/') ? 'video' : 'image',
                        ]);
                    }
                }
                return $comment->load('medias');
            });

            // Tạo thông báo
            $post = Post::find($request['post_id']);
            if ($request['user_id'] != $post['user_id']) {
                $contentNotif = "đã bình luận bài viết của bạn";
                $typeNotif    = "post";
                $notification = Notification::createNotification($result['user_id'], $result->post->user_id,
                    $contentNotif, $typeNotif, $result['post_id']);
                broadcast(new NewNotificationEvent($notification))->toOthers();
            }
            broadcast(new NewCommentRequest($request['post_id'], $result))->toOthers();
            return response()->json([
                'success' => true,
                'message' => "Tạo bình luận thành công",
                'comment' => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tạo bình luận",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function storeLike(Request $request)
    {
        try {
            $likeStatus = null;
            $result     = DB::transaction(function () use ($request, $likeStatus) {
                $like = Like::query()
                    ->where("user_id", $request['user_id'])
                    ->where("post_id", $request['post_id'])
                    ->first();
                // dd($like);
                if (! $like) {
                    $newLike = Like::create([
                        'user_id' => $request['user_id'],
                        'post_id' => $request['post_id'],
                        'score'   => $request['score'],
                    ]);
                    $likeStatus = true;
                    $temp       = $newLike;
                } else {
                    $temp = $like;
                    $like->delete();
                    $likeStatus = false;

                }

                // Tạo thông báo
                // Nếu người dùng like và không phải là chủ bài viết
                $post = Post::find($request['post_id']);
                if ($likeStatus && $request['user_id'] != $post['user_id']) {
                    $contentNotif = "đã thích bài viết của bạn";
                    $typeNotif    = "post";
                    $notification = Notification::createNotification($temp['user_id'], $temp->post->user_id,
                        $contentNotif, $typeNotif, $temp['post_id']);
                    broadcast(new NewNotificationEvent($notification))->toOthers();
                }

                return $newLike ?? $temp;
            });

            broadcast(new NewLikeRequest($request['post_id'], $result))->toOthers();

            return response()->json([
                'success' => true,
                'message' => "Tương tác thành công",
                'result'  => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tương tác với bài viết",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    public function storeView(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $view = Watch::query()
                    ->where("user_id", $request['user_id'])
                    ->where("post_id", $request['post_id'])
                    ->first();

                // dd($view);
                if (! $view) {
                    $newView = Watch::create([
                        'user_id' => $request['user_id'],
                        'post_id' => $request['post_id'],
                        'score'   => $request['score'],
                    ]);
                }
                return $view ?? $newView;
            });

            broadcast(new NewViewRequest($request['post_id'], $result))->toOthers();

            return response()->json([
                'success' => true,
                'message' => "Cập nhật view thành công",
                'result'  => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật view với bài viết",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function storeShare(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $share = Share::create([
                    'user_id' => $request['user_id'],
                    'post_id' => $request['post_id'],
                    'score'   => $request['score'],
                ]);
                return $share;
            });

            // Tạo thông báo
            $post = Post::find($request['post_id']);
            if ($request['user_id'] != $post['user_id']) {
                $contentNotif = "đã chia sẻ bài viết của bạn";
                $typeNotif    = "post";
                $notification = Notification::createNotification($result['user_id'], $result->post->user_id,
                    $contentNotif, $typeNotif, $result['post_id']);
                broadcast(new NewNotificationEvent($notification))->toOthers();
            }

            broadcast(new NewShareRequest($request['post_id'], $result))->toOthers();

            return response()->json([
                'success' => true,
                'message' => "Tương tác thành công",
                'result'  => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tương tác với bài viết",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    //  Tố cáo
    public function storeReport(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $report = Report::create([
                    'user_id' => $request['user_id'],
                    'post_id' => $request['post_id'],
                    'score'   => $request['score'],
                    'content' => $request['content'],
                ]);
                return $report;
            });

            // Tạo thông báo
            $admin        = User::getAdmin();
            $contentNotif = "đã tố cáo 1 bài viết";
            $typeNotif    = "post";
            $notification = Notification::createNotification($result['user_id'], $admin->id,
                $contentNotif, $typeNotif, $result['post_id']);
            broadcast(new NewNotificationEvent($notification))->toOthers();

            return response()->json([
                'success' => true,
                'message' => "Tố cáo bài viết thành công",
                'result'  => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tố cáo bài viết",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    // Lấy Dashboard Post
    public function getDashBoardPosts(Request $request)
    {
        $user          = $request->user();
        $listFriendIds = Relation::getFriendsId($user['id']);
        $perPage       = $request->get('perPage', 10);
        $posts         = Post::with(['medias', 'user.profile'])
        // Lấy các bài public
            ->where("rule", "public")
            ->where("status", 'actived')
            ->orWhere("user_id", $user['id'])
            ->orWhere(function ($q) use ($listFriendIds) {

                // Lấy các bài của bạn bè
                $q->where('rule', 'friend')->whereIn('user_id', $listFriendIds);
            })

        // Tính tổng điểm
            ->select('posts.*')
            ->selectRaw('
        COALESCE((SELECT SUM(score) FROM likes WHERE likes.post_id = posts.id), 0) +
        COALESCE((SELECT SUM(score) FROM watches WHERE watches.post_id = posts.id), 0) +
        COALESCE((SELECT SUM(score) FROM comments WHERE comments.post_id = posts.id), 0) +
        COALESCE((SELECT SUM(score) FROM shares WHERE shares.post_id = posts.id), 0) -
        COALESCE((SELECT SUM(score) FROM reports WHERE reports.post_id = posts.id), 0) AS total_score
    ')
            ->orderByDesc('total_score')
            ->paginate($perPage);

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
            'message'  => "Lấy bài viết cho trang chủ thành công",
            "posts"    => $posts,
            "views"    => $views,
            "likes"    => $likes,
            "comments" => $comments,
            "shares"   => $shares,
        ], 200);
    }
}

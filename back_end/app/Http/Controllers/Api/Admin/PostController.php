<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Lấy Post
    public function getPosts(Request $request)
    {
        $user    = $request->user();
        $perPage = $request->get('perPage', 10);
        $posts   = Post::with(['medias', 'user.profile'])
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

        $views   = [];
        $likes   = [];
        $shares  = [];
        $reports = [];
        foreach ($posts as $post) {
            $likes[$post->id]    = $post->likes()->pluck("user_id")->all();
            $views[$post->id]    = $post->watches()->pluck("user_id")->all();
            $comments[$post->id] = $post->comments()->pluck("user_id")->all();
            $shares[$post->id]   = $post->shares()->pluck("user_id")->all();
            $reports[$post->id]  = $post->reports()->pluck("user_id")->all();
        }

        return response()->json([
            "success"    => true,
            'message'    => "Lấy bài viết cho trang chủ thành công",
            "pagination" => $posts,
            "views"      => $views,
            "likes"      => $likes,
            "comments"   => $comments,
            "shares"     => $shares,
            "reports"    => $reports,
        ], 200);
    }

    // Thay đổi trạng thái bài viết
    public function changePostStatus(Request $request)
    {
        $admin = User::getAdmin();
        if ($admin->id != $request->user()->id) {
            return response()->json([
                "success" => false,
                'message' => "Bạn không thể thực hiện hành vi này!",
            ], 405);
        }

        $post = Post::find($request['post_id']);
        $post->update([
            'status' => $request['status'],
        ]);

        return response()->json([
            "success" => true,
            'message' => "Thay đổi trạng thái thành công",
            "status"  => $request['status'],
        ], 200);

    }

    public function getReportPosts(Request $request)
    {
        $perPage    = $request->get('perPage', 10);
        $pagination = Report::with(["post.medias", "user.profile"])->where("post_id", "!=", null)
            ->orderBy("created_at", "desc")->paginate($perPage);
        return response()->json([
            'success'    => true,
            'message'    => "Lấy danh sách tố cáo bài viết thành công",
            'pagination' => $pagination,
        ]);
    }
}

<?php
namespace App\Http\Controllers\Api\User;

use App\Events\NewStoryRequest;
use App\Events\NewViewStoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoryRequest;
use App\Models\Relation;
use App\Models\Story;
use App\Models\StoryMedia;
use App\Models\Watch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function getDashboardStories(Request $request)
    {
        $currentUserId = $request->user()->id;
        $friendIds     = Relation::getFriendsId($currentUserId);

        $stories = Story::with('user.profile')
            ->with("media")
            ->where('stories.rule', 'friend')
            ->whereIn('stories.user_id', $friendIds)
            ->orwhere('stories.user_id', $currentUserId)
            ->leftJoin('watches as w', function ($join) use ($currentUserId) {
                $join->on('stories.id', '=', 'w.story_id')
                    ->where('w.user_id', '=', $currentUserId);
            })
            ->orderByRaw('CASE WHEN w.id IS NULL THEN 0 ELSE 1 END') // Ưu tiên chưa xem (NULL là chưa có bản ghi)
            ->orderBy('stories.created_at', 'desc')
            ->select('stories.*')
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

    public function createStory(StoreStoryRequest $request)
    {
        $owner     = $request->user();
        $friendIds = Relation::getFriendsId($owner['id']);
        try {
            $result = DB::transaction(function () use ($request, $owner) {
                $request['user_id'] = $owner['id'];

                // Chưa làm rule nên để mặc định là friend
                $request['rule'] = "friend";
                $story           = Story::query()->create(
                    $request->all(),
                );

                if ($request->has('media')) {
                    $path = $request['media']->store("story", 'public');
                    $url  = Storage::url($path);
                }
                StoryMedia::create([
                    'story_id' => $story->id,
                    'path'     => $url,
                    'type'     => str_starts_with($request['media']->getMimeType(), 'video/') ? 'video' : 'image',
                ]);

                return $story->load('media')->load('user.profile');
            });

            // Tự gửi bản thân
            broadcast(new NewStoryRequest($result, $owner['id']))->toOthers();
            foreach ($friendIds as $id) {
                // Gửi đến bạn bè
                broadcast(new NewStoryRequest($result, $id))->toOthers();
            }

            return response()->json([
                "success" => true,
                'message' => "Tạo tin thành công",
                "story"   => $result,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi tạo tin",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function getStoryDetail(Request $request, $storyId)
    {
        $story = Story::find($storyId)
            ->load("media")
            ->load('user.profile');

        $views = $story->watches()->get();

        return response()->json([
            "success" => true,
            "message" => 'Lấy story thành công',
            "story"   => $story,
            "views"   => $views,
        ], 200);

    }

    public function storeView(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $view = Watch::query()
                    ->where("user_id", $request['user_id'])
                    ->where("story_id", $request['story_id'])
                    ->first();

                // dd($view);
                if (! $view) {
                    $newView = Watch::create([
                        'user_id'  => $request['user_id'],
                        'story_id' => $request['story_id'],
                        'score'    => $request['score'],
                    ]);
                }
                return $view ?? $newView;
            });
            $story = Story::find($request['story_id']);
            broadcast(new NewViewStoryRequest($request['story_id'], $result, $story['user_id']))->toOthers();

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
}

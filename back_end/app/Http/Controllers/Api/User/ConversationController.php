<?php
namespace App\Http\Controllers\Api\User;

use App\Events\ReceiveMessageRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConversationController extends Controller
{
    public function findConversationUser(Request $request)
    {
        $conversation = Conversation::findConversationUser($request->user()->id, $request->user_id);
        if ($conversation) {
            return response()->json([
                'success'      => true,
                'message'      => "Lấy cuộc hội thoại thành công",
                'conversation' => $conversation,
            ]);
        } else {
            try {
                $result = DB::transaction(function () use ($request) {
                    $conversation = Conversation::query()->create([
                        'type' => 'friend',
                    ]);

                    $owner = $request->user();
                    $conversation->addUser($owner->id);
                    $conversation->addUser($request->user_id);
                    return $conversation;
                });
                return response()->json([
                    'success'      => true,
                    'message'      => "Tạo hội thoại thành công",
                    'conversation' => $result,
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    "success" => false,
                    'message' => "Có lỗi tạo hội thoại",
                    "data"    => $th->getMessage(),
                ], 400);
            }

        }
    }

    public function getConversation(Request $request)
    {
        try {
            $owner         = $request->user();
            $conversations = $owner->conversations()
                ->orderBy("updated_at", 'desc')
                ->get();

            foreach ($conversations as $conversation) {
                $conversation['user']    = $conversation->users()->where("user_id", '!=', $owner->id)->first()->profile;
                $conversation['message'] = $conversation->messages()->orderBy("id", 'desc')->first();
            }
            return response()->json([
                'success'       => true,
                'message'       => "Lấy danh sách hội thoại thành công",
                'conversations' => $conversations,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi lấy danh sách hội thoại",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    public function getMessage($conversationId)
    {
        try {
            $conversation             = Conversation::find($conversationId);
            $conversation['users']    = $conversation->users()->with('profile')->get();
            $conversation['messages'] = $conversation->messages()->with('medias')->orderBy("created_at", 'desc')->get();
            return response()->json([
                'success'      => true,
                'message'      => "Lấy danh sách hội thoại thành công",
                'conversation' => $conversation,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi lấy tin nhắn",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function sendMessage(StoreMessageRequest $request)
    {
        $owner = Auth::user();
        try {
            $result = DB::transaction(function () use ($request) {
                $message = Message::create($request->all());
                $message->conversation->update([
                    'updated_at' => now(),
                ]);

                if ($request->has('media')) {
                    foreach ($request->media as $media) {
                        $path = $media->store("conversations", 'public');
                        $url  = Storage::url($path);

                        MessageMedia::create([
                            'message_id' => $message->id,
                            'path'       => $url,
                            'type'       => str_starts_with($media->getMimeType(), 'video/') ? 'video' : 'image',
                        ]);
                    }
                }
                return $message->load('medias');
            });

            $usersIdArray = $result->conversation->users()->pluck('user_id')->all();
            broadcast(new ReceiveMessageRequest($result, $owner->id));

            foreach ($usersIdArray as $userId) {
                if ($userId != $owner->id) {
                    broadcast(new ReceiveMessageRequest($result, $userId));
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Gửi tin nhắn thành công",
                'message' => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi gửi tin nhắn",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }
}

<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                ->orderBy("created_at", 'desc')
                ->with('messages')
                ->get();
            foreach ($conversations as $conversation) {
                $conversation['user'] = $conversation->users()->where("user_id", '!=', $owner->id)->first()->profile;
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
            $conversation = Conversation::find($conversationId)->load('messages');
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

    public function sendMessage(Request $request)
    {
        // dd($request->all());
        try {
            $result = DB::transaction(function () use ($request) {
                $message = Message::create($request->all());
                return $message;
            });
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

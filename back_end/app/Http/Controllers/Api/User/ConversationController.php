<?php
namespace App\Http\Controllers\Api\User;

use App\Events\CallAcceptRequest;
use App\Events\NewCallRequest;
use App\Events\ReceiveMessageRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

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

            foreach ($conversation['messages'] as $message) {
                $message['userName'] = $message->user->profile->name;
            }
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

    public function createConversation(StoreConversationRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $url = null;

                if ($request->has('thumb')) {
                    $path = $request['thumb']->store("conversations", 'public');
                    $url  = Storage::url($path);
                }

                $conversation = Conversation::query()->create([
                    'name'  => $request['name'],
                    'type'  => $request['type'],
                    'thumb' => $url,
                ]);

                foreach ($request['user'] as $index => $user) {
                    $conversation->addUser($user, $request['role'][$index], now(), $request['has_created'][$index]);
                }

                return $conversation;
            });

            return response()->json([
                'success'      => true,
                'message'      => "Tạo cuộc trò chuyện thành công",
                'conversation' => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi tạo cuộc trò chuyện",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function startCall(Request $request)
    {

        $formUser     = $request->user();
        $conversation = Conversation::find($request['conversation_id']);
        $message      = Message::create([
            'user_id'         => $formUser->id,
            'content'         => $request['content'],
            'type'            => $request['type'],
            'conversation_id' => $request['conversation_id'],
        ]);
        try {
            // Thông tin agora Channel
            $ids     = Str::random(10);
            $channel = "call_" . $ids;

            // Token bảo mật
            $uid        = $formUser->id;
            $expireTime = time() + 3600;
            Log::info('AGORA_APP_ID: ' . env('AGORA_APP_ID'));
            Log::info('AGORA_CERTIFICATE: ' . env('AGORA_CERTIFICATE'));
            $token = RtcTokenBuilder::buildTokenWithUid(
                env('AGORA_APP_ID'),
                env('AGORA_CERTIFICATE'),
                $channel,
                $uid, //id người dùng
                RtcTokenBuilder::RolePublisher,
                $expireTime,
            );
            Log::info('Generated Token FromUser: ' . $token);
            Log::info('Generated Channel FromUser: ' . $channel);
            // Ảnh call
            $thumbForm = "null";
            foreach ($conversation->users as $user) {
                if ($user['id'] != $formUser->id) {
                    if ($conversation->type == "friend") {
                        $thumbForm = $user->profile->avatar ?? "null";
                    } else {
                        $thumbForm = $conversation->thumb ?? $formUser->profile->avatar ?? "null";
                    }
                }
            }

            // Ảnh của người nhận
            $thumbTo = $conversation->thumb ?? $formUser->profile->avatar ?? "null";

            // Gửi sự kiện đến từng user
            foreach ($conversation->users as $user) {

                if ($user['id'] != $formUser->id) {

                    broadcast(new NewCallRequest($formUser, $user['id'], $channel, $message, $thumbTo))->toOthers();
                }
            }
            return response()->json([
                'success' => true,
                'message' => $message,
                'channel' => $channel,
                'token'   => $token,
                'uid'     => $uid,
                'thumb'   => $thumbForm,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi tạo cuộc gọi",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    public function acceptCall(Request $request)
    {
        try {

            $user       = $request->user();
            $channel    = $request->channel;
            $message    = Message::find($request['message_id']);
            $expireTime = time() + 3600;
            $token      = RtcTokenBuilder::buildTokenWithUid(
                env('AGORA_APP_ID'),
                env('AGORA_CERTIFICATE'),
                $channel,
                $user->id, //id người dùng
                RtcTokenBuilder::RolePublisher,
                $expireTime,
            );
            Log::info('Generated Token ToUser: ' . $token);
            Log::info('Generated Channel ToUser: ' . $channel);
            broadcast(new CallAcceptRequest($message, $channel))->toOthers();
            return response()->json([
                'token' => $token,
                'uid'   => $user->id,

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi chấp nhận cuộc gọi",
                "data"    => $th->getMessage(),
            ], 400);
        }

    }

    public function sendPostMessage(Request $request)
    {
        $owner   = Auth::user();
        $usersId = $request['user'];
        try {

            foreach ($usersId as $userId) {

                // Tìm cuộc trò chuyện
                $conversation = Conversation::findConversationUser($owner->id, $userId);
                if (! $conversation) {

                    // Tạo cuộc trò chuyện
                    $conversation = Conversation::query()->create([
                        'type' => 'friend',
                    ]);
                    $conversation->addUser($owner->id);
                    $conversation->addUser($userId);
                }

                $message = Message::create([
                    "conversation_id" => $conversation->id,
                    "user_id"         => $owner->id,
                    "content"         => $request['content'],
                    'type'            => "message",
                ]);
                $message->conversation->update([
                    'updated_at' => now(),
                ]);

                // Gửi broadcast cho bản thân
                broadcast(new ReceiveMessageRequest($message->load("medias"), $owner->id));

                // Gửi broadcast cho người nhận
                broadcast(new ReceiveMessageRequest($message->load("medias"), $userId));
            }

            return response()->json([
                'success' => true,
                'message' => "Gửi tin nhắn thành công",
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi gửi tin nhắn",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }
}

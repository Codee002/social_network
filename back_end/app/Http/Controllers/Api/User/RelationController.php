<?php
namespace App\Http\Controllers\Api\User;

use App\Events\NewNotificationEvent;
use App\Events\ReceiveRelationRequest;
use App\Events\SendRelationRequest;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelationController extends Controller
{
    public function addRelation(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $request['sender_id'] = $request->user()->id;
                $relation             = Relation::query()->create($request->all());

                // Tạo thông báo
                $contentNotif = "đã gủi lời mời kết bạn";
                $typeNotif    = "relation";
                $notification = Notification::createNotification($request['sender_id'], $request['received_id'],
                $contentNotif, $typeNotif, $relation->id);
                broadcast(new NewNotificationEvent($notification))->toOthers();

                broadcast(new ReceiveRelationRequest($relation))->toOthers();
                broadcast(new SendRelationRequest($relation))->toOthers();
                return $relation;
            });
            return response()->json([
                'success'  => true,
                'message'  => "Gửi quan hệ thành công",
                'relation' => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi gửi quan hệ",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

    public function changeRelation(Request $request)
    {
        // dd($request->all());
        try {
            $result = DB::transaction(function () use ($request) {
                $relation     = Relation::find($request['relation_id']);
                $tempRelation = $relation;
                if ($request['status'] == "reject") {
                    $relation->delete();
                    $tempRelation['status'] = 'reject';
                } else if ($request['status'] == 'delete') {
                    $relation->delete();
                    $tempRelation['status'] = 'delete';
                } else {
                    $relation->update($request->all());
                }
                // broadcast(new NewRelationRequest($request->user(), $tempRelation))->toOthers();
                broadcast(new ReceiveRelationRequest($tempRelation))->toOthers();
                broadcast(new SendRelationRequest($tempRelation))->toOthers();
                return $tempRelation;
            });
            return response()->json([
                'success'  => true,
                'message'  => "Cập nhật quan hệ thành công",
                'relation' => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => "Có lỗi khi cập nhật quan hệ",
                "data"    => $th->getMessage(),
            ], 400);
        }
    }

}

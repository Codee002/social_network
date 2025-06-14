<?php
namespace App\Http\Controllers\Api\User;

use App\Events\NewRelationRequest;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
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
                broadcast(new NewRelationRequest($request->user(), $relation))->toOthers();
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
                }
                $relation->update($request->all());
                broadcast(new NewRelationRequest($request->user(), $tempRelation))->toOthers();
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

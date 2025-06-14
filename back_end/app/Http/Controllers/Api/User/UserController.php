<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Http\Request;

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
        return response()->json([
            "success" => true,
            'message' => "Lấy thông tin user thành công",
            "user"    => $user,
        ], 200);
    }

    public function getFriends(String $id)
    {
        $user = User::find($id)->load("sendRelations");
        // $user->getFriends();
        // dd($user->sendRelations);
        // dd($user->sendRelations()
        //         ->where("type", 'friend')
        //         ->where("status", "completed")
        //         ->get());
        return response()->json([
            "data" => $user->sendRelations()
                ->where("type", 'friend')
                ->where("status", "completed")
                ->get(),
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
}

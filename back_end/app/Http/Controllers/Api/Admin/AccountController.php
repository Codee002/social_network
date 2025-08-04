<?php
namespace App\Http\Controllers\Api\Admin;

use App\Events\ChangeStatusAccount;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getAccounts(Request $request)
    {
        $perPage  = $request->get('perPage', 10);
        $accounts = User::with('profile')->paginate($perPage);

        return response()->json([
            'success'    => true,
            'message'    => "Lấy danh sách tài khoản thành công",
            'pagination' => $accounts,
        ]);
    }

    public function getReportAccounts(Request $request)
    {
        $perPage    = $request->get('perPage', 10);
        $pagination = Report::with(["receiver.profile", "user.profile"])->where("received_id", "!=", null)
            ->orderBy("created_at", "desc")->paginate($perPage);
        return response()->json([
            'success'    => true,
            'message'    => "Lấy danh sách tố cáo tài khoản thành công",
            'pagination' => $pagination,
        ]);
    }

    // Thay đổi trạng thái tài khoản
    public function changeAccountStatus(Request $request)
    {
        $admin = User::getAdmin();
        if ($admin->id != $request->user()->id) {
            return response()->json([
                "success" => false,
                'message' => "Bạn không thể thực hiện hành vi này!",
            ], 405);
        }

        $user = User::find($request['user_id']);
        $user->update([
            'status' => $request['status'],
        ]);

        broadcast(new ChangeStatusAccount($user))->toOthers();

        return response()->json([
            "success" => true,
            'message' => "Thay đổi trạng thái thành công",
            "status"  => $request['status'],
        ], 200);

    }

}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'content',
        'sender_id',
        'received_id',
        'url_id',
        'status',
    ];

    // ------------ Relation ------------
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_id', 'id');
    }

    // --------------- Function --------
    /**
     * senderId: Người tạo ra sự kiện
     * receiverId: Người sẽ nhận sự kiện
     * content: Thông báo sẽ phát ra
     * type: Loại sự kiện
     * status: Đã xem, chưa xem
     */
    public static function createNotification($senderId, $receiverId, $content, $type, $urlId, $status = 'received')
    {
        $notification = Notification::create([
            'sender_id'   => $senderId,
            'received_id' => $receiverId,
            'content'     => $content,
            'url_id'      => $urlId,
            'type'        => $type,
            'status'      => $status,
        ]);
        return $notification;
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\UserLineNotify;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class UserLineNotifyController extends Controller
{
 
    
    public function index()
    {
        // ดึงข้อมูลของผู้ใช้ที่ล็อกอินอยู่
        $user_id = Auth::id();

        // ดึงข้อมูล LINE Notify ที่เกี่ยวข้องกับผู้ใช้ที่ล็อกอินอยู่
        $notifications = UserLineNotify::where('user_id', $user_id)->with('user')->get();

        // ส่งข้อมูลไปยัง Vue component
        return Inertia::render('UserLineNotify/Index', [
            'notifications' => $notifications
        ]);
    }
public function create()
{
    // ส่งข้อมูล user_id ของผู้ใช้ที่ล็อกอิน
    return Inertia::render('UserLineNotify/Create', [
        'user_id' => auth()->id(),  // ส่ง user_id ของผู้ใช้ที่ล็อกอิน
    ]);
} 
public function store(Request $request)
{
    $validated = $request->validate([
        'line_notify_token' => 'required|string|max:255',
    ]);

    $token = $validated['line_notify_token'];
    $message = '🎉 ขอบคุณที่สมัครรับการแจ้งเตือนจากเรา!';

    try {
        // ส่งข้อความไป LINE Notify
        $this->sendLineNotification($token, $message);

        // ดึงข้อมูล user_id ของผู้ใช้ที่ล็อกอิน
        $user_id = auth()->id();

        // ตรวจสอบว่าผู้ใช้มีข้อมูลอยู่ในระบบหรือไม่
        $userLineNotify = UserLineNotify::where('user_id', $user_id)->first();

        if ($userLineNotify) {
            // ถ้ามีข้อมูลแล้ว ทำการอัปเดต
            $userLineNotify->update([
                'line_notify_token' => $token,
            ]);

            $message = 'Token ของคุณได้ถูกอัปเดตเรียบร้อยแล้ว!';
        } else {
            // ถ้าไม่มีข้อมูล ให้สร้างข้อมูลใหม่
            $userLineNotify = UserLineNotify::create([
                'user_id' => $user_id,
                'line_notify_token' => $token,
            ]);

            $message = 'สมัครรับการแจ้งเตือนเรียบร้อยแล้ว!';
        }

        // ส่ง success = true กลับไป
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    } catch (\Exception $e) {
        // ส่ง success = false กลับไป
        return response()->json([
            'success' => false,
            'error_message' => 'ส่งข้อความไปยัง LINE Notify ไม่สำเร็จ: ' . $e->getMessage(),
        ]);
    }
}




private function sendLineNotification($token, $message)
{
    $lineApiUrl = 'https://notify-api.line.me/api/notify';
    $data = ['message' => $message];
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/x-www-form-urlencoded',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $lineApiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // ตรวจสอบหาก HTTP code ไม่ใช่ 200 (Success)
    if ($httpCode != 200) {
        $errorMessage = "ไม่สามารถส่งข้อความไปยัง LINE Notify ได้ (HTTP Code: $httpCode)";
        // หากต้องการสามารถเก็บข้อความจากการตอบกลับของ LINE API ได้
        $errorResponse = json_decode($result, true);
        if (isset($errorResponse['error'])) {
            $errorMessage = $errorResponse['error'];
        }
        curl_close($ch);
        throw new \Exception($errorMessage); // ส่งข้อผิดพลาดพร้อมข้อความ
    }

    curl_close($ch);
    return $result;
}

    // // แสดงข้อมูลเฉพาะตาม id
    // public function show(UserLineNotify $userLineNotify)
    // {
    //     return Inertia::render('UserLineNotify/Show', [
    //         'notification' => $userLineNotify
    //     ]);
    // }

    // // แสดงฟอร์มแก้ไขข้อมูล
    // public function edit(UserLineNotify $userLineNotify)
    // {
    //     return Inertia::render('UserLineNotify/Edit', [
    //         'notification' => $userLineNotify
    //     ]);
    // }

    // อัปเดตข้อมูลที่มีอยู่
    public function update(Request $request, UserLineNotify $userLineNotify)
    {
        $request->validate([
            'line_notify_token' => 'required|string|max:255',
        ]);

        $userLineNotify->update($request->all());

        return redirect()->route('user_line_notify.index')->with('success', 'Line Notify token updated successfully.');
    }

    // ลบข้อมูล
    public function destroy($id)
    {   $userLineNotify = UserLineNotify::find($id);
        $userLineNotify->delete();
        return redirect()->route('user_line_notify.index')->with('success', 'Line Notify token deleted successfully.');
    }
}

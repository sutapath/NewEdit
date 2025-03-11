<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLineNotify;

class LineNotifyController extends Controller
{
    // ฟังก์ชันสำหรับส่งการแจ้งเตือน
    public function sendNotification($userId, Request $request)
    {
        // ดึงข้อมูล Line Notify Token ของผู้ใช้
        $userLineNotify = UserLineNotify::where('user_id', $userId)->first();

        if ($userLineNotify && $userLineNotify->line_notify_token) {
            $token = $userLineNotify->line_notify_token;
            $message = $request->input('message');

            // เรียกใช้ Line Notify API
            $response = $this->notify($token, $message);

            return response()->json([
                'success' => $response,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User does not have a Line Notify token.',
        ]);
    }
     

    private function notify($token, $message)
    {
        $lineApiUrl = "https://notify-api.line.me/api/notify";
    
        $data = [
            'message' => $message,
        ];
    
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
        curl_close($ch);
    
        if ($httpCode != 200) {
            throw new \Exception('Error sending notification. HTTP Code: ' . $httpCode);
        }
    
        return $result;
    }
}

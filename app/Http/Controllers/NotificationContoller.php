<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationContoller extends Controller
{
    public function send_notification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'messageData' => 'required',
                'firebaseIds' => 'required'
            ]);
    
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            // Send Notification to app
            $url = 'https://fcm.googleapis.com/fcm/send';

            $admin_header = array(
                "key: AIzaSyBwTH4gMhdWKZd5dlxYbvY3SIYMREOzGZY",
                "Authorization: key=" . "AAAAgvxGJqg:APA91bHQCC7Av_6k-DhytBf0-lhgbO_omK2nfbThcwz4C49VF1EK500EnrK1HmxGTRpixPBVIxojkRmoys2U1FV4KfmIhTn-hFURrYSS9BIRS_-Op6E3Y4k7IQ-qirLKqyS8iw7qyv6v",
                "Content-Type: application/json"
            );

            $student_header = array(
                "key: AIzaSyDovLKo3djdRbs963vqKdbj-geRWyzMTrg",
                "Authorization: key=" . "AAAAOjqNmFY:APA91bFaHsWDfwZqlt2uYKo7Lufj_4ZfP9tNK57HSZHIOD8kW-Rca-GlDbTyDBAAG3LacvqxUmgPK3zIzxoL6r6wwKWx_I7WEsqvYpjvhiZaCoK8CZtgDdmi8Gwp-xXtSruDgt_qKpWI",
                "Content-Type: application/json"
            );

            $fields = array(
                'registration_ids' => $request->firebaseIds,
                'notification' => array(
                    "title" => 'New message on Precisely',
                    "image" => 'https://lithics.in/apis/ic_notification.png'
                ),
                'data' => $request->messageData,
                'android' => array("priority" => "high"),
                "webpush" => array(
                    "headers" => array(
                        "Urgency" => "high"
                    )
                )
            );

            $fields = json_encode($fields, JSON_UNESCAPED_SLASHES);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $student_header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            curl_exec($ch);

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}

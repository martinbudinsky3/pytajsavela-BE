<?php

namespace App\Traits;

use App\Models\User;

trait SendsNotifications
{
    private function sendNotificationToUser(User $user, $data=null)
    {
        $url = env('FCM_URL');
        $fcmTokens = $user->fcmTokens()->pluck('fcm_token');
        $fcmServerKey = env('FCM_KEY');

        $notificationData = [
            "registration_ids" => $fcmTokens,
            "data" => $data
        ];
        $encodedData = json_encode($notificationData);

        $headers = [
            'Authorization:key=' . $fcmServerKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
    }
}

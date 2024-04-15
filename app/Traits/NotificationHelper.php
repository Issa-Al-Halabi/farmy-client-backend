<?php

namespace App\Traits;

use App\Enums\NotificationsTypes;

class NotificationHelper
{
    public static function sendPushNotification(array $fcmTokens, array $data, $type): string
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $apiKey = 'AAAA70MPwxg:APA91bEDUrpXu_tLoLEfHtHZMPzf2D7ii4KwXs7QdFN-54pE8Hrm26bNeYL08H9015QFyo_m4mMvFW6Gfh9kcbG5qMwOFjBpaFc_5NszVdcYr3wyeDvHaxD5HfvJjP8M1TWxgX_F_N8w';
        $header = [
            "authorization: key=" . $apiKey,
            "content-type: application/json",
        ];
        $value = NotificationsTypes::PushNotifications;
        $type = NotificationsTypes::getName($value);

        $payloads = [];
        foreach ($fcmTokens as $token) {
            if (isset($token)) {
                switch ($type) {
                    case 'Orders':
                    case 'Offers':
                        $payloads[] = NotificationHelper::buildPayload($token, $data);
                        break;
                    case 'PushNotifications':
                        $payloads[] = NotificationHelper::buildPayload($token, $data);
                        break;
                    default:
                        break;
                }
            }
        }


        $responses = [];

        foreach ($payloads as $payload) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 120,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => $header,
            ]);

            $response = curl_exec($ch);
            $responses[] = $response;
            curl_close($ch);
        }

        return json_encode($responses);
    }

    public static function sendPushNotificationToTopic(string $topic, array $data, $type): string
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $apiKey = 'AAAA-FF9gFg:APA91bHzhsnzPFmUDOKVoV70tJcn6Wsb4mInxzbrcRKmm2vvTgZkuPfx6f3MPpyzjSmPR0p0ly7x16UxOd4LdPeOm-LJLhy6JmAMBtUfPu6aidQMyyOm6aWOgXrccMZQyf6BfPCLBMPX';
        $header = [
            "authorization: key=" . $apiKey,
            "content-type: application/json",
        ];
        $payload = NotificationHelper::buildPayload($topic, $data);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private static function buildPayload(string $fcmToken, array $data): string
    {
        $payload = [
            "to" => $fcmToken,
            "data" => [
                "title" => $data['title'],
                "body" => $data['body'],
            ],
            "notification" => [
                "title" => $data['title'],
                "body" => $data['body'],
            ],
        ];
        return json_encode($payload);
    }
    public static function getTranslatedData(int $points , string $reason): array
    {
        if(app()->getLocale() == "ar"){
            return [
                "title" => "تم اضافة " .  $points . " نقطة لحسابك",
                "body" => "تم اضافة " .  $points . " نقطة لحسابك ".__($reason),
            ];
        }
        return [
            "title" => $points ." points have been added to your account",
            "body" => __("reward_notifications".$reason),
        ];
    }
}

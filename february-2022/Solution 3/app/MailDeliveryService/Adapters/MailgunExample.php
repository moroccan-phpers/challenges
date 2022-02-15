<?php

namespace App\MailDeliveryService\Adapters;

use App\MailDeliveryService\MailDeliveryServiceContract;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

class MailgunExample implements MailDeliveryServiceContract
{

    public function submitEmailForDelivery(string $request_id, string $sender, string $recipient, string $message): JsonResponse
    {
        // Normally we need to add the mailgun library
        // and follow the instruction to submit an email throw API
        if ($sender === "error@gmail.com") {
            return new JsonResponse(['status' => 'ERROR'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse(['status' => 'ACCEPTED'], JsonResponse::HTTP_OK);
    }

    public function isTrustedWebhook(Request $request): bool
    {
        return true;
       /* if (! $request->has('signature'))
            return false;

        $signature = $request->get('signature');
        if (!isset($signature['token'], $signature['timestamp'], $signature['signature']))
            return false;

        return hash_hmac(
            "SHA256",
            $signature['timestamp'] . $signature['token'],
            Config::get('services.mailgun.secret')
        ) === $signature['signature'];*/
    }
}

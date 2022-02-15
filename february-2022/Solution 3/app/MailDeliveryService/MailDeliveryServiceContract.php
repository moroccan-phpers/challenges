<?php

namespace App\MailDeliveryService;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface MailDeliveryServiceContract
{
    /**
     * Submit email for delivery
     *
     * @param string $request_id
     * @param string $sender
     * @param string $recipient
     * @param string $message
     * @return JsonResponse
     */
    public function submitEmailForDelivery(string $request_id, string $sender, string $recipient, string $message): JsonResponse;

    /**
     * Check if the incoming webhook is trusted
     *
     * @param Request $request
     * @return bool
     */
    public function isTrustedWebhook(Request  $request): bool;
}

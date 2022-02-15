<?php

namespace App\MailDeliveryService\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MailDeliveryServiceException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report(): ?bool
    {
        Log::error("Mail delivery service exception", [$this]);
        return true;
    }

    public function render(): JsonResponse
    {
        return new JsonResponse(
            ['message' => 'An error has occurred, please try again.'],
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}

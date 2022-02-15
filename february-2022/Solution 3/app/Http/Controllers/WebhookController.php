<?php

namespace App\Http\Controllers;

use App\MailDeliveryService\Exceptions\MailDeliveryServiceException;
use App\MailDeliveryService\Adapters\MailgunExample;
use App\MailDeliveryService\MailDeliveryServiceContract;
use App\MailDeliveryService\MailDeliveryServiceFactory;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class WebhookController extends Controller
{
    private WebhookService $webhookService;
    private MailDeliveryServiceContract $mailDeliveryService;

    /**
     * @throws MailDeliveryServiceException
     */
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
        $this->mailDeliveryService = MailDeliveryServiceFactory::getInstance(MailgunExample::class);
    }

    public function webhook(Request $request): JsonResponse
    {
        if (!$this->mailDeliveryService->isTrustedWebhook($request)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => Lang::get('Could not verify the signature.'),
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $requestId = $request->get('ID');
        $status = $request->get('STATU');
        if (!$requestId || !$status) {
            return new JsonResponse([
                'status' => 'error',
                'message' => Lang::get('Did not receive all the information.'),
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $this->webhookService->setStatusById($requestId, $status);

        return new JsonResponse(['status' => 'received'], JsonResponse::HTTP_OK);
    }
}

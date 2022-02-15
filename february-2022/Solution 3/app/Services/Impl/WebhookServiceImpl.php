<?php

namespace App\Services\Impl;

use App\Models\EmailRequest;
use App\Repositories\EmailRequestRepository;
use App\Services\WebhookService;

class WebhookServiceImpl implements WebhookService
{
    private EmailRequestRepository $emailRequestRepository;

    public function __construct(EmailRequestRepository $emailRequestRepository)
    {
        $this->emailRequestRepository = $emailRequestRepository;
    }

    public function setStatusById(string $requestId, string $status): void
    {
        $status = ($status === "DELIVERED") ? EmailRequest::SENT : EmailRequest::FAILED;

        $emailRequest = $this->emailRequestRepository->findById($requestId);

        $this->emailRequestRepository->update($emailRequest, ['status' =>$status]);
    }
}

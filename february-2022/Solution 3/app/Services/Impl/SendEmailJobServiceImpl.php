<?php

namespace App\Services\Impl;

use App\Models\EmailRequest;
use App\Repositories\EmailRequestRepository;
use App\Services\SendEmailJobService;

class SendEmailJobServiceImpl implements SendEmailJobService
{
    private EmailRequestRepository $emailRequestRepository;

    public function __construct(EmailRequestRepository $emailRequestRepository)
    {
        $this->emailRequestRepository = $emailRequestRepository;
    }

    public function updateEmailRequestToFailedStatus(EmailRequest $emailRequest): void
    {
        $this->emailRequestRepository->update($emailRequest, ['status' => EmailRequest::FAILED]);
    }
}

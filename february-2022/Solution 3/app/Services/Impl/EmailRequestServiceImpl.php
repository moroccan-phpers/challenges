<?php

namespace App\Services\Impl;

use App\Jobs\SendEmail;
use App\Models\EmailRequest;
use App\Repositories\EmailRequestRepository;
use App\Services\EmailRequestService;

class EmailRequestServiceImpl implements EmailRequestService
{
    private EmailRequestRepository $emailRequestRepository;

    public function __construct(EmailRequestRepository $emailRequestRepository)
    {
        $this->emailRequestRepository = $emailRequestRepository;
    }

    public function sendEmail(array $data): EmailRequest
    {
        $data['status'] = EmailRequest::PROCESSING;
        $emailRequest = $this->emailRequestRepository->create($data);

        SendEmail::dispatch($emailRequest)->afterResponse();
        return $emailRequest;
    }

    public function getEmailRequestById(string $id): EmailRequest
    {
        return $this->emailRequestRepository->findById($id);
    }
}

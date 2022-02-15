<?php

namespace App\Services;

use App\Models\EmailRequest;

interface SendEmailJobService
{
    public function updateEmailRequestToFailedStatus(EmailRequest $emailRequest): void;
}

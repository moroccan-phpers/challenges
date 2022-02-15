<?php

namespace App\Services;

use App\Models\EmailRequest;

interface EmailRequestService
{
    public function sendEmail(array $data): EmailRequest;

    public function getEmailRequestById(string $id): EmailRequest;
}

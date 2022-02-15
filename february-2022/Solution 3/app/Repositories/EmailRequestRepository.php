<?php

namespace App\Repositories;

use App\Models\EmailRequest;

interface EmailRequestRepository
{
    public function create(array $data): EmailRequest;

    public function findById(string $id): EmailRequest;

    public function update(EmailRequest $emailRequest, array $data): EmailRequest;
}

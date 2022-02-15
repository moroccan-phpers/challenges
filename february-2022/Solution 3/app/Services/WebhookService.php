<?php

namespace App\Services;

interface WebhookService
{
    public function setStatusById(string $requestId, string $status): void;
}

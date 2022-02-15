<?php

namespace App\Repositories\Eloquent;

use App\Models\EmailRequest;
use App\Repositories\EmailRequestRepository;
use Illuminate\Support\Arr;

class EmailRequestRepositoryEloquent implements EmailRequestRepository
{

    public function create(array $data): EmailRequest
    {
        return EmailRequest::create(Arr::only($data, [
            'sender',
            'recipient',
            'message',
            'status',
        ]));
    }

    public function findById(string $id): EmailRequest
    {
        return EmailRequest::findOrFail($id);
    }

    public function update(EmailRequest $emailRequest, array $data): EmailRequest
    {
        $emailRequest->update($data);

        return $emailRequest;
    }

    public function updateByMessageId(string $messageId, array $data): void
    {
        EmailRequest::where(['message_id' => $messageId])
            ->limit(1)
            ->update($data);
    }
}

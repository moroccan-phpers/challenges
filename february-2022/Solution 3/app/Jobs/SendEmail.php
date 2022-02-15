<?php

namespace App\Jobs;

use App\MailDeliveryService\MailDeliveryServiceContract;
use App\MailDeliveryService\Exceptions\MailDeliveryServiceException;
use App\MailDeliveryService\MailDeliveryServiceFactory;
use App\MailDeliveryService\Adapters\MailgunExample;
use App\Models\EmailRequest;
use App\Services\SendEmailJobService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private EmailRequest $emailRequest;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 120;
    private MailDeliveryServiceContract $mailDeliveryService;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws MailDeliveryServiceException
     */
    public function __construct(EmailRequest $emailRequest)
    {
        $this->emailRequest = $emailRequest;
        $this->mailDeliveryService = MailDeliveryServiceFactory::getInstance(MailgunExample::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SendEmailJobService $sendEmailJobService)
    {
        try {
            $response = $this->mailDeliveryService->submitEmailForDelivery(
                $this->emailRequest->id,
                $this->emailRequest->sender,
                $this->emailRequest->recipient,
                $this->emailRequest->message
            );

            if ($response->status() === 500) {
                throw new Exception("The Mail Delivery Service failed.");
            }
        } catch (Exception $exception) {
            // Update the status to failed
            $sendEmailJobService->updateEmailRequestToFailedStatus($this->emailRequest);

            Log::error($exception->getMessage());
        }

    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     * Wait 60s for the first retry, 5min the second, 10min the third, ...
     *
     * @return array
     */
    public function backoff(): array
    {
        return [60, 5 * 60, 10 * 60,10 * 60,];
    }
}

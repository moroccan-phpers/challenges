<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmailRequestAccepetedResource;
use App\Http\Resources\EmailRequestResource;
use App\Services\EmailRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmailRequestController extends Controller
{
    private EmailRequestService $emailRequestService;

    public function __construct(EmailRequestService $mailService)
    {
        $this->emailRequestService = $mailService;
    }

    /**
     * @throws ValidationException
     */
    public function send(Request $request): JsonResponse
    {
        // Make a validation
        $validator = Validator::make($request->all(), [
            'sender' => ['required', 'string', 'email', 'max:200'],
            'recipient' => ['required', 'string', 'email', 'max:200'],
            'message' => ['required', 'string', 'max:60000'],
        ]);

        // Check the validation and return a response if it fails
        if ($validator->fails()) {
            return new JsonResponse([
                'status' => 'denied',
                'message' => Lang::get('missing recipients field')
            ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // send the email
        $emailRequest = $this->emailRequestService->sendEmail($validator->validated());

        return new JsonResponse(
            new EmailRequestAccepetedResource($emailRequest),
            JsonResponse::HTTP_ACCEPTED
        );
    }

    /**
     * @throws ValidationException
     */
    public function status(Request $request): JsonResponse
    {
        // Make a validation
        $validator = Validator::make($request->all(), [
            'request_id' => ['required', 'string', 'max:64'],
        ]);

        // Check the validation and return a response if it fails
        if ($validator->fails()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => Lang::get('no request id is provided')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $emailRequest = $this->emailRequestService->getEmailRequestById($validator->validated()['request_id']);

        return new JsonResponse(
            new EmailRequestResource($emailRequest),
            JsonResponse::HTTP_OK
        );
    }
}

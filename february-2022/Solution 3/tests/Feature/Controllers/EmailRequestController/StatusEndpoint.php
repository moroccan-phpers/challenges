<?php

namespace Controllers\EmailRequestController;

use App\Models\EmailRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusEndpoint extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_if_request_id_is_not_provided()
    {
        $response = $this->json('GET', '/status');
        $response->assertForbidden();
        $response->assertJsonFragment([
            'status' => 'error'
        ]);
    }

    public function test_it_fails_if_request_id_is_invalid()
    {
        $response = $this->json('GET', '/status', [
            'request_id' => 'test',
        ]);
        $response->assertNotFound();
    }

    public function test_it_returns_information()
    {
        $emailRequest = EmailRequest::factory()->create();

        $response = $this->json('GET', '/status', $data = [
            'request_id' => $emailRequest->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'request_id' => $emailRequest->id,
            'status' => $emailRequest->status,
        ]);
    }
}

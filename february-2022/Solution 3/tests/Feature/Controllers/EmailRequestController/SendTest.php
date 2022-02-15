<?php

namespace Tests\Feature\Controllers\EmailRequestController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_if_data_is_not_valid()
    {
        $response = $this->json('POST', '/send');
        $response->assertForbidden();
        $response->assertJsonFragment([
            'status' => 'denied'
        ]);
    }

    public function test_it_fails_if_sender_is_invalid()
    {
        $response = $this->json('POST', '/send', [
            'sender' => 'not_an_email',
            'recipient' => 'recip@gmail.com',
            'message' => 'Hello world!',
        ]);
        $response->assertForbidden();
        $response->assertJsonFragment([
            'status' => 'denied'
        ]);
    }

    public function test_it_fails_if_recipient_is_invalid()
    {
        $response = $this->json('POST', '/send', [
            'sender' => 'recip@gmail.com',
            'recipient' => 'not_an_email',
            'message' => 'Hello world!',
        ]);
        $response->assertForbidden();
        $response->assertJsonFragment([
            'status' => 'denied'
        ]);
    }

    public function test_it_saves_the_request()
    {
        $response = $this->json('POST', '/send', $data = [
            'sender' => 'sender@gmail.com',
            'recipient' => 'recipient@gmail.com',
            'message' => 'Hello world!',
        ]);
        $response->assertStatus(202);
        $response->assertJsonFragment([
            'status' => 'accepted'
        ]);

        $this->assertDatabaseHas('email_requests', $data);
    }
}

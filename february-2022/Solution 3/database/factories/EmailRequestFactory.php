<?php

namespace Database\Factories;

use App\Models\EmailRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'sender' => $this->faker->email,
            'recipient' => $this->faker->email,
            'message' => $this->faker->paragraph(rand(1, 5)),
            'status' => [EmailRequest::ACCEPTED, EmailRequest::DENIED][rand(0, 1)],
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'sender_user_id' => User::factory(),
            'body' => fake('id_ID')->sentence(12),
        ];
    }
}

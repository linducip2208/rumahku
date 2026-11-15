<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyOrder>
 */
class PropertyOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'package_slug' => 'basic',
            'amount' => 99000,
            'status' => 'pending',
            'paid_at' => null,
            'expires_at' => null,
        ];
    }
}

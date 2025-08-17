<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<UserProfile> */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_type' => $this->faker->randomElement(['pribadi', 'bisnis']),
            'activation_date' => Carbon::now()->subMonth(),
            'ppoe_name' => $this->faker->safeEmail(),
            'whatsapp_number' => $this->faker->numerify('08##########'),
            'place_name' => $this->faker->company(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'lat_long' => [
                'lat' => $this->faker->latitude(),
                'lng' => $this->faker->longitude()
            ]
        ];
    }
}

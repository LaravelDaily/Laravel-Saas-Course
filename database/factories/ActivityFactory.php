<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Activitylog\Models\Activity;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'log_name' => fake()->randomElement(['default', 'custom', 'system']),
            'description' => fake()->randomElement(['created', 'updated', 'deleted']),
            'subject_type' => User::class,
            'subject_id' => User::factory(),
            'causer_type' => User::class,
            'causer_id' => User::factory(),
            'properties' => [],
            'event' => fake()->randomElement(['created', 'updated', 'deleted']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

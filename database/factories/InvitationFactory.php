<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => RoleEnum::Admin->value,
            'token' => Str::uuid()->toString(),
            'accepted_at' => null,
        ];
    }

    public function asUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleEnum::User->value,
        ]);
    }

    public function asViewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleEnum::Viewer->value,
        ]);
    }
}

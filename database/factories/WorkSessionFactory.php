<?php

namespace Database\Factories;

use App\Domain\Entities\User;
use App\Models\Domain\Entities\WorkSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Domain\Entities\WorkSession>
 */
class WorkSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkSession::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loginAt = $this->faker->dateTimeBetween('-1 week', 'now');
        $logoutAt = clone $loginAt;
        $logoutAt->modify('+' . $this->faker->numberBetween(10, 480) . ' minutes');

        return [
            'user_id' => User::factory(),
            'login_at' => $loginAt,
            'logout_at' => $logoutAt,
            'duration_minutes' => $logoutAt->diff($loginAt)->i + ($logoutAt->diff($loginAt)->h * 60),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
        ];
    }

    /**
     * Indicate that the session is still active (no logout).
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'logout_at' => null,
            'duration_minutes' => null,
        ]);
    }
}

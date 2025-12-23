<?php

namespace Database\Factories;

use App\Enums\TodoPriority;
use App\Enums\TodoStatus;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'assignee' => fake()->optional()->name(),
            'time_tracked' => fake()->numberBetween(0, 480),
            'status' => fake()->randomElement(TodoStatus::cases()),
            'priority' => fake()->randomElement(TodoPriority::cases()),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Todo $todo) {
            $todo->due_date = $todo->created_at
                ->copy()
                ->addDays(rand(1, 15))
                ->addHours(rand(0, 23));
        });
    }
}

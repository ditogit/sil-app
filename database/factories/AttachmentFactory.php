<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = fake()->word().'.pdf';

        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'file_name' => $fileName,
            'file_path' => 'attachments/'.$fileName,
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1024, 1048576),
        ];
    }
}

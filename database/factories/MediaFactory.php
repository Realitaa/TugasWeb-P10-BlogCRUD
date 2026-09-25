<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'file_path' => 'media/'.fake()->uuid().'.jpg',
            'file_name' => fake()->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10000, 500000),
        ];
    }
}

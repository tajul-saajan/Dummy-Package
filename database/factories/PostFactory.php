<?php

namespace Tajul\Saajan\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tajul\Saajan\Models\Post;
use Tajul\Saajan\Tests\User;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $author = User::factory()->create();

        return [
            'title'     => $this->faker->words(3, true),
            'body'      => $this->faker->paragraph,
            'author_id' => 999,
            'author_type' => get_class($author),
        ];
    }
}

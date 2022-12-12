<?php

namespace Database\Factories;

use App\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\News>
 */
class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'title' => $this->faker->text,
          'content' => $this->faker->paragraph($nbSentences = 5, $variableNbSentences = true),
          'user_id' => 1,
          'client_id' => 1
        ];
    }
}

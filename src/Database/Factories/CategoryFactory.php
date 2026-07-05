<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Database\Factories;

use CmsOrbit\Blog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('##'),
            'description' => fake()->optional()->sentence(),
            'sort' => fake()->numberBetween(0, 100),
        ];
    }
}

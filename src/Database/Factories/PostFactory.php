<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Database\Factories;

use CmsOrbit\Blog\Enums\PostStatus;
use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Blog\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'body' => fake()->paragraphs(3, true),
            'excerpt' => fake()->paragraph(),
            'status' => fake()->randomElement(PostStatus::cases()),
            'published_at' => fake()->optional()->dateTimeBetween('-1 year'),
            'featured_image' => fake()->optional()->imageUrl(800, 450),
            'meta_title' => fake()->optional()->sentence(6),
            'meta_description' => fake()->optional()->sentence(12),
            'category_id' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(fn (): array => [
            'category_id' => $category->getKey(),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');
        $city = $faker->optional()->randomElement(['Jakarta', 'Depok', 'Bekasi', 'Surabaya', 'Bandung']);
        $type = $faker->optional()->randomElement(['rumah', 'apartemen', 'tanah', 'ruko', 'villa']);

        $title = $city && $type
            ? sprintf(
                '%s %s di %s: Panduan Harga & Tips Membeli',
                ucfirst($type),
                $faker->randomElement(['Dijual', 'Murah', 'Terbaru']),
                $city
            )
            : $faker->sentence(7);

        return [
            'title' => $title,
            'slug' => Str::slug(Str::limit($title, 60)).'-'.$faker->unique()->numerify('####'),
            'body' => implode("\n\n", $faker->paragraphs(6)),
            'city' => $city,
            'type' => $type,
        ];
    }
}

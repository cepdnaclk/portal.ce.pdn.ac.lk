<?php

namespace Database\Factories;

use App\Domains\Gallery\Models\GalleryImage;
use App\Domains\News\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GalleryImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GalleryImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $filename = Str::random(40) . '.jpg';
        $path = 'gallery/' . $filename;

        return [
            'imageable_type' => News::class,
            'imageable_id' => News::factory(),
            'filename' => $filename,
            'original_filename' => 'test-image.jpg',
            'disk' => 'public',
            'path' => $path,
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(100000, 5000000),
            'width' => 800,
            'height' => 600,
            'alt_text' => $this->faker->sentence(3),
            'caption' => $this->faker->sentence(10),
            'credit' => $this->faker->name,
            'order' => 0,
            'is_cover' => false,
        ];
    }

    /**
     * Indicate that the image is a cover image.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cover()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_cover' => true,
            ];
        });
    }
}

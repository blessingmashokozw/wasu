<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ListingImage>
 */
class ListingImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listing = Listing::find($this->faker->randomElement(Listing::all()->pluck('listing_id')->toArray()));

        $imageUrl = $this->getProductImageUrl($listing);

        return [
            'listing_id' => $listing->listing_id,
            'image_path' => $imageUrl,
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    private function getProductImageUrl(Listing $listing): string
    {
        $title = strtolower($listing->title);

        // Product images from Unsplash (real product photography)
        $productImages = [
            'samsung' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&q=80',
            'galaxy' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&q=80',
            'phone' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&q=80',
            'smartphone' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&q=80',
            'nike' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
            'shoes' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
            'sneakers' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
            'running' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
            'headphones' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80',
            'sony' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80',
            'wireless' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80',
            'laptop' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80',
            'dell' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80',
            'computer' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80',
            'camera' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
            'canon' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
            'dslr' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
            'jeans' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&q=80',
            'levis' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&q=80',
            'pants' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&q=80',
            'watch' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&q=80',
            'apple' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&q=80',
            'bookshelf' => 'https://images.unsplash.com/photo-1594620302200-9a762244a156?w=800&q=80',
            'ikea' => 'https://images.unsplash.com/photo-1594620302200-9a762244a156?w=800&q=80',
            'furniture' => 'https://images.unsplash.com/photo-1594620302200-9a762244a156?w=800&q=80',
            'mixer' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
            'kitchenaid' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
            'playstation' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=800&q=80',
            'ps5' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=800&q=80',
            'gaming' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=800&q=80',
            'drill' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=800&q=80',
            'bosch' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=800&q=80',
            'tools' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=800&q=80',
            'chair' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=800&q=80',
            'herman' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=800&q=80',
            'office' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=800&q=80',
        ];

        // Service images
        $serviceImages = [
            'cleaning' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80',
            'house' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80',
            'repair' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&q=80',
            'phone' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&q=80',
            'training' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80',
            'fitness' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80',
            'gym' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80',
            'photography' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800&q=80',
            'wedding' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&q=80',
            'web' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80',
            'development' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80',
            'plumbing' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?w=800&q=80',
            'car' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&q=80',
            'detailing' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&q=80',
            'tutoring' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&q=80',
            'education' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&q=80',
            'landscaping' => 'https://images.unsplash.com/photo-1558904541-efa843a96f01?w=800&q=80',
            'garden' => 'https://images.unsplash.com/photo-1558904541-efa843a96f01?w=800&q=80',
            'graphic' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=800&q=80',
            'design' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=800&q=80',
        ];

        // Check for product keywords
        foreach ($productImages as $keyword => $url) {
            if (str_contains($title, $keyword)) {
                return $url;
            }
        }

        // Check for service keywords
        foreach ($serviceImages as $keyword => $url) {
            if (str_contains($title, $keyword)) {
                return $url;
            }
        }

        // Default images based on listing type
        if ($listing->listing_type === 'Service') {
            return 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&q=80'; // Professional service default
        }

        // Default product image
        return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&q=80'; // Generic product
    }
}

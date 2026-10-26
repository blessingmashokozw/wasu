<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listingType = fake()->randomElement(['Product', 'Service']);

        if ($listingType === 'Product') {
            $productData = $this->generateProductData();
            return [
                'seller_id' => \App\Models\User::factory()->verifiedSeller(),
                'category_id' => \App\Models\Category::factory(),
                'listing_type' => 'Product',
                'title' => $productData['title'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'quantity_available' => fake()->numberBetween(1, 50),
                'listing_status' => 'Active',
            ];
        } else {
            $serviceData = $this->generateServiceData();
            return [
                'seller_id' => \App\Models\User::factory()->verifiedSeller(),
                'category_id' => \App\Models\Category::factory(),
                'listing_type' => 'Service',
                'title' => $serviceData['title'],
                'description' => $serviceData['description'],
                'price' => $serviceData['price'],
                'quantity_available' => 1,
                'listing_status' => 'Active',
            ];
        }
    }

    private function generateProductData(): array
    {
        $products = [
            [
                'title' => 'Samsung Galaxy A54 5G - 128GB Storage',
                'description' => 'Brand new Samsung Galaxy A54 5G smartphone with 128GB internal storage, 6GB RAM, 50MP main camera, and 5000mAh battery. Includes original charger, USB cable, and 1-year warranty. Perfect for everyday use with excellent battery life and smooth performance.',
                'price' => fake()->randomFloat(2, 450, 550),
            ],
            [
                'title' => 'Nike Air Max 270 Running Shoes - Size 42',
                'description' => 'Authentic Nike Air Max 270 running shoes in excellent condition. Size 42 (EU). Features breathable mesh upper, responsive Air Max cushioning, and durable rubber outsole. Worn only a few times, like new. Perfect for running or casual wear.',
                'price' => fake()->randomFloat(2, 120, 180),
            ],
            [
                'title' => 'Sony WH-1000XM4 Wireless Headphones',
                'description' => 'Premium Sony WH-1000XM4 noise-canceling wireless headphones in black. Industry-leading noise cancellation, 30-hour battery life, crystal clear hands-free calling, and comfortable over-ear design. Includes carrying case and USB-C charging cable.',
                'price' => fake()->randomFloat(2, 280, 350),
            ],
            [
                'title' => 'Dell XPS 13 Laptop - Intel i7, 16GB RAM, 512GB SSD',
                'description' => 'Refurbished Dell XPS 13 ultrabook in excellent condition. Intel Core i7 10th Gen, 16GB RAM, 512GB SSD, 13.4" InfinityEdge display. Perfect for work, study, or entertainment. Battery life up to 12 hours. Comes with original charger.',
                'price' => fake()->randomFloat(2, 800, 1200),
            ],
            [
                'title' => 'Canon EOS Rebel T7 DSLR Camera Kit',
                'description' => 'Canon EOS Rebel T7 DSLR camera with 18-55mm lens kit. 24.1MP sensor, built-in Wi-Fi and NFC, full HD video recording. Ideal for beginners and photography enthusiasts. Includes camera bag, memory card, and battery charger.',
                'price' => fake()->randomFloat(2, 450, 600),
            ],
            [
                'title' => 'Levi\'s 501 Original Fit Jeans - Size 32',
                'description' => 'Classic Levi\'s 501 Original Fit jeans in dark blue wash. Size 32 waist, 32 length. 100% cotton, button fly, straight leg. Authentic Levi\'s quality and style. Never worn, tags still attached. Perfect for casual wear.',
                'price' => fake()->randomFloat(2, 60, 90),
            ],
            [
                'title' => 'Apple Watch Series 8 - 45mm GPS',
                'description' => 'Apple Watch Series 8 in excellent condition, 45mm case size with GPS. Features heart rate monitoring, ECG, blood oxygen sensor, always-on retina display, and crash detection. Includes sport band and magnetic charging cable. Battery health 95%.',
                'price' => fake()->randomFloat(2, 350, 450),
            ],
            [
                'title' => 'IKEA Hemnes Bookshelf - White',
                'description' => 'IKEA Hemnes bookshelf in white, solid wood construction. 6 adjustable shelves, perfect for books, decor, or storage. Minimal assembly required. Excellent condition, no scratches or damage. Dimensions: 90x197x19 cm.',
                'price' => fake()->randomFloat(2, 80, 120),
            ],
            [
                'title' => 'KitchenAid Stand Mixer - 5-Quart Artisan',
                'description' => 'KitchenAid 5-Quart Artisan Stand Mixer in Empire Red. 325-watt motor, 10-speed settings, includes flat beater, dough hook, and wire whip. Perfect for baking, mixing, and food preparation. Used only a few times, like new with original box.',
                'price' => fake()->randomFloat(2, 350, 500),
            ],
            [
                'title' => 'PlayStation 5 Console with DualSense Controller',
                'description' => 'Sony PlayStation 5 console with one DualSense wireless controller. Ultra-high speed SSD, 4K gaming, ray tracing, 3D audio. Includes HDMI cable, power cord, and USB charging cable. Excellent condition, works perfectly. No games included.',
                'price' => fake()->randomFloat(2, 450, 550),
            ],
            [
                'title' => 'Bosch Cordless Drill 18V - Professional Grade',
                'description' => 'Bosch 18V cordless drill/driver, professional grade. 2-speed gearbox, LED work light, 20+1 clutch settings. Includes 2 batteries, charger, and carrying case. Perfect for DIY projects and home repairs. Used lightly, excellent condition.',
                'price' => fake()->randomFloat(2, 150, 220),
            ],
            [
                'title' => 'Herman Miller Aeron Chair - Size B',
                'description' => 'Authentic Herman Miller Aeron ergonomic office chair, size B. Fully adjustable with lumbar support, tilt mechanism, and armrests. Classic black color. Excellent condition, minimal wear. Perfect for home office or professional use.',
                'price' => fake()->randomFloat(2, 600, 900),
            ],
        ];

        return fake()->randomElement($products);
    }

    private function generateServiceData(): array
    {
        $services = [
            [
                'title' => 'Professional House Cleaning Service',
                'description' => 'Complete house cleaning service by experienced professionals. Includes dusting, vacuuming, mopping, kitchen cleaning, bathroom sanitization, and window cleaning. Eco-friendly products used. Available weekly, bi-weekly, or monthly. Satisfaction guaranteed.',
                'price' => fake()->randomFloat(2, 80, 150),
            ],
            [
                'title' => 'Mobile Phone Repair - Screen Replacement',
                'description' => 'Professional mobile phone repair service specializing in screen replacements for iPhone, Samsung, and other major brands. High-quality OEM or premium aftermarket screens. Same-day service available. 90-day warranty on all repairs. Free diagnosis.',
                'price' => fake()->randomFloat(2, 50, 120),
            ],
            [
                'title' => 'Personal Training Sessions - Fitness Coach',
                'description' => 'Certified personal trainer offering one-on-one fitness coaching. Customized workout plans, nutrition guidance, and progress tracking. Sessions available at gym or your home. Specializes in weight loss, muscle building, and overall fitness. Flexible scheduling.',
                'price' => fake()->randomFloat(2, 30, 60),
            ],
            [
                'title' => 'Professional Photography - Events & Portraits',
                'description' => 'Professional photography services for weddings, corporate events, birthdays, and portrait sessions. High-quality equipment, creative lighting, and expert editing. Packages include digital files and prints. Portfolio available upon request. Competitive rates.',
                'price' => fake()->randomFloat(2, 200, 500),
            ],
            [
                'title' => 'Web Development - Custom Websites',
                'description' => 'Custom web development services for businesses and individuals. Responsive design, SEO-friendly, fast loading, and secure. Includes domain registration, hosting setup, and basic maintenance. E-commerce solutions available. Free consultation.',
                'price' => fake()->randomFloat(2, 300, 800),
            ],
            [
                'title' => 'Plumbing Services - Repairs & Installations',
                'description' => 'Licensed plumber providing residential and commercial plumbing services. Leak detection, pipe repairs, fixture installation, water heater maintenance, and drain cleaning. Emergency services available. Fair pricing with no hidden fees. Fully insured.',
                'price' => fake()->randomFloat(2, 60, 150),
            ],
            [
                'title' => 'Car Detailing - Full Interior & Exterior',
                'description' => 'Complete car detailing service including hand wash, clay bar treatment, wax polish, interior vacuuming, leather conditioning, and window cleaning. Uses premium products. Protects your vehicle\'s finish and restores showroom shine. Mobile service available.',
                'price' => fake()->randomFloat(2, 100, 200),
            ],
            [
                'title' => 'Tutoring - Mathematics & Science Subjects',
                'description' => 'Experienced tutor offering private lessons in mathematics, physics, chemistry, and biology for primary, secondary, and college students. Personalized learning approach, exam preparation, and homework help. Online or in-person sessions available.',
                'price' => fake()->randomFloat(2, 25, 50),
            ],
            [
                'title' => 'Landscaping & Garden Maintenance',
                'description' => 'Professional landscaping and garden maintenance services. Lawn mowing, hedge trimming, tree pruning, flower bed maintenance, irrigation system installation, and seasonal cleanup. Transform your outdoor space into a beautiful garden. Weekly or monthly plans.',
                'price' => fake()->randomFloat(2, 50, 120),
            ],
            [
                'title' => 'Graphic Design - Logos & Branding',
                'description' => 'Creative graphic design services for logos, business cards, brochures, social media graphics, and complete branding packages. Modern, professional designs that make your business stand out. Multiple revisions included. Fast turnaround times.',
                'price' => fake()->randomFloat(2, 100, 300),
            ],
        ];

        return fake()->randomElement($services);
    }
}

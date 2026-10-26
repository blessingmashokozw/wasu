<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    /**
     * Display a listing of the seller's listings.
     */
    public function index(): Response
    {
        $listings = Listing::with(['category', 'images'])
            ->where('seller_id', auth()->id())
            ->get();

        return Inertia::render('Seller/Listings/Index', [
            'listings' => $listings,
        ]);
    }

    /**
     * Show the form for creating a new listing.
     */
    public function create(): Response
    {
        $categories = \App\Models\Category::all();

        return Inertia::render('Seller/Listings/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created listing.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Listing::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'listing_type' => 'required|in:Product,Service',
            'title' => 'required|string|max:120',
            'description' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'quantity_available' => 'required|integer|gte:0',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|file|mimes:jpeg,png|max:2048',
        ]);

        $listing = \DB::transaction(function () use ($validated, $request) {
            $listing = Listing::create([
                'seller_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'listing_type' => $validated['listing_type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'quantity_available' => $validated['quantity_available'],
                'listing_status' => 'Active',
            ]);

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('listings', 'public');
                ListingImage::create([
                    'listing_id' => $listing->listing_id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                ]);
            }

            return $listing;
        });

        return redirect()->route('seller.listings.index')->with('success', 'Listing created successfully.');
    }

    /**
     * Show the form for editing the specified listing.
     */
    public function edit(Listing $listing): Response
    {
        Gate::authorize('update', $listing);

        $listing->load(['category', 'images']);
        $categories = \App\Models\Category::all();

        return Inertia::render('Seller/Listings/Edit', [
            'listing' => $listing,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified listing.
     */
    public function update(Request $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'listing_type' => 'required|in:Product,Service',
            'title' => 'required|string|max:120',
            'description' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'quantity_available' => 'required|integer|gte:0',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|file|mimes:jpeg,png|max:2048',
        ]);

        \DB::transaction(function () use ($validated, $request, $listing) {
            $listing->update([
                'category_id' => $validated['category_id'],
                'listing_type' => $validated['listing_type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'quantity_available' => $validated['quantity_available'],
            ]);

            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($listing->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }

                // Upload new images
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('listings', 'public');
                    ListingImage::create([
                        'listing_id' => $listing->listing_id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                    ]);
                }
            }
        });

        return redirect()->route('seller.listings.index')->with('success', 'Listing updated successfully.');
    }

    /**
     * Update listing status (hide, mark out of stock, remove).
     */
    public function updateStatus(Request $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

        $validated = $request->validate([
            'listing_status' => 'required|in:Active,Out of stock,Hidden,Removed',
        ]);

        $listing->update(['listing_status' => $validated['listing_status']]);

        return back()->with('success', 'Listing status updated successfully.');
    }

    /**
     * Remove the specified listing.
     */
    public function destroy(Listing $listing)
    {
        Gate::authorize('delete', $listing);

        \DB::transaction(function () use ($listing) {
            foreach ($listing->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $listing->delete();
        });

        return back()->with('success', 'Listing deleted successfully.');
    }
}

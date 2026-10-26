<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CatalogueController extends Controller
{
    /**
     * Display the catalogue with filters.
     */
    public function index(Request $request): Response
    {
        $query = Listing::with(['category', 'seller', 'images'])
            ->where('listing_status', 'Active')
            ->where('quantity_available', '>', 0);

        // Keyword search (title and description)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Seller filter
        $sellerReviews = null;
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);

            // Load seller reviews
            $sellerReviews = \App\Models\Review::with(['reviewer', 'order'])
                ->where('reviewee_id', $request->seller_id)
                ->where('review_type', 'seller')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Listing type filter
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // Pagination (12 per page)
        $listings = $query->paginate(12);

        $categories = Category::all();

        return Inertia::render('Catalogue/Index', [
            'listings' => $listings,
            'categories' => $categories,
            'filters' => $request->only(['keyword', 'category_id', 'min_price', 'max_price', 'seller_id', 'listing_type']),
            'seller_reviews' => $sellerReviews,
        ]);
    }

    /**
     * Display the specified listing.
     */
    public function show(Listing $listing): Response
    {
        Gate::authorize('view', $listing);

        $listing->load(['category', 'seller', 'images' => function ($query) {
            $query->orderBy('is_primary', 'desc');
        }]);

        // Load seller reviews
        $sellerReviews = \App\Models\Review::with(['reviewer', 'order'])
            ->where('reviewee_id', $listing->seller_id)
            ->where('review_type', 'seller')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Catalogue/Show', [
            'listing' => $listing,
            'seller_reviews' => $sellerReviews,
        ]);
    }
}

import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Search, Filter, X } from 'lucide-react';

export default function CatalogueIndex({ listings, categories, filters, seller_reviews }) {
    const { auth } = usePage().props;
    const isAuthenticated = !!auth?.user;
    const { data, setData, get } = useForm({
        keyword: filters.keyword || '',
        category_id: filters.category_id || '',
        min_price: filters.min_price || '',
        max_price: filters.max_price || '',
        seller_id: filters.seller_id || '',
        listing_type: filters.listing_type || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        get(route('catalogue.index'), { preserveState: true });
    };

    const clearFilters = () => {
        setData({
            keyword: '',
            category_id: '',
            min_price: '',
            max_price: '',
            seller_id: '',
            listing_type: '',
        });
        get(route('catalogue.index'), { preserveState: true });
    };

    const content = (
        <>
            <Head title="Catalogue" />
            
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    
                    <h1 className="text-4xl font-bold text-gray-900 mb-2">Catalogue</h1>
                    <p className="text-gray-600 mb-8">Discover amazing products and services from verified sellers</p>

                    {/* Filters */}
                    <Card className="mb-8 border-2 border-blue-100">
                        <CardContent className="pt-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex flex-col lg:flex-row gap-4">
                                    {/* Search Bar */}
                                    <div className="flex-1">
                                        <div className="relative">
                                            <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                            <input
                                                type="text"
                                                value={data.keyword}
                                                onChange={(e) => setData('keyword', e.target.value)}
                                                placeholder="Search products, services..."
                                                className="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Filter Dropdowns */}
                                    <div className="flex flex-wrap gap-3">
                                        <select
                                            value={data.category_id}
                                            onChange={(e) => setData('category_id', e.target.value)}
                                            className="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white min-w-[140px]"
                                        >
                                            <option value="">All Categories</option>
                                            {categories.map((category) => (
                                                <option key={category.category_id} value={category.category_id}>
                                                    {category.category_name}
                                                </option>
                                            ))}
                                        </select>

                                        <select
                                            value={data.listing_type}
                                            onChange={(e) => setData('listing_type', e.target.value)}
                                            className="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white min-w-[120px]"
                                        >
                                            <option value="">All Types</option>
                                            <option value="Product">Product</option>
                                            <option value="Service">Service</option>
                                        </select>

                                        <div className="flex items-center gap-2">
                                            <div className="relative">
                                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                                <input
                                                    type="number"
                                                    value={data.min_price}
                                                    onChange={(e) => setData('min_price', e.target.value)}
                                                    placeholder="Min"
                                                    step="0.01"
                                                    min="0"
                                                    className="w-24 pl-7 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                                />
                                            </div>
                                            <span className="text-gray-400">-</span>
                                            <div className="relative">
                                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                                <input
                                                    type="number"
                                                    value={data.max_price}
                                                    onChange={(e) => setData('max_price', e.target.value)}
                                                    placeholder="Max"
                                                    step="0.01"
                                                    min="0"
                                                    className="w-24 pl-7 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                                />
                                            </div>
                                        </div>

                                        <Button type="submit" className="px-6 py-3">
                                            <Search className="mr-2 h-4 w-4" />
                                            Search
                                        </Button>

                                        <Button type="button" variant="ghost" onClick={clearFilters} className="px-4 py-3">
                                            <X className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    {/* Listings Grid */}
                    {listings.data.length === 0 ? (
                        <Card className="p-12 text-center">
                            <p className="text-gray-500 text-lg">No listings found</p>
                        </Card>
                    ) : (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            {listings.data.map((listing) => (
                                <Link
                                    key={listing.listing_id}
                                    href={route('catalogue.show', listing.listing_id)}
                                >
                                    <Card className="overflow-hidden hover:shadow-xl transition-shadow duration-300 cursor-pointer h-full flex flex-col">
                                        <div className="relative aspect-square bg-gray-100">
                                            {listing.images && listing.images.length > 0 ? (
                                                <img
                                                    src={
                                                        listing.images.find(img => img.is_primary)?.image_path.startsWith('http')
                                                            ? listing.images.find(img => img.is_primary)?.image_path
                                                            : `/storage/${listing.images.find(img => img.is_primary)?.image_path || listing.images[0].image_path}`
                                                    }
                                                    alt={listing.title}
                                                    className="w-full h-full object-cover"
                                                />
                                            ) : (
                                                <div className="w-full h-full flex items-center justify-center text-gray-400">
                                                    No image
                                                </div>
                                            )}
                                            <Badge className="absolute top-2 right-2" variant={listing.listing_type === 'Product' ? 'default' : 'secondary'}>
                                                {listing.listing_type}
                                            </Badge>
                                        </div>
                                        <CardContent className="p-4 flex-1 flex flex-col">
                                            <h3 className="font-semibold text-gray-900 line-clamp-2 mb-2">{listing.title}</h3>
                                            <p className="text-sm text-gray-500 mb-3">{listing.category?.category_name}</p>
                                            <div className="mt-auto">
                                                <div className="flex items-center justify-between mb-2">
                                                    <span className="text-2xl font-bold text-blue-600">
                                                        ${parseFloat(listing.price).toFixed(2)}
                                                    </span>
                                                </div>
                                                <div className="flex items-center justify-between text-sm text-gray-500">
                                                    <span>{listing.quantity_available} available</span>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </Link>
                            ))}
                        </div>
                    )}

                    {/* Seller Reviews Section */}
                    {seller_reviews && seller_reviews.length > 0 && (
                        <Card className="mt-8">
                            <CardContent className="p-6">
                                <h3 className="text-xl font-bold text-gray-900 mb-4">Seller Reviews</h3>
                                <div className="space-y-4">
                                    {seller_reviews.map((review) => (
                                        <div key={review.review_id} className="border-b pb-4 last:border-0">
                                            <div className="flex items-center gap-2 mb-2">
                                                <div className="flex">
                                                    {[1, 2, 3, 4, 5].map((star) => (
                                                        <span
                                                            key={star}
                                                            className={`text-xl ${star <= review.rating ? 'text-yellow-400' : 'text-gray-300'}`}
                                                        >
                                                            ★
                                                        </span>
                                                    ))}
                                                </div>
                                                <span className="text-sm text-gray-500">
                                                    {new Date(review.created_at).toLocaleDateString()}
                                                </span>
                                            </div>
                                            {review.comment && (
                                                <p className="text-gray-700">{review.comment}</p>
                                            )}
                                            <p className="text-sm text-gray-500 mt-2">
                                                By: {review.reviewer?.full_name || 'Anonymous'}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Pagination */}
                    {listings.links && listings.links.length > 3 && (
                        <div className="mt-8 flex justify-center">
                            <div className="flex gap-2">
                                {listings.links.map((link, index) => (
                                    link.url ? (
                                        <Link key={index} href={link.url}>
                                            <Button
                                                variant={link.active ? 'default' : 'outline'}
                                                className={link.active ? 'bg-blue-600 hover:bg-blue-700' : ''}
                                            >
                                                {link.label.replace(/&laquo;|&raquo;/g, '').replace(/&nbsp;/g, '')}
                                            </Button>
                                        </Link>
                                    ) : (
                                        <Button key={index} variant="outline" disabled>
                                            {link.label.replace(/&laquo;|&raquo;/g, '').replace(/&nbsp;/g, '')}
                                        </Button>
                                    )
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );

    // if (isAuthenticated) {
        return <AuthenticatedLayout>{content}</AuthenticatedLayout>;
    // }

    return content;
}

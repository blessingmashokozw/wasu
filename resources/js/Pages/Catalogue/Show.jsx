import { Head, Link, useForm } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { ArrowLeftCircleIcon, ArrowRightCircleIcon, ShoppingCartPlusIcon, MessageCircleIcon } from 'lucide-react';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious, useCarousel } from '@/components/ui/carousel';
import { useEffect, useState } from 'react';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';


export default function CatalogueShow({ listing, seller_reviews }) {
    const { auth } = usePage().props;
    const isAuthenticated = !!auth?.user;
    const { post, processing } = useForm({
        items: [{ listing_id: listing.listing_id, quantity: 1 }],
    });
    const [currentSlide, setCurrentSlide] = useState(0);

    const addToCart = () => {
        post(route('buyer.orders.checkout'), {
            onError: (errors) => {
                console.error('Checkout error:', errors);
            },
        });
    };

    const content = (
        <>
            <Head title={listing.title} />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                  
                    <Link
                        href={route('catalogue.index')}
                        className="inline-flex items-center mb-6"
                    >
                        <Button variant='outline'><ArrowLeftCircleIcon className="mr-2" />  Back to Catalogue</Button>
                    </Link>

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                            {/* Images */}
                            <div className="relative">
                                {listing.images && listing.images.length > 0 ? (
                                    <Carousel className="w-full" setApi={(api) => {
                                        api?.on('select', () => {
                                            setCurrentSlide(api.selectedScrollSnap());
                                        });
                                    }}>
                                        <CarouselContent>
                                            {listing.images.map((image) => (
                                                <CarouselItem key={image.image_id}>
                                                    <img
                                                        src={
                                                            image.image_path.startsWith('http')
                                                                ? image.image_path
                                                                : `/storage/${image.image_path}`
                                                        }
                                                        alt={listing.title}
                                                        className="w-full rounded-lg aspect-video object-cover"
                                                    />
                                                </CarouselItem>
                                            ))}
                                        </CarouselContent>
                                        <CarouselPrevious className="left-2" />
                                        <CarouselNext className="right-2" />
                                        <div className="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                                            {currentSlide + 1} / {listing.images.length}
                                        </div>
                                    </Carousel>
                                ) : (
                                    <div className="aspect-video bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                        No image available
                                    </div>
                                )}
                            </div>

                            {/* Details */}
                            <div>
                                <div className="flex items-center gap-2 mb-2">
                                    <span className="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                        {listing.listing_type}
                                    </span>
                                    <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                        {listing.listing_status}
                                    </span>
                                </div>

                                <h1 className="text-3xl font-bold text-gray-900 mb-4">{listing.title}</h1>

                                <p className="text-4xl font-bold text-indigo-600 mb-6">
                                    ${parseFloat(listing.price).toFixed(2)}
                                </p>

                                <div className="mb-6">
                                    <h3 className="text-sm font-medium text-gray-500 mb-1">Category</h3>
                                    <p className="text-gray-900">{listing.category?.category_name}</p>
                                </div>

                                <div className="mb-6">
                                    <h3 className="text-sm font-medium text-gray-500 mb-1">Description</h3>
                                    <p className="text-gray-900 whitespace-pre-wrap">{listing.description}</p>
                                </div>

                                <div className="mb-6">
                                    <h3 className="text-sm font-medium text-gray-500 mb-1">Available Quantity</h3>
                                    <p className="text-gray-900">{listing.quantity_available}</p>
                                </div>

                                <div className="mb-6">
                                    <h3 className="text-sm font-medium text-gray-500 mb-1">Seller</h3>
                                    <Link
                                        href={route('catalogue.index', { seller_id: listing.seller?.seller_id })}
                                        className="flex items-center gap-3 text-indigo-600 hover:text-indigo-700 font-medium"
                                    >
                                        <Avatar>
                                            <AvatarFallback>
                                                {listing.seller?.business_name?.substring(0, 2).toUpperCase() ||
                                                    listing.seller?.user?.full_name?.split(' ').map(n => n[0]).join('').toUpperCase() ||
                                                    'SL'}
                                            </AvatarFallback>
                                        </Avatar>
                                        {listing.seller?.business_name || listing.seller?.user?.full_name}
                                    </Link>
                                </div>

                                <div className="border-t pt-6">
                                    <div className="flex gap-3">
                                        <Button
                                            variant={'default'}
                                            size={'lg'}
                                            onClick={addToCart}
                                            disabled={listing.quantity_available === 0 || processing}
                                            className="flex-1"
                                        >
                                            <ShoppingCartPlusIcon className="mr-2" />
                                            {processing ? 'Processing...' : (listing.quantity_available === 0 ? 'Out of Stock' : 'Add to Cart')}
                                        </Button>
                                        {isAuthenticated && (
                                            <Link
                                                href={route('messages.index', {
                                                    user_id: listing.seller?.user_id,
                                                    listing_id: listing.listing_id,
                                                })}
                                            >
                                                <Button
                                                    variant={'outline'}
                                                    size={'lg'}
                                                >
                                                    <MessageCircleIcon className="mr-2" />
                                                    Message Seller
                                                </Button>
                                            </Link>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Seller Reviews Section */}
                        {seller_reviews && seller_reviews.length > 0 && (
                            <div className="bg-white rounded-lg shadow p-6 mt-6">
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
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );


    return <AuthenticatedLayout>{content}</AuthenticatedLayout>;

}

import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { SaveIcon } from 'lucide-react';

export default function ReviewCreate({ order }) {
    const { data, setData, post, processing, errors } = useForm({
        review_type: 'seller',
        rating: 5,
        comment: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('buyer.reviews.store', order.order_id));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Write a Review" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('buyer.orders.show', order.order_id)}
                            className="inline-flex items-center text-indigo-600 hover:text-indigo-700"
                        >
                            ← Back to Order
                        </Link>
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900 mb-8">Write a Review</h1>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                        <p className="text-gray-900 font-medium">Order #{order.order_id}</p>
                        <p className="text-gray-600">
                            Seller: {order.seller?.seller.business_name || order.seller?.user?.full_name}
                        </p>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="mb-6">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Who are you reviewing?
                                </label>
                                <div className="flex gap-4">
                                    <label className="flex items-center">
                                        <input
                                            type="radio"
                                            value="seller"
                                            checked={data.review_type === 'seller'}
                                            onChange={(e) => setData('review_type', e.target.value)}
                                            className="mr-2"
                                        />
                                        <span>Seller ({order.seller?.seller?.business_name || order.seller?.user?.full_name})</span>
                                    </label>
                                    <label className="flex items-center">
                                        <input
                                            type="radio"
                                            value="buyer"
                                            checked={data.review_type === 'buyer'}
                                            onChange={(e) => setData('review_type', e.target.value)}
                                            className="mr-2"
                                        />
                                        <span>Buyer ({order.buyer?.full_name})</span>
                                    </label>
                                </div>
                                {errors.review_type && (
                                    <p className="mt-1 text-sm text-red-600">{errors.review_type}</p>
                                )}
                            </div>

                            <div className="mb-6">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Rating
                                </label>
                                <div className="flex gap-2">
                                    {[1, 2, 3, 4, 5].map((star) => (
                                        <button
                                            key={star}
                                            type="button"
                                            onClick={() => setData('rating', star)}
                                            className={`text-3xl ${star <= data.rating ? 'text-yellow-400' : 'text-gray-300'} hover:text-yellow-400 focus:outline-none`}
                                        >
                                            ★
                                        </button>
                                    ))}
                                </div>
                                {errors.rating && (
                                    <p className="mt-1 text-sm text-red-600">{errors.rating}</p>
                                )}
                            </div>

                            <div className="mb-6">
                                <label htmlFor="comment" className="block text-sm font-medium text-gray-700 mb-2">
                                    Comment (Optional)
                                </label>
                                <textarea
                                    id="comment"
                                    value={data.comment}
                                    onChange={(e) => setData('comment', e.target.value)}
                                    rows={5}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Share your experience with this seller..."
                                />
                                {errors.comment && (
                                    <p className="mt-1 text-sm text-red-600">{errors.comment}</p>
                                )}
                            </div>

                            <div className="flex justify-end gap-4">
                                <Link
                                    href={route('buyer.orders.show', order.order_id)}
                                >
                                    <Button variant='outline'>
                                        Cancel
                                    </Button>
                                </Link>
                                <Button variant={'default'}
                                    type="submit"
                                    disabled={processing}
                                >
                                    <SaveIcon className="mr-2" />
                                    {processing ? 'Submitting...' : 'Submit Review'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

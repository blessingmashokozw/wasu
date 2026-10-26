import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ReviewEdit({ review }) {
    const { data, setData, put, processing, errors } = useForm({
        rating: review.rating,
        comment: review.comment || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('reviews.update', review.review_id));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Edit Review" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('buyer.orders.show', review.order.order_id)}
                            className="inline-flex items-center text-indigo-600 hover:text-indigo-700"
                        >
                            ← Back to Order
                        </Link>
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900 mb-8">Edit Review</h1>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                        <p className="text-gray-900 font-medium">Order #{review.order.order_id}</p>
                        <p className="text-gray-600">
                            Seller: {review.reviewee?.full_name}
                        </p>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6">
                        <form onSubmit={handleSubmit}>
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
                                    href={route('buyer.orders.show', review.order.order_id)}
                                    className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Updating...' : 'Update Review'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

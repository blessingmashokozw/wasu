import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function BuyerOrdersShow({ order }) {
    const getStatusColor = (status) => {
        switch (status) {
            case 'Pending payment':
                return 'bg-yellow-100 text-yellow-800';
            case 'Paid':
                return 'bg-blue-100 text-blue-800';
            case 'Completed':
                return 'bg-green-100 text-green-800';
            case 'Cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getPaymentStatusColor = (status) => {
        switch (status) {
            case 'Successful':
                return 'bg-green-100 text-green-800';
            case 'Failed':
                return 'bg-red-100 text-red-800';
            case 'Initiated':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Order #${order.order_id}`} />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('buyer.orders.index')}
                            className="inline-flex items-center text-indigo-600 hover:text-indigo-700"
                        >
                            ← Back to My Orders
                        </Link>
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900 mb-8">Order #{order.order_id}</h1>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-1">Order Status</h3>
                            <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.order_status)}`}>
                                {order.order_status}
                            </span>
                        </div>

                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-1">Order Date</h3>
                            <p className="text-gray-900">{new Date(order.order_date).toLocaleString()}</p>
                        </div>

                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-1">Payment Status</h3>
                            {order.payments && order.payments.length > 0 ? (
                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${getPaymentStatusColor(order.payments[0].payment_status)}`}>
                                    {order.payments[0].payment_status}
                                </span>
                            ) : (
                                <span className="text-gray-500">No payment</span>
                            )}
                        </div>

                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-1">Total Amount</h3>
                            <p className="text-2xl font-bold text-gray-900">${parseFloat(order.total_amount).toFixed(2)}</p>
                        </div>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Seller Information</h3>
                        <p className="text-gray-900 font-medium">
                            {order.seller?.business_name || order.seller?.user?.full_name}
                        </p>
                        <p className="text-sm text-gray-500">{order.seller?.user?.email}</p>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h3>
                        <p className="text-gray-900">{order.delivery_address}</p>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                        <div className="space-y-4">
                            {order.order_items.map((item) => (
                                <div key={item.order_item_id} className="flex items-center gap-4 pb-4 border-b last:border-0">
                                    {item.listing?.images && item.listing.images.length > 0 && (
                                        <img
                                            src={`/storage/${item.listing.images.find(img => img.is_primary)?.image_path || item.listing.images[0].image_path}`}
                                            alt={item.listing?.title}
                                            className="w-16 h-16 rounded object-cover"
                                        />
                                    )}
                                    <div className="flex-1">
                                        <p className="font-medium text-gray-900">{item.listing?.title}</p>
                                        <p className="text-sm text-gray-500">Qty: {item.quantity} × ${parseFloat(item.unit_price).toFixed(2)}</p>
                                    </div>
                                    <p className="font-medium text-gray-900">${parseFloat(item.subtotal).toFixed(2)}</p>
                                </div>
                            ))}
                        </div>
                        <div className="flex justify-between font-bold mt-4 pt-4 border-t">
                            <span>Total</span>
                            <span>${parseFloat(order.total_amount).toFixed(2)}</span>
                        </div>
                    </div>

                    {order.delivery && (
                        <div className="bg-white rounded-lg shadow p-6">
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-semibold text-gray-900">Delivery Information</h3>
                                <Link
                                    href={route('buyer.deliveries.show', order.delivery.delivery_id)}
                                    className="text-indigo-600 hover:text-indigo-700 text-sm"
                                >
                                    View Delivery Details →
                                </Link>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-gray-500">Status</p>
                                    <p className="text-gray-900">{order.delivery.current_status}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500">Method</p>
                                    <p className="text-gray-900">{order.delivery.delivery_method}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500">Estimated Date</p>
                                    <p className="text-gray-900">
                                        {order.delivery.estimated_date ? new Date(order.delivery.estimated_date).toLocaleDateString() : '-'}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500">Received At</p>
                                    <p className="text-gray-900">
                                        {order.delivery.received_at ? new Date(order.delivery.received_at).toLocaleString() : '-'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Review Section */}
                    <div className="bg-white rounded-lg shadow p-6 mt-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Rate Seller</h3>
                        {order.reviews && order.reviews.length > 0 ? (
                            <div className="space-y-4">
                                {order.reviews.map((review) => (
                                    <div key={review.review_id} className="border rounded-lg p-4">
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
                                        <div className="mt-3 flex gap-2">
                                            <Link
                                                href={route('reviews.edit', review.review_id)}
                                                className="text-sm text-indigo-600 hover:text-indigo-700"
                                            >
                                                Edit Review
                                            </Link>
                                            <button
                                                onClick={() => {
                                                    if (confirm('Are you sure you want to delete this review?')) {
                                                        router.delete(route('reviews.destroy', review.review_id));
                                                    }
                                                }}
                                                className="text-sm text-red-600 hover:text-red-700"
                                            >
                                                Delete Review
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : order.order_status === 'Completed' ? (
                            <div>
                                <p className="text-gray-600 mb-4">Your order has been delivered. Would you like to rate the seller?</p>
                                <Link
                                    href={route('buyer.reviews.create', order.order_id)}
                                    className="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                                >
                                    Write a Review
                                </Link>
                            </div>
                        ) : (
                            <p className="text-gray-500">You can rate the seller after your order is delivered.</p>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

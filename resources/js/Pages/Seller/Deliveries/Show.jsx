import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function SellerDeliveriesShow({ delivery }) {
    const getStatusColor = (status) => {
        switch (status) {
            case 'Processed':
                return 'bg-blue-100 text-blue-800';
            case 'Dispatched':
                return 'bg-yellow-100 text-yellow-800';
            case 'In transit':
                return 'bg-purple-100 text-purple-800';
            case 'Delivered':
                return 'bg-green-100 text-green-800';
            case 'Received':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Delivery Details" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('seller.deliveries.index')}
                            className="inline-flex items-center text-indigo-600 hover:text-indigo-700"
                        >
                            ← Back to Deliveries
                        </Link>
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900 mb-8">Delivery Details</h1>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 className="text-sm font-medium text-gray-500 mb-1">Order ID</h3>
                                <p className="text-gray-900">#{delivery.order?.order_id}</p>
                            </div>
                            <div>
                                <h3 className="text-sm font-medium text-gray-500 mb-1">Current Status</h3>
                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(delivery.current_status)}`}>
                                    {delivery.current_status}
                                </span>
                            </div>
                            <div>
                                <h3 className="text-sm font-medium text-gray-500 mb-1">Delivery Method</h3>
                                <p className="text-gray-900">{delivery.delivery_method}</p>
                            </div>
                            <div>
                                <h3 className="text-sm font-medium text-gray-500 mb-1">Estimated Date</h3>
                                <p className="text-gray-900">
                                    {delivery.estimated_date ? new Date(delivery.estimated_date).toLocaleDateString() : '-'}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Delivery Timeline</h3>
                        {delivery.updates && delivery.updates.length > 0 ? (
                            <div className="space-y-4">
                                {delivery.updates.map((update) => (
                                    <div key={update.update_id} className="flex gap-4">
                                        <div className="flex flex-col items-center">
                                            <div className="w-3 h-3 bg-indigo-600 rounded-full"></div>
                                            {update.update_id !== delivery.updates[delivery.updates.length - 1].update_id && (
                                                <div className="w-0.5 h-full bg-gray-200 mt-1"></div>
                                            )}
                                        </div>
                                        <div className="flex-1 pb-4">
                                            <p className="font-medium text-gray-900">{update.status}</p>
                                            {update.note && <p className="text-sm text-gray-500">{update.note}</p>}
                                            <p className="text-xs text-gray-400 mt-1">
                                                {new Date(update.updated_at).toLocaleString()} by {update.updated_by?.full_name}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-gray-500">No updates yet</p>
                        )}
                    </div>

                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                        <div className="mb-4">
                            <p className="text-sm text-gray-500">Buyer:</p>
                            <p className="font-medium text-gray-900">{delivery.order?.buyer?.user?.full_name}</p>
                            <p className="text-sm text-gray-500">{delivery.order?.buyer?.user?.email}</p>
                        </div>
                        <div className="mb-4">
                            <p className="text-sm text-gray-500">Delivery Address:</p>
                            <p className="text-gray-900">{delivery.order?.delivery_address}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500 mb-2">Items:</p>
                            <div className="space-y-2">
                                {delivery.order?.order_items.map((item) => (
                                    <div key={item.order_item_id} className="flex justify-between text-sm">
                                        <span>{item.listing?.title} x{item.quantity}</span>
                                        <span className="font-medium">${parseFloat(item.subtotal).toFixed(2)}</span>
                                    </div>
                                ))}
                            </div>
                            <div className="flex justify-between font-bold mt-2 pt-2 border-t">
                                <span>Total</span>
                                <span>${parseFloat(delivery.order?.total_amount || 0).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

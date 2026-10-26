import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function SellerDeliveriesIndex({ orders }) {
    const { patch } = useForm();

    const updateDeliveryStatus = (deliveryId, status, note, deliveryMethod, estimatedDate) => {
        patch(route('seller.deliveries.update', deliveryId), {
            data: {
                status,
                note,
                delivery_method: deliveryMethod,
                estimated_date: estimatedDate,
            },
        });
    };

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
            <Head title="My Deliveries" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-8">My Deliveries</h1>

                    {orders.length === 0 ? (
                        <div className="text-center py-12 bg-white rounded-lg shadow">
                            <p className="text-gray-500 text-lg">No paid orders to deliver</p>
                        </div>
                    ) : (
                        <div className="space-y-6">
                            {orders.map((order) => (
                                <div key={order.order_id} className="bg-white rounded-lg shadow p-6">
                                    <div className="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">
                                                Order #{order.order_id}
                                            </h3>
                                            <p className="text-sm text-gray-500">
                                                {new Date(order.order_date).toLocaleDateString()}
                                            </p>
                                        </div>
                                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.delivery?.current_status)}`}>
                                            {order.delivery?.current_status}
                                        </span>
                                    </div>

                                    <div className="mb-4">
                                        <p className="text-sm text-gray-500">Buyer:</p>
                                        <p className="font-medium text-gray-900">{order.buyer?.user?.full_name}</p>
                                        <p className="text-sm text-gray-500">{order.buyer?.user?.email}</p>
                                    </div>

                                    <div className="mb-4">
                                        <p className="text-sm text-gray-500">Delivery Address:</p>
                                        <p className="text-gray-900">{order.delivery_address}</p>
                                    </div>

                                    <div className="mb-4">
                                        <p className="text-sm text-gray-500 mb-2">Items:</p>
                                        <div className="space-y-2">
                                            {order.order_items.map((item) => (
                                                <div key={item.order_item_id} className="flex justify-between text-sm">
                                                    <span>{item.listing?.title} x{item.quantity}</span>
                                                    <span className="font-medium">${parseFloat(item.subtotal).toFixed(2)}</span>
                                                </div>
                                            ))}
                                        </div>
                                        <div className="flex justify-between font-bold mt-2 pt-2 border-t">
                                            <span>Total</span>
                                            <span>${parseFloat(order.total_amount).toFixed(2)}</span>
                                        </div>
                                    </div>

                                    {order.delivery && (
                                        <div className="border-t pt-4">
                                            <h4 className="text-sm font-medium text-gray-900 mb-3">Update Delivery Status</h4>
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                                        Status
                                                    </label>
                                                    <select
                                                        id={`status-${order.delivery.delivery_id}`}
                                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    >
                                                        <option value="Processed" selected={order.delivery.current_status === 'Processed'}>
                                                            Processed
                                                        </option>
                                                        <option value="Dispatched" selected={order.delivery.current_status === 'Dispatched'}>
                                                            Dispatched
                                                        </option>
                                                        <option value="In transit" selected={order.delivery.current_status === 'In transit'}>
                                                            In transit
                                                        </option>
                                                        <option value="Delivered" selected={order.delivery.current_status === 'Delivered'}>
                                                            Delivered
                                                        </option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                                        Delivery Method
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id={`method-${order.delivery.delivery_id}`}
                                                        defaultValue={order.delivery.delivery_method}
                                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    />
                                                </div>

                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                                        Estimated Date
                                                    </label>
                                                    <input
                                                        type="date"
                                                        id={`date-${order.delivery.delivery_id}`}
                                                        defaultValue={order.delivery.estimated_date}
                                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    />
                                                </div>

                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                                        Note
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id={`note-${order.delivery.delivery_id}`}
                                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    />
                                                </div>
                                            </div>

                                            <button
                                                onClick={() => {
                                                    const status = document.getElementById(`status-${order.delivery.delivery_id}`).value;
                                                    const method = document.getElementById(`method-${order.delivery.delivery_id}`).value;
                                                    const date = document.getElementById(`date-${order.delivery.delivery_id}`).value;
                                                    const note = document.getElementById(`note-${order.delivery.delivery_id}`).value;
                                                    updateDeliveryStatus(order.delivery.delivery_id, status, note, method, date);
                                                }}
                                                className="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
                                            >
                                                Update Status
                                            </button>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

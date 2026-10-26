import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { IdCardIcon } from 'lucide-react';

export default function BuyerOrdersCheckout({ items, total, seller, delivery_address }) {
    const { data, setData, post, processing, errors } = useForm({
        items: items,
        delivery_address: delivery_address || '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('buyer.orders.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Checkout" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('catalogue.index')}
                            className="inline-flex items-center text-indigo-600 hover:text-indigo-700"
                        >
                            ← Back to Catalogue
                        </Link>
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

                    <form onSubmit={submit}>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {/* Order Summary */}
                            <div className="bg-white rounded-lg shadow p-6">
                                <h2 className="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                                <div className="space-y-4">
                                    {items.map((item) => (
                                        <div key={item.listing_id} className="flex items-center gap-4 pb-4 border-b">
                                            {item.listing.images && item.listing.images.length > 0 && (
                                                <img
                                                    src={`/storage/${item.listing.images.find(img => img.is_primary)?.image_path || item.listing.images[0].image_path}`}
                                                    alt={item.listing.title}
                                                    className="w-16 h-16 rounded object-cover"
                                                />
                                            )}
                                            <div className="flex-1">
                                                <p className="font-medium text-gray-900">{item.listing.title}</p>
                                                <p className="text-sm text-gray-500">Qty: {item.quantity}</p>
                                                <p className="text-sm text-gray-500">${parseFloat(item.unit_price).toFixed(2)} each</p>
                                            </div>
                                            <p className="font-medium text-gray-900">${parseFloat(item.subtotal).toFixed(2)}</p>
                                        </div>
                                    ))}
                                </div>

                                <div className="mt-6 pt-4 border-t">
                                    <div className="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span>${parseFloat(total).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Delivery Information */}
                            <div className="space-y-6">
                                <div className="bg-white rounded-lg shadow p-6">
                                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Seller Information</h2>
                                    <p className="text-gray-900 font-medium">{seller?.business_name || seller?.user?.full_name}</p>
                                    <p className="text-sm text-gray-500">{seller?.user?.email}</p>
                                </div>

                                <div className="bg-white rounded-lg shadow p-6">
                                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h2>
                                    <textarea
                                        value={data.delivery_address}
                                        onChange={(e) => setData('delivery_address', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="3"
                                        required
                                    />
                                    {errors.delivery_address && (
                                        <p className="text-red-500 text-sm mt-1">{errors.delivery_address}</p>
                                    )}
                                </div>

                                <div className="bg-white rounded-lg shadow p-6">
                                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Payment</h2>
                                    <p className="text-sm text-gray-500 mb-2">
                                        You will be redirected to the payment gateway after placing your order.
                                    </p>
                                    <p className="text-sm text-gray-500">
                                        Payment method: Paynow
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="mt-8 flex justify-end gap-4">
                            <Link
                                href={route('catalogue.index')}
                            >
                                  <Button variant='outline' size='lg'>Cancel</Button>
                            </Link>
                            <Button variant='default' size='lg'
                                type="submit"
                                disabled={processing}
                            >
                                <IdCardIcon className="mr-2"/>
                                {processing ? 'Processing...' : `Pay $${parseFloat(total).toFixed(2)}`}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

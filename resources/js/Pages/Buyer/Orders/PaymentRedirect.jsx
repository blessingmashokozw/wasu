import { Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { ArrowRightIcon, CreditCardIcon, LockIcon } from 'lucide-react';

export default function PaymentRedirect() {
    const { props } = usePage();
    const { redirect_url, order_id, amount } = props;

    const handleRedirect = () => {
        window.location.href = redirect_url;
    };

    return (
        <AuthenticatedLayout>
            <Head title="Redirecting to Payment" />
            <div className="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
                <div className="max-w-md w-full">
                    <div className="bg-white rounded-lg shadow-lg p-8">
                        {/* Icon */}
                        <div className="flex justify-center mb-6">
                            <div className="bg-blue-100 rounded-full p-4">
                                <CreditCardIcon className="h-12 w-12 text-blue-600" />
                            </div>
                        </div>

                        {/* Heading */}
                        <h1 className="text-2xl font-bold text-gray-900 text-center mb-2">
                            Proceed to Payment
                        </h1>
                        <p className="text-gray-600 text-center mb-6">
                            You will be redirected to Paynow to complete your payment.
                        </p>

                        {/* Order Details */}
                        <div className="bg-gray-50 rounded-lg p-4 mb-6">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-sm text-gray-600">Order ID:</span>
                                <span className="font-medium text-gray-900">#{order_id}</span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-gray-600">Amount:</span>
                                <span className="font-bold text-xl text-blue-600">${parseFloat(amount).toFixed(2)}</span>
                            </div>
                        </div>

                        {/* Security Notice */}
                        <div className="flex items-center justify-center gap-2 mb-6 text-sm text-gray-500">
                            <LockIcon className="h-4 w-4" />
                            <span>Secure payment powered by Paynow</span>
                        </div>

                        {/* Redirect Button */}
                        <Button
                            onClick={handleRedirect}
                            className="w-full"
                            size="lg"
                        >
                            Continue to Paynow
                            <ArrowRightIcon className="ml-2 h-4 w-4" />
                        </Button>

                        {/* Cancel Link */}
                        <div className="mt-4 text-center">
                            <a
                                href={route('buyer.orders.index')}
                                className="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Cancel and return to orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { CheckCheckIcon, PlusCircleIcon, XCircleIcon, XIcon } from 'lucide-react';

export default function AdminSellersIndex({ sellers, filter = 'Pending' }) {
    const { post } = useForm();

    const verifySeller = (sellerId) => {
        if (confirm('Are you sure you want to verify this seller?')) {
            post(route('admin.sellers.verify', sellerId));
        }
    };

    const rejectSeller = (sellerId) => {
        if (confirm('Are you sure you want to reject this seller?')) {
            post(route('admin.sellers.reject', sellerId));
        }
    };

    const changeFilter = (status) => {
        window.location.href = route('admin.sellers.index', { status });
    };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold text-white">
            Seller Verification
        </h2>}>
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="flex justify-between items-center mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">Seller Verification</h1>
                        <div className="flex gap-2">
                            {['All', 'Pending', 'Verified', 'Rejected'].map((status) => (
                                <Button
                                    key={status}
                                    variant='outline'
                                    onClick={() => changeFilter(status)}
                                // className={`px-4 py-2 rounded-md transition ${
                                //     filter === status
                                //         ? 'bg-indigo-600 text-white'
                                //         : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                // }`}
                                >
                                    {status}
                                </Button>
                            ))}
                            <Link
                                href={route('admin.sellers.create')}
                            >
                                <Button variant='default'><PlusCircleIcon className="mr-2" /> Add New Seller</Button>
                            </Link>

                        </div>
                    </div>

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Seller
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Business
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Verified By
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {sellers.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-4 text-center text-gray-500">
                                            No sellers found
                                        </td>
                                    </tr>
                                ) : (
                                    sellers.map((seller) => (
                                        <tr key={seller.seller_id}>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div>
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {seller.user?.full_name}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {seller.user?.email}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    {seller.business_name}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {seller.business_description}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${seller.verification_status === 'Verified'
                                                    ? 'bg-green-100 text-green-800'
                                                    : seller.verification_status === 'Rejected'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-yellow-100 text-yellow-800'
                                                    }`}>
                                                    {seller.verification_status}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {seller.verified_by?.full_name || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {seller.verification_status === 'Pending' && (
                                                    <>
                                                        <Button variant='default' className="mr-2"
                                                            onClick={() => verifySeller(seller.seller_id)}
                                                        >
                                                            <CheckCheckIcon class='mr-2' /> Verify
                                                        </Button>
                                                        <Button variant='destructive'
                                                            onClick={() => rejectSeller(seller.seller_id)}
                                                        >
                                                            <XCircleIcon class='mr-2' />Reject
                                                        </Button>
                                                    </>
                                                )}
                                                {seller.verification_status !== 'Pending' && (
                                                    <span className="text-gray-400">No actions</span>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

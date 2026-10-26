import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { Edit2, PlusCircle, Trash2 } from 'lucide-react';

export default function SellerListingsIndex({ listings }) {
    const { patch, delete: destroy } = useForm();

    const updateStatus = (listingId, status) => {
        patch(route('seller.listings.status', listingId), {
            data: { listing_status: status },
        });
    };

    const deleteListing = (listingId) => {
        if (confirm('Are you sure you want to delete this listing?')) {
            destroy(route('seller.listings.destroy', listingId));
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'Active':
                return 'bg-green-100 text-green-800';
            case 'Out of stock':
                return 'bg-yellow-100 text-yellow-800';
            case 'Hidden':
                return 'bg-gray-100 text-gray-800';
            case 'Removed':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="My Listings" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="flex justify-between items-center mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">My Listings</h1>
                        <Link
                            href={route('seller.listings.create')}
                        >
                            <Button variant="default"><PlusCircle className="mr-2"/> Create New Listing</Button>
                        </Link>
                    </div>

                    {listings.length === 0 ? (
                        <div className="text-center py-12 bg-white rounded-lg shadow">
                            <p className="text-gray-500 text-lg mb-4">You have no listings yet</p>
                            <Link
                                href={route('seller.listings.create')}
                                className="text-indigo-600 hover:text-indigo-700"
                            >
                                Create your first listing
                            </Link>
                        </div>
                    ) : (
                        <div className="bg-white rounded-lg shadow overflow-hidden">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Listing
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {listings.map((listing) => (
                                        <tr key={listing.listing_id}>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    {listing.images && listing.images.length > 0 && (
                                                        <img
                                                            src={`${listing.images.find(img => img.is_primary)?.image_path || listing.images[0].image_path}`}
                                                            alt={listing.title}
                                                            className="h-10 w-10 rounded object-cover mr-3"
                                                        />
                                                    )}
                                                    <div>
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {listing.title}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {listing.listing_type}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {listing.category?.category_name}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                ${parseFloat(listing.price).toFixed(2)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {listing.quantity_available}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(listing.listing_status)}`}>
                                                    {listing.listing_status}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div className="flex items-center gap-2">
                                                    <select
                                                        value={listing.listing_status}
                                                        onChange={(e) => updateStatus(listing.listing_id, e.target.value)}
                                                        className="text-xs rounded border-gray-300"
                                                    >
                                                        <option value="Active">Active</option>
                                                        <option value="Out of stock">Out of stock</option>
                                                        <option value="Hidden">Hidden</option>
                                                        <option value="Removed">Removed</option>
                                                    </select>

                                                    <Link href={route('seller.listings.edit', listing.listing_id)}>
                                                        <Button variant="outline"><Edit2 /></Button>
                                                    </Link>
                                                    <Button variant="destructive"
                                                        onClick={() => deleteListing(listing.listing_id)}
                                                    ><Trash2 /> 
                                                    </Button>
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

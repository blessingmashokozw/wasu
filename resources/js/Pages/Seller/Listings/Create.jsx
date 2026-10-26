import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeftCircleIcon, SaveIcon, XIcon } from 'lucide-react';
import { Button } from '@/Components/ui/button';

export default function SellerListingsCreate({ categories }) {
    const { data, setData, post, processing, errors } = useForm({
        category_id: '',
        listing_type: 'Product',
        title: '',
        description: '',
        price: '',
        quantity_available: '',
        images: [],
    });

    const handleImageChange = (e) => {
        setData('images', [...data.images, ...Array.from(e.target.files)]);
    };

    const removeImage = (index) => {
        setData('images', data.images.filter((_, i) => i !== index));
    };

    const submit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('category_id', data.category_id);
        formData.append('listing_type', data.listing_type);
        formData.append('title', data.title);
        formData.append('description', data.description);
        formData.append('price', data.price);
        formData.append('quantity_available', data.quantity_available);
        data.images.forEach((image) => {
            formData.append('images[]', image);
        });

        post(route('seller.listings.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Create Listing" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('seller.listings.index')}
                            className="inline-flex items-center"
                        >
                            <Button variant='outline'><ArrowLeftCircleIcon className="mr-2" />   Back to My Listings</Button>

                        </Link>
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900 mb-8">Create New Listing</h1>

                    <div className="bg-white rounded-lg shadow p-6">
                        <form onSubmit={submit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Category *
                                    </label>
                                    <select
                                        value={data.category_id}
                                        onChange={(e) => setData('category_id', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="">Select a category</option>
                                        {categories.map((category) => (
                                            <option key={category.category_id} value={category.category_id}>
                                                {category.category_name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.category_id && (
                                        <p className="text-red-500 text-sm mt-1">{errors.category_id}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Type *
                                    </label>
                                    <select
                                        value={data.listing_type}
                                        onChange={(e) => setData('listing_type', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="Product">Product</option>
                                        <option value="Service">Service</option>
                                    </select>
                                    {errors.listing_type && (
                                        <p className="text-red-500 text-sm mt-1">{errors.listing_type}</p>
                                    )}
                                </div>

                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Title *
                                    </label>
                                    <input
                                        type="text"
                                        value={data.title}
                                        onChange={(e) => setData('title', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        maxLength="120"
                                        required
                                    />
                                    {errors.title && (
                                        <p className="text-red-500 text-sm mt-1">{errors.title}</p>
                                    )}
                                </div>

                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Description *
                                    </label>
                                    <textarea
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="4"
                                        required
                                    />
                                    {errors.description && (
                                        <p className="text-red-500 text-sm mt-1">{errors.description}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Price ($) *
                                    </label>
                                    <input
                                        type="number"
                                        value={data.price}
                                        onChange={(e) => setData('price', e.target.value)}
                                        step="0.01"
                                        min="0"
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    {errors.price && (
                                        <p className="text-red-500 text-sm mt-1">{errors.price}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Quantity Available *
                                    </label>
                                    <input
                                        type="number"
                                        value={data.quantity_available}
                                        onChange={(e) => setData('quantity_available', e.target.value)}
                                        min="0"
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    {errors.quantity_available && (
                                        <p className="text-red-500 text-sm mt-1">{errors.quantity_available}</p>
                                    )}
                                </div>

                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Images * (1-5 images, max 2MB each)
                                    </label>
                                    <input
                                        type="file"
                                        onChange={handleImageChange}
                                        accept="image/jpeg,image/png"
                                        multiple
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    {errors.images && (
                                        <p className="text-red-500 text-sm mt-1">{errors.images}</p>
                                    )}

                                    {data.images.length > 0 && (
                                        <div className="mt-4 grid grid-cols-5 gap-2">
                                            {data.images.map((image, index) => (
                                                <div key={index} className="relative">
                                                    <img
                                                        src={URL.createObjectURL(image)}
                                                        alt={`Preview ${index + 1}`}
                                                        className="w-full h-20 object-cover rounded"
                                                    />
                                                    <button
                                                        type="button"
                                                        onClick={() => removeImage(index)}
                                                        className="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 text-xs"
                                                    >
                                                        ×
                                                    </button>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="mt-6 flex justify-end gap-4">
                                <Link
                                    href={route('seller.listings.index')}
                                >
                                    <Button variant='outline'><XIcon className="mr-2"/> Cancel</Button>
                                </Link>
                                <Button variant='default'
                                    type="submit"
                                    disabled={processing}
                                >
                                    <SaveIcon className="mr-2"/> Create Listing
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

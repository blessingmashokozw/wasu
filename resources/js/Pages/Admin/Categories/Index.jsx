import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';

export default function AdminCategoriesIndex({ categories }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        category_name: '',
        description: '',
    });

    const [isEditing, setIsEditing] = useState(null);
    const { data: editData, setData: setEditData, put, processing: editProcessing } = useForm({
        category_name: '',
        description: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('admin.categories.store'), {
            onSuccess: () => reset(),
        });
    };

    const startEdit = (category) => {
        setIsEditing(category.category_id);
        setEditData({
            category_name: category.category_name,
            description: category.description,
        });
    };

    const cancelEdit = () => {
        setIsEditing(null);
        setEditData({ category_name: '', description: '' });
    };

    const updateCategory = (e) => {
        e.preventDefault();
        put(route('admin.categories.update', isEditing), {
            onSuccess: () => cancelEdit(),
        });
    };

    const deleteCategory = (categoryId) => {
        if (confirm('Are you sure you want to delete this category?')) {
            
            useForm().delete(route('admin.categories.destroy', categoryId));
        }
    };

    return (
        <AuthenticatedLayout
         header={<h2 className="text-xl font-semibold text-white">
                    Category Management
                </h2>}
        >
            <Head title="Category Management" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                    {/* Create Category Form */}
                    <div className="bg-white rounded-lg shadow p-6 mb-8">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Create New Category</h2>
                        <form onSubmit={submit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Category Name
                                    </label>
                                    <input
                                        type="text"
                                        value={data.category_name}
                                        onChange={(e) => setData('category_name', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    {errors.category_name && (
                                        <p className="text-red-500 text-sm mt-1">{errors.category_name}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="1"
                                    />
                                    {errors.description && (
                                        <p className="text-red-500 text-sm mt-1">{errors.description}</p>
                                    )}
                                </div>
                            </div>

                            <div className="mt-4">
                                <Button variant='default'
                                    type="submit"
                                    disabled={processing}
                                >
                                    Create Category
                                </Button>
                            </div>
                        </form>
                    </div>

                    {/* Categories List */}
                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {categories.length === 0 ? (
                                    <tr>
                                        <td colSpan="3" className="px-6 py-4 text-center text-gray-500">
                                            No categories found
                                        </td>
                                    </tr>
                                ) : (
                                    categories.map((category) => (
                                        <tr key={category.category_id}>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {isEditing === category.category_id ? (
                                                    <input
                                                        type="text"
                                                        value={editData.category_name}
                                                        onChange={(e) => setEditData('category_name', e.target.value)}
                                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    />
                                                ) : (
                                                    <span className="text-sm font-medium text-gray-900">
                                                        {category.category_name}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4">
                                                {isEditing === category.category_id ? (
                                                    <textarea
                                                        value={editData.description}
                                                        onChange={(e) => setEditData('description', e.target.value)}
                                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        rows="1"
                                                    />
                                                ) : (
                                                    <span className="text-sm text-gray-500">
                                                        {category.description || '-'}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {isEditing === category.category_id ? (
                                                    <>
                                                        <button
                                                            onClick={updateCategory}
                                                            disabled={editProcessing}
                                                            className="text-green-600 hover:text-green-900 mr-3"
                                                        >
                                                            Save
                                                        </button>
                                                        <button
                                                            onClick={cancelEdit}
                                                            className="text-gray-600 hover:text-gray-900"
                                                        >
                                                            Cancel
                                                        </button>
                                                    </>
                                                ) : (
                                                    <>
                                                        <Button variant='outline'
                                                            onClick={() => startEdit(category)}
                                                            className="mr-3"
                                                        >
                                                            Edit
                                                        </Button>
                                                        <Button variant='destructive'
                                                            onClick={() => deleteCategory(category.category_id)}
                                                            className="text-red-600 hover:text-red-900"
                                                        >
                                                            Delete
                                                        </Button>
                                                    </>
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

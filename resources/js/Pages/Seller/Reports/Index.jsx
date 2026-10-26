import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    LineChart,
    Line,
    BarChart,
    Bar,
    PieChart,
    Pie,
    Cell,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
} from 'recharts';

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D', '#FFC658', '#8DD1E1'];

export default function SellerReports({ summary, monthly_revenue, category_revenue, top_listings, order_status_distribution, payment_method_distribution }) {
    const formatCurrency = (value) => `$${value.toFixed(2)}`;

    const CustomTooltip = ({ active, payload, label }) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-white p-3 border border-gray-300 rounded shadow-lg">
                    <p className="font-semibold">{label}</p>
                    {payload.map((entry, index) => (
                        <p key={index} style={{ color: entry.color }}>
                            {entry.name}: {entry.name === 'amount' || entry.name === 'Amount' ? formatCurrency(entry.value) : entry.value}
                        </p>
                    ))}
                </div>
            );
        }
        return null;
    };

    const PieTooltip = ({ active, payload }) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-white p-3 border border-gray-300 rounded shadow-lg">
                    <p className="font-semibold">{payload[0].name}</p>
                    <p style={{ color: payload[0].color }}>
                        {payload[0].name === 'amount' || payload[0].name === 'Amount' ? formatCurrency(payload[0].value) : payload[0].value}
                    </p>
                </div>
            );
        }
        return null;
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-white">
                    Seller Reports
                </h2>
            }
        >
            <Head title="Seller Reports" />

            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {/* Summary Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Total Listings</h3>
                            <p className="text-3xl font-bold text-gray-900">{summary.total_listings}</p>
                        </div>
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Total Orders</h3>
                            <p className="text-3xl font-bold text-gray-900">{summary.total_orders}</p>
                        </div>
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Total Revenue</h3>
                            <p className="text-3xl font-bold text-blue-600">{formatCurrency(summary.total_revenue)}</p>
                        </div>
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Successful Orders</h3>
                            <p className="text-3xl font-bold text-green-600">{summary.successful_orders}</p>
                        </div>
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Cancelled Orders</h3>
                            <p className="text-3xl font-bold text-red-600">{summary.cancelled_orders}</p>
                        </div>
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Pending Orders</h3>
                            <p className="text-3xl font-bold text-yellow-600">{summary.pending_orders}</p>
                        </div>
                    </div>

                    {/* Charts Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        {/* Monthly Revenue Chart */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <LineChart data={monthly_revenue}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="month" />
                                    <YAxis />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend />
                                    <Line
                                        type="monotone"
                                        dataKey="amount"
                                        stroke="#8884d8"
                                        strokeWidth={2}
                                        name="Revenue"
                                    />
                                </LineChart>
                            </ResponsiveContainer>
                        </div>

                        {/* Order Status Distribution */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <PieChart>
                                    <Pie
                                        data={order_status_distribution}
                                        cx="50%"
                                        cy="50%"
                                        labelLine={false}
                                        label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                                        outerRadius={80}
                                        fill="#8884d8"
                                        dataKey="count"
                                        nameKey="status"
                                    >
                                        {order_status_distribution.map((entry, index) => (
                                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                        ))}
                                    </Pie>
                                    <Tooltip content={<PieTooltip />} />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        {/* Category Revenue */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Revenue by Category</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <BarChart data={category_revenue}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="category" angle={-45} textAnchor="end" height={100} />
                                    <YAxis />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend />
                                    <Bar dataKey="amount" fill="#8884d8" name="Revenue" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>

                        {/* Payment Method Distribution */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <BarChart data={payment_method_distribution}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="method" angle={-45} textAnchor="end" height={100} />
                                    <YAxis />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend />
                                    <Bar dataKey="amount" fill="#82CA9D" name="Amount" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Top Listings */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Top Performing Listings</h3>
                        <ResponsiveContainer width="100%" height={400}>
                            <BarChart data={top_listings} layout="vertical">
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis type="number" />
                                <YAxis dataKey="listing" type="category" width={300} tick={{ fontSize: 12 }} />
                                <Tooltip content={<CustomTooltip />} />
                                <Legend />
                                <Bar dataKey="amount" fill="#FFBB28" name="Revenue" />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>

                    {/* No Data Message */}
                    {summary.total_orders === 0 && (
                        <div className="mt-8 text-center py-12 bg-white rounded-lg shadow">
                            <p className="text-gray-500 text-lg">No order data available</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

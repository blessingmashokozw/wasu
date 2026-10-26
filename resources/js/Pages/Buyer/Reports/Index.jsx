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

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D'];

export default function BuyerReports({ summary, monthly_spending, category_spending, seller_spending, order_status_distribution }) {
    const formatCurrency = (value) => `$${value.toFixed(2)}`;

    const CustomTooltip = ({ active, payload, label }) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-white p-3 border border-gray-300 rounded shadow-lg">
                    <p className="font-semibold">{label}</p>
                    {payload.map((entry, index) => (
                        <p key={index} style={{ color: entry.color }}>
                            {entry.name}: {entry.name === 'amount' ? formatCurrency(entry.value) : entry.value}
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
                        {payload[0].name === 'amount' ? formatCurrency(payload[0].value) : payload[0].value}
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
                    Buyer Reports
                </h2>
            }
        >
            <Head title="Buyer Reports" />

            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {/* Summary Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Total Orders</h3>
                            <p className="text-3xl font-bold text-gray-900">{summary.total_orders}</p>
                        </div>
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-sm font-medium text-gray-500 mb-2">Total Spent</h3>
                            <p className="text-3xl font-bold text-gray-900">{formatCurrency(summary.total_spent)}</p>
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
                        {/* Monthly Spending Chart */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Monthly Spending</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <LineChart data={monthly_spending}>
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
                                        name="Amount Spent"
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

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {/* Category Spending */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Spending by Category</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <BarChart data={category_spending}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="category" angle={-45} textAnchor="end" height={100} />
                                    <YAxis />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend />
                                    <Bar dataKey="amount" fill="#8884d8" name="Amount Spent" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>

                        {/* Seller Spending */}
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Spending by Seller</h3>
                            <ResponsiveContainer width="100%" height={300}>
                                <BarChart data={seller_spending}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="seller" angle={-45} textAnchor="end" height={100} />
                                    <YAxis />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend />
                                    <Bar dataKey="amount" fill="#82CA9D" name="Amount Spent" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
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

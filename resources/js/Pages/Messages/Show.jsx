import { Head, Link, useForm } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function MessagesShow({ messages, other_user_id, order_id, listing_id }) {
    const { auth } = usePage().props;
    const currentUserId = auth.user.user_id;

    const { data, setData, post, processing, errors } = useForm({
        receiver_id: other_user_id,
        message_text: '',
        order_id: order_id || null,
        listing_id: listing_id || null,
    });

    const messagesEndRef = useRef(null);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    const submit = (e) => {
        e.preventDefault();
        post(route('messages.store'), {
            onSuccess: () => {
                setData('message_text', '');
                scrollToBottom();
            },
        });
    };

    const otherUser = messages.length > 0 ? (messages[0].sender_id === currentUserId ? messages[0].receiver : messages[0].sender) : null;

    return (
        <AuthenticatedLayout>
            <Head title="Conversation" />
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="mb-8">
                        <Link
                            href={route('messages.index')}
                            className="inline-flex items-center text-indigo-600 hover:text-indigo-700"
                        >
                            ← Back to Messages
                        </Link>
                    </div>

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        {/* Header */}
                        <div className="border-b p-4">
                            <h2 className="text-lg font-semibold text-gray-900">
                                {otherUser?.full_name}
                            </h2>
                            {order_id && <p className="text-sm text-gray-500">Order #{order_id}</p>}
                            {listing_id && <p className="text-sm text-gray-500">About listing</p>}
                        </div>

                        {/* Messages */}
                        <div className="h-96 overflow-y-auto p-4 space-y-4">
                            {messages.length === 0 ? (
                                <p className="text-center text-gray-500 py-8">No messages yet. Start the conversation!</p>
                            ) : (
                                messages.map((message) => {
                                    const isOwn = message.sender_id === currentUserId;
                                    return (
                                        <div
                                            key={message.message_id}
                                            className={`flex ${isOwn ? 'justify-end' : 'justify-start'}`}
                                        >
                                            <div
                                                className={`max-w-xs md:max-w-md lg:max-w-lg rounded-lg p-3 ${
                                                    isOwn
                                                        ? 'bg-indigo-600 text-white'
                                                        : 'bg-gray-100 text-gray-900'
                                                }`}
                                            >
                                                <p className="text-sm">{message.message_text}</p>
                                                <p
                                                    className={`text-xs mt-1 ${
                                                        isOwn ? 'text-indigo-200' : 'text-gray-500'
                                                    }`}
                                                >
                                                    {new Date(message.sent_at).toLocaleString()}
                                                </p>
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                            <div ref={messagesEndRef} />
                        </div>

                        {/* Input */}
                        <form onSubmit={submit} className="border-t p-4">
                            <div className="flex gap-2">
                                <input
                                    type="text"
                                    value={data.message_text}
                                    onChange={(e) => setData('message_text', e.target.value)}
                                    placeholder="Type a message..."
                                    className="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition disabled:bg-gray-300"
                                >
                                    Send
                                </button>
                            </div>
                            {errors.message_text && (
                                <p className="text-red-500 text-sm mt-1">{errors.message_text}</p>
                            )}
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

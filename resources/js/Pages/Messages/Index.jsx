import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent } from '@/components/ui/card';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { router, usePage } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { SendHorizonalIcon } from 'lucide-react';

export default function MessagesIndex({ conversations }) {
    const { url } = usePage();
    const [selectedConversation, setSelectedConversation] = useState(null);
    const [messages, setMessages] = useState([]);
    const [messageText, setMessageText] = useState('');
    const { post, processing } = useForm();

    // Check if coming from catalogue with pre-filled parameters
    useEffect(() => {
        const urlParams = new URLSearchParams(url.split('?')[1]);
        const userId = urlParams.get('user_id');
        const listingId = urlParams.get('listing_id');

        if (userId && listingId) {
            // Find or create conversation with this user about this listing
            const existingConversation = conversations.find(conv =>
                conv.other_user.user_id == userId &&
                conv.listing?.listing_id == listingId
            );

            console.log(existingConversation)
            if (existingConversation) {
                selectConversation(existingConversation);
            } else {
                // Create a temporary conversation object
                const tempConversation = {
                    other_user: { user_id: userId, full_name: 'Seller' },
                    listing: { listing_id: listingId, title: 'Product Inquiry' },
                    order: null,
                    latest_message: null,
                    unread_count: 0,
                };
                selectConversation(tempConversation);
                
            }
        }
    }, [url, conversations]);

    const selectConversation = async (conversation) => {
   
        setSelectedConversation(conversation);
        // Load messages for this conversation
        const response = await fetch(route('messages.show', {
            user_id: conversation.other_user.user_id,
            order_id: conversation.order?.order_id,
            listing_id: conversation.listing?.listing_id,
        }), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();
        setMessages(data.messages || []);
    };

    const sendMessage = (e) => {
        e.preventDefault();

        if (!messageText.trim()) return;

        post(route('messages.store'), {
            receiver_id: selectedConversation.other_user.user_id,
            message_text: messageText,
            order_id: selectedConversation.order?.order_id,
            listing_id: selectedConversation.listing?.listing_id,
            onSuccess: () => {
                setMessageText('');
                // Reload messages
                selectConversation(selectedConversation);
            },
            onError: (errors) => {
                console.error('Message send error:', errors);
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Messages" />
            <div className="h-[calc(100vh-64px)] bg-gray-50">
                <div className="max-w-7xl mx-auto  flex h-full">
                    {/* Sidebar - Conversation List */}
                    <div className="w-80 border-r bg-white flex flex-col">
                        <div className="p-4 border-b">
                            <h1 className="text-xl font-bold text-gray-900">Messages</h1>
                        </div>
                        <ScrollArea className="flex-1">
                            {conversations.length === 0 ? (
                                <div className="p-4 text-center text-gray-500">
                                    No conversations yet
                                </div>
                            ) : (
                                conversations.map((conversation) => (
                                    <div
                                        key={`${conversation.other_user.user_id}-${conversation.order?.order_id || conversation.listing?.listing_id || 'general'}`}
                                        onClick={() => selectConversation(conversation)}
                                        className={`p-4 border-b cursor-pointer hover:bg-gray-50 transition ${
                                            selectedConversation?.other_user.user_id === conversation.other_user.user_id &&
                                            selectedConversation?.order?.order_id === conversation.order?.order_id &&
                                            selectedConversation?.listing?.listing_id === conversation.listing?.listing_id
                                                ? 'bg-blue-50'
                                                : ''
                                        }`}
                                    >
                                        <div className="flex items-start gap-3">
                                            <Avatar>
                                                <AvatarFallback>
                                                    {conversation.other_user.full_name.charAt(0)}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div className="flex-1 min-w-0">
                                                <div className="flex justify-between items-start">
                                                    <h3 className="font-semibold text-gray-900 text-sm">
                                                        {conversation.other_user.full_name}
                                                    </h3>
                                                    <span className="text-xs text-gray-500">
                                                        {new Date(conversation.latest_message.sent_at).toLocaleDateString()}
                                                    </span>
                                                </div>
                                                <p className="text-sm text-gray-600 truncate mt-1">
                                                    {conversation.latest_message.message_text}
                                                </p>
                                                {conversation.order && (
                                                    <p className="text-xs text-gray-500 mt-1">Order #{conversation.order.order_id}</p>
                                                )}
                                                {conversation.listing && (
                                                    <p className="text-xs text-gray-500 mt-1">About: {conversation.listing.title}</p>
                                                )}
                                            </div>
                                            {conversation.unread_count > 0 && (
                                                <div className="bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                                    {conversation.unread_count}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ))
                            )}
                        </ScrollArea>
                    </div>

                    {/* Main Panel - Chat View */}
                    <div className="flex-1 flex flex-col">
                        {selectedConversation ? (
                            <>
                                {/* Chat Header */}
                                <div className="p-4 border-b bg-white">
                                    <div className="flex items-center gap-3">
                                        <Avatar>
                                            <AvatarFallback>
                                                {selectedConversation.other_user.full_name.charAt(0)}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <h2 className="font-semibold text-gray-900">
                                                {selectedConversation.other_user.full_name}
                                            </h2>
                                            {selectedConversation.order && (
                                                <p className="text-sm text-gray-500">Order #{selectedConversation.order.order_id}</p>
                                            )}
                                            {selectedConversation.listing && (
                                                <p className="text-sm text-gray-500">About: {selectedConversation.listing.title}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Messages */}
                                <ScrollArea className="flex-1 p-4">
                                    <div className="space-y-4">
                                        {messages.map((message) => (
                                            <div
                                                key={message.message_id}
                                                className={`flex gap-3 ${message.sender_id === selectedConversation.other_user.user_id ? 'flex-row' : 'flex-row-reverse'}`}
                                            >
                                                <Avatar className="w-8 h-8 shrink-0">
                                                    <AvatarFallback>
                                                        {message.sender_id === selectedConversation.other_user.user_id
                                                            ? selectedConversation.other_user.full_name.charAt(0)
                                                            : 'You'}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <div className={`flex flex-col max-w-[70%] ${message.sender_id === selectedConversation.other_user.user_id ? 'items-start' : 'items-end'}`}>
                                                    <div
                                                        className={`rounded-lg p-3 ${
                                                            message.sender_id === selectedConversation.other_user.user_id
                                                                ? 'bg-gray-200 text-gray-900'
                                                                : 'bg-blue-600 text-white'
                                                        }`}
                                                    >
                                                        <p className="text-sm">{message.message_text}</p>
                                                    </div>
                                                    <span className="text-xs text-gray-500 mt-1">
                                                        {new Date(message.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                                    </span>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </ScrollArea>

                                {/* Message Input */}
                                <div className="p-4 border-t bg-white">
                                    <form onSubmit={sendMessage} className="flex gap-2">
                                        <input
                                            type="text"
                                            value={messageText}
                                            onChange={(e) => setMessageText(e.target.value)}
                                            placeholder="Type a message..."
                                            className="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            required
                                        />
                                        <Button variant='default'
                                            type="submit"
                                            disabled={processing}
                                            className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                                        >
                                            <SendHorizonalIcon/>
                                        </Button>
                                    </form>
                                </div>
                            </>
                        ) : (
                            <div className="flex-1 flex items-center justify-center bg-gray-50">
                                <div className="text-center text-gray-500">
                                    <p className="text-lg">Select a conversation to start messaging</p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

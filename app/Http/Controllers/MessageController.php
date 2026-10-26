<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index(): Response
    {
        $userId = auth()->id();

        // Get all messages where user is sender or receiver
        $messages = Message::with(['sender', 'receiver', 'order', 'listing'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->orderBy('sent_at', 'desc')
            ->get();

        // Group by conversation (other user + optional order/listing)
        $conversations = $messages->groupBy(function ($message) use ($userId) {
            $otherUserId = $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
            $context = $message->order_id ? "order-{$message->order_id}" : ($message->listing_id ? "listing-{$message->listing_id}" : 'general');
            return "{$otherUserId}-{$context}";
        })->map(function ($conversationMessages) {
            $latestMessage = $conversationMessages->first();
            $otherUser = $latestMessage->sender_id === auth()->id() ? $latestMessage->receiver : $latestMessage->sender;

            return [
                'other_user' => $otherUser,
                'order' => $latestMessage->order,
                'listing' => $latestMessage->listing,
                'latest_message' => $latestMessage,
                'unread_count' => $conversationMessages->where('receiver_id', auth()->id())->where('is_read', false)->count(),
            ];
        })->values();

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Display a specific conversation.
     */
    public function show(Request $request): Response|JsonResponse|RedirectResponse
    {
        $userId = auth()->id();
        $otherUserId = $request->query('user_id');
        $orderId = $request->query('order_id');
        $listingId = $request->query('listing_id');

        if (!$otherUserId) {
            return redirect()->route('messages.index');
        }

        // Get messages between the two users in this context
        $query = Message::with(['sender', 'receiver', 'order', 'listing'])
            ->where(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $userId)->where('receiver_id', $otherUserId)
                  ->orWhere('sender_id', $otherUserId)->where('receiver_id', $userId);
            });

        if ($orderId) {
            $query->where('order_id', $orderId);
        } elseif ($listingId) {
            $query->where('listing_id', $listingId);
        }

        $messages = $query->orderBy('sent_at', 'asc')->get();

        // Mark messages as read
        Message::where('receiver_id', $userId)
            ->where('sender_id', $otherUserId)
            ->when($orderId, fn ($q) => $q->where('order_id', $orderId))
            ->when($listingId, fn ($q) => $q->where('listing_id', $listingId))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'messages' => $messages,
                'other_user_id' => $otherUserId,
                'order_id' => $orderId,
                'listing_id' => $listingId,
            ]);
        }

        return Inertia::render('Messages/Show', [
            'messages' => $messages,
            'other_user_id' => $otherUserId,
            'order_id' => $orderId,
            'listing_id' => $listingId,
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Message::class);

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'message_text' => 'required|string|max:2000',
            'order_id' => 'nullable|exists:orders,order_id',
            'listing_id' => 'nullable|exists:listings,listing_id',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'message_text' => $validated['message_text'],
            'order_id' => $validated['order_id'] ?? null,
            'listing_id' => $validated['listing_id'] ?? null,
            'is_read' => false,
            'sent_at' => now(),
        ]);

        // Create notification for receiver
        Notification::create([
            'user_id' => $validated['receiver_id'],
            'notification_type' => 'New Message',
            'message' => 'You have received a new message',
            'is_read' => false,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * Update a message (only sender can edit).
     */
    public function update(Request $request, Message $message)
    {
        Gate::authorize('update', $message);

        $validated = $request->validate([
            'message_text' => 'required|string|max:2000',
        ]);

        $message->update([
            'message_text' => $validated['message_text'],
        ]);

        return back()->with('success', 'Message updated successfully.');
    }

    /**
     * Delete a message (only sender can delete).
     */
    public function destroy(Message $message)
    {
        Gate::authorize('delete', $message);

        $message->delete();

        return back()->with('success', 'Message deleted successfully.');
    }
}

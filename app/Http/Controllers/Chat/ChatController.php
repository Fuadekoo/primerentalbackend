<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Pusher\Pusher;

class ChatController extends Controller
{
    // Send message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $sender = auth()->user();
        $receiver = User::where('role', 'admin')->first();

        if ($request->has('receiver_id')) {
            $receiver = User::find($request->receiver_id);
        }

        $chat = Chat::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
        ]);

        $this->triggerPusherEvent($chat);

        return response()->json(['message' => 'Message sent successfully', 'data' => $chat]);
    }


    // Get messages
    public function getMessages()
    {
        $user = auth()->user();
        $chats = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get();

        return response()->json(['data' => $chats]);
    }

    // Reply to message by admin
    public function replyMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $admin = auth()->user();
        $receiver = User::find($userId);

        if (!$receiver) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $chat = Chat::create([
            'sender_id' => $admin->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
        ]);

        $this->triggerPusherEvent($chat);

        return response()->json(['message' => 'Reply sent successfully', 'data' => $chat]);
    }

    // Get users list that send message  with messages
    public function getUsersWithMessages()
    {
        $admin = auth()->user();
        $users = Chat::where('receiver_id', $admin->id)
            ->orWhere('sender_id', $admin->id)
            ->with('sender', 'receiver')
            ->get()
            ->groupBy('sender_id');

        return response()->json(['data' => $users]);
    }


    // Get messages for admin for each sender id
    public function getMessagesForAdmin($senderId)
    {
        $admin = auth()->user();

        $messages = Chat::where('sender_id', $senderId)
            ->where('receiver_id', $admin->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['data' => $messages]);
    }


    private function triggerPusherEvent($chat)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );

        $pusher->trigger('chat-channel', 'chat-event', $chat);
    }


     // Fetch users with their latest message
     public function AddmingetUsersWithMessages()
     {
         $users = User::with(['latestMessage' => function ($query) {
             $query->latest();
         }])->get();

         $userList = $users->map(function ($user) {
             return [
                 'id' => $user->id,
                 'email' => $user->email,
                 'profile_photo' => $user->profile_photo_url ?? 'default.png',
                 'latest_message' => $user->latestMessage->message ?? 'No messages yet',
             ];
         });

         return response()->json(['users' => $userList]);
     }
}

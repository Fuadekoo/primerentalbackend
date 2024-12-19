<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message; // Assuming you have a Message model
use App\Models\User;
use Illuminate\Http\Request;
use Pusher\Pusher;

class MessageController extends Controller
{
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

        $message = Chat::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
        ]);

        $this->triggerPusherEvent($message);

        return response()->json(['message' => 'Message sent successfully', 'data' => $message]);
    }

    public function getMessages()
    {
        $user = auth()->user();
        $messages = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get();

        return response()->json(['data' => $messages]);
    }

    public function fetchMessages($userId)
    {
        $user = auth()->user();
        $messages = Chat::where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->orWhere('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['data' => $messages]);
    }

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

        $message = Chat::create([
            'sender_id' => $admin->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
        ]);

        $this->triggerPusherEvent($message);

        return response()->json(['message' => 'Reply sent successfully', 'data' => $message]);
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

      public function getUsersWithMessages()
      {
          $admin = auth()->user();

          // Get the latest message for each sender
          $latestMessages = Chat::where('receiver_id', $admin->id)
              ->orWhere('sender_id', $admin->id)
              ->with('sender')
              ->orderBy('created_at', 'desc')
              ->get()
              ->unique('sender_id')
              ->values();

          // Format the response to include only sender data and the latest message
          $response = $latestMessages->map(function ($message) {
              return [
                  'sender_id' => $message->sender_id,
                  'sender_email' => $message->sender->email,
                  'message' => $message->message,
                  'created_at' => $message->created_at,
              ];
          });
          return response()->json(['data' => $response]);
        }


     // New method to fetch messages where the authenticated user is either the sender or receiver
     public function getMessagesForUser()
     {
         $user = auth()->user();

         $messages = Chat::where('sender_id', $user->id)
             ->orWhere('receiver_id', $user->id)
             ->orderBy('created_at', 'asc')
             ->get()
             ->map(function ($message) use ($user) {
                 $message->type = $message->sender_id === $user->id ? 'sent' : 'received';
                 return $message;
             });

         return response()->json(['data' => $messages]);
     }

      // New method to fetch messages between the authenticated admin and a specific user
    public function getMessagesWithUser($userId)
    {
        $admin = auth()->user();

        $messages = Chat::where(function ($query) use ($admin, $userId) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($admin, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $admin->id);
        })->orderBy('created_at', 'asc')
          ->get()
          ->map(function ($message) use ($admin) {
              $message->type = $message->sender_id === $admin->id ? 'sent' : 'received';
              return $message;
          });

        return response()->json(['data' => $messages]);
    }


    private function triggerPusherEvent($message)
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

        $pusher->trigger('message-channel', 'message-event', $message);
    }
}

<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function index()
    {
        $notifications = auth()->user()->notifications;
        return response()->json($notifications);
    }

    public function markAsRead(Request $request)
    {
        $notification = auth()->user()->notifications->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'message' => 'Notification marked as read']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Notification not found'], 404);
        }
    }

    public function acceptBooking(Request $request)
    {
        $notification = auth()->user()->notifications->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            $notification->data['booking']->status = 'accepted';
            $notification->data['booking']->save();
            return response()->json(['status' => 'success', 'message' => 'Booking accepted']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Notification not found'], 404);
        }
    }

    public function rejectBooking(Request $request)
    {
        $notification = auth()->user()->notifications->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            $notification->data['booking']->status = 'rejected';
            $notification->data['booking']->save();
            return response()->json(['status' => 'success', 'message' => 'Booking rejected']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Notification not found'], 404);
        }
    }

    public function chattoUser(Request $request)
    {
        $notification = auth()->user()->notifications->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'message' => 'Chatting with user']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Notification not found'], 404);
        }
    }
}

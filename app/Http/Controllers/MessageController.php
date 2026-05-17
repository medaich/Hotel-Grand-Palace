<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = Message::with('sender')->orderBy('created_at', 'desc')->get();

        $view_message = null;
        if ($request->filled('view')) {
            $view_message = Message::with('sender')->find($request->view);
            if ($view_message && !$view_message->is_read) {
                $view_message->update(['is_read' => true]);
            }
        }

        return view('messages.index', compact('messages', 'view_message'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:200',
            'body' => 'required|string',
        ]);

        $validated['sender_id'] = auth()->id();
        $validated['is_read'] = false;

        Message::create($validated);

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }
}

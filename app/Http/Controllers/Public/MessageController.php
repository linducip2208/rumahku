<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        Message::create([
            'property_id' => $validated['property_id'],
            'sender_user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('chat_success', 'Pesan Anda terkirim ke diskusi properti ini.');
    }
}

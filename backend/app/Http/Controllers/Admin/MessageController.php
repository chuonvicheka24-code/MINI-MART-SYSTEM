<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function read(ContactMessage $message): JsonResponse
    {
        $message->update(['read' => true]);

        return response()->json(['message' => $message]);
    }

    public function readAll(): JsonResponse
    {
        ContactMessage::where('read', false)->update(['read' => true]);

        return response()->json(['ok' => true]);
    }

    public function reply(Request $request, ContactMessage $message): JsonResponse
    {
        $data = $request->validate(['reply' => 'required|string|max:2000']);

        $message->update([
            'reply' => $data['reply'],
            'replied_at' => now(),
            'read' => true,
        ]);

        return response()->json(['message' => $message]);
    }

    public function destroy(ContactMessage $message): JsonResponse
    {
        $message->delete();

        return response()->json(['ok' => true]);
    }
}

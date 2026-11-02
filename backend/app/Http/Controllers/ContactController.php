<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('contact.index', compact('categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($data);

        $e = fn ($v) => TelegramService::e($v);
        TelegramService::send(
            "✉️ <b>New contact message</b>\n"
            . "👤 {$e($data['name'])}\n"
            . "📧 {$e($data['email'])}\n\n"
            . $e($data['message'])
        );

        return response()->json(['message' => "Sent to the store — we'll reply within a day."]);
    }
}

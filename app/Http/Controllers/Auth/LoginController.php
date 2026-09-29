<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CartController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('auth.login', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 1. Capture the cart from the guest session before logging in
        $guestCart = session(CartController::SESSION_KEY, []);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Those credentials do not match our records.'])
                ->onlyInput('email');
        }

        // 2. Regenerate session ID (security)
        $request->session()->regenerate();

        // 3. Re-store the guest cart into the newly authenticated session
        if (! empty($guestCart)) {
            session([CartController::SESSION_KEY => $guestCart]);
        }

        return Auth::user()->isAdmin()
            ? redirect()->intended(route('admin.index'))
            : redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
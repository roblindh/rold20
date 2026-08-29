<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Dynamic\Player;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Authenticate user
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'Name' => 'required|string',
            'Password' => 'required|string',
        ]);

        $player = Player::where('Name', $credentials['Name'])->first();

        if (!$player) {
            return back()->withErrors([
                'Name' => 'No account found with this username.',
            ])->onlyInput('Name');
        }

        // Verify password (supports modern bcrypt and legacy fallback)
        $passwordMatches = false;
        if (Hash::check($credentials['Password'], (string)$player->Password)) {
            $passwordMatches = true;
        } elseif ($player->Password === md5($credentials['Password']) || $player->Password === $credentials['Password']) {
            $passwordMatches = true;
            // Upgrade legacy password hash to modern bcrypt
            $player->Password = Hash::make($credentials['Password']);
            $player->save();
        }

        if (!$passwordMatches) {
            return back()->withErrors([
                'Password' => 'The provided password does not match our records.',
            ])->onlyInput('Name');
        }

        Auth::login($player, $request->boolean('remember'));
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $role = $player->isGM() ? 'Game Master' : 'Player';
        return redirect()->intended(route('home'))->with('status', "Welcome back, {$player->Name}! Logged in as {$role}.");
    }

    /**
     * Show registration form
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    /**
     * Register a new Player or Game Master account
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:50|unique:players,Name',
            'Password' => 'required|string|min:4|confirmed',
            'Type' => 'required|integer|in:1,2',
        ], [
            'Name.unique' => 'This username is already taken. Please choose another.',
            'Password.confirmed' => 'The password confirmation does not match.',
            'Password.min' => 'Password must be at least 4 characters.',
        ]);

        $player = Player::create([
            'Name' => $validated['Name'],
            'Password' => Hash::make($validated['Password']),
            'Type' => (int)$validated['Type'],
        ]);

        Auth::login($player);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $role = $player->isGM() ? 'Game Master' : 'Player';
        return redirect()->route('home')->with('status', "Account created successfully! Welcome {$player->Name} ({$role}).");
    }

    /**
     * Log the user out of the application
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('home')->with('status', 'You have been logged out successfully.');
    }
}

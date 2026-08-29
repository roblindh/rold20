@extends('layouts.app', ['title' => 'Player & GM Login'])

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-lg border-2 border-slate-300 shadow-lg overflow-hidden">
        <!-- Header banner -->
        <div class="px-6 py-4 border-b-2 border-slate-300" style="background: url('{{ asset('styles/goldparchment.jpg') }}') repeat #f0f0d9;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('styles/reddragon_sml.gif') }}" alt="Dragon" class="h-8 w-auto object-contain" />
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">Account Login</h1>
                    <p class="text-xs text-slate-700 font-semibold">Access your characters and campaigns</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">
            <!-- Optional account notice -->
            <div class="bg-amber-50 border border-amber-300 text-amber-900 px-3.5 py-2.5 rounded text-xs flex items-start gap-2">
                <span class="text-base">💡</span>
                <div>
                    <span class="font-bold">Account is optional:</span> You can freely browse rules and create characters as a guest. Logging in allows you to save and manage your characters across sessions.
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded text-xs space-y-1">
                    <div class="font-bold">Please correct the following:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username -->
                <div>
                    <label for="Name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Player / GM Name</label>
                    <input type="text" id="Name" name="Name" value="{{ old('Name') }}" required autofocus
                           class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm text-black"
                           placeholder="Enter your username">
                </div>

                <!-- Password -->
                <div>
                    <label for="Password" class="block text-xs font-bold uppercase text-slate-700 mb-1">Password</label>
                    <input type="password" id="Password" name="Password" required
                           class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm text-black"
                           placeholder="Enter your password">
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-slate-700">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-2.5 px-4 rounded shadow transition text-sm flex items-center justify-center gap-2" style="background-color: #8b1a1a; color: #ffffff;">
                    <span>🗝️</span>
                    <span>Log In</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                Don't have an account yet? 
                <a href="{{ route('register') }}" class="font-bold text-indigo-700 hover:text-indigo-900 underline">Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection

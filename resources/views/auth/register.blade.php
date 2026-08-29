@extends('layouts.app', ['title' => 'Register Player or Game Master Account'])

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-lg border-2 border-slate-300 shadow-lg overflow-hidden">
        <!-- Header banner -->
        <div class="px-6 py-4 border-b-2 border-slate-300" style="background: url('{{ asset('styles/goldparchment.jpg') }}') repeat #f0f0d9;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('styles/reddragon_sml.gif') }}" alt="Dragon" class="h-8 w-auto object-contain" />
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">Create Account</h1>
                    <p class="text-xs text-slate-700 font-semibold">Join as a Player or Game Master</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">
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

            <form action="{{ route('register', [], false) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Account Type / Role Selection -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Account Role</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border-2 rounded-lg p-3 flex flex-col items-center justify-center cursor-pointer transition hover:bg-amber-50"
                               :class="role === 1 ? 'border-amber-600 bg-amber-50/50 text-amber-900 font-bold' : 'border-slate-300 text-slate-700'"
                               x-data="{ role: {{ old('Type', 1) }} }">
                            <input type="radio" name="Type" value="1" {{ old('Type', 1) == 1 ? 'checked' : '' }} class="sr-only" @change="role = 1">
                            <span class="text-2xl mb-1">🧙‍♂️</span>
                            <span class="text-sm font-bold">Player</span>
                            <span class="text-[11px] text-slate-500 text-center mt-0.5">Create & manage PCs</span>
                        </label>
                        <label class="border-2 rounded-lg p-3 flex flex-col items-center justify-center cursor-pointer transition hover:bg-amber-50"
                               :class="role === 2 ? 'border-amber-600 bg-amber-50/50 text-amber-900 font-bold' : 'border-slate-300 text-slate-700'"
                               x-data="{ role: {{ old('Type', 1) }} }">
                            <input type="radio" name="Type" value="2" {{ old('Type') == 2 ? 'checked' : '' }} class="sr-only" @change="role = 2">
                            <span class="text-2xl mb-1">👑</span>
                            <span class="text-sm font-bold">Game Master</span>
                            <span class="text-[11px] text-slate-500 text-center mt-0.5">Run campaigns & NPCs</span>
                        </label>
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label for="Name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Username / Name</label>
                    <input type="text" id="Name" name="Name" value="{{ old('Name') }}" required autofocus
                           class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm text-black"
                           placeholder="Choose a player or GM name">
                </div>

                <!-- Password -->
                <div>
                    <label for="Password" class="block text-xs font-bold uppercase text-slate-700 mb-1">Password</label>
                    <input type="password" id="Password" name="Password" required
                           class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm text-black"
                           placeholder="At least 4 characters">
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="Password_confirmation" class="block text-xs font-bold uppercase text-slate-700 mb-1">Confirm Password</label>
                    <input type="password" id="Password_confirmation" name="Password_confirmation" required
                           class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm text-black"
                           placeholder="Confirm your password">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-2.5 px-4 rounded shadow transition text-sm flex items-center justify-center gap-2" style="background-color: #8b1a1a; color: #ffffff;">
                    <span>✨</span>
                    <span>Create Account</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                Already have an account? 
                <a href="{{ route('login', [], false) }}" class="font-bold text-indigo-700 hover:text-indigo-900 underline">Log in here</a>
            </div>
        </div>
    </div>
</div>
@endsection

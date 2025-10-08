<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold text-center mb-4">Accedi</h2>

        @if (session('status'))
            <div class="mb-4 text-green-600">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border-gray-300 rounded-md shadow-sm">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full border-gray-300 rounded-md shadow-sm">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-1"> Ricordami
                </label>
                <a href="{{ route('password.request') }}" class="text-blue-600 text-sm">Password dimenticata?</a>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md">
                Accedi
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Non hai un account?
            <a href="{{ route('register') }}" class="text-blue-600">Registrati</a>
        </p>
    </div>
</x-guest-layout>

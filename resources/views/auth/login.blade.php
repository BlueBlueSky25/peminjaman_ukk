<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Login</h2>
                
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                <!-- Info Credentials -->
                <!-- <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded text-sm">
                    <p><strong>Demo Login:</strong></p>
                    <p>Username: admin</p>
                    <p>Password: admin123</p>
                </div> -->
                
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    
                    <!-- Username Field -->
                    <div class="mb-6">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ old('username') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Masukkan username"
                            required
                        >
                    </div>

                    <!-- Password Field -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Masukkan password"
                            required
                        >
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        Login
                    </button> 
                </form>
            </div>
        </div>
    </div>
</body>
</html>
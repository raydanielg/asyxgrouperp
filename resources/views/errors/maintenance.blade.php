<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-emerald-900 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">System Maintenance</h1>
        <p class="text-sm text-gray-500 mb-6">The system is temporarily under maintenance. Please check back later. Only super administrators can access the system right now.</p>
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Back to Login</a>
    </div>
</body>
</html>

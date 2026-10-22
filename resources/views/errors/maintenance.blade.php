<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-sm w-full text-center">
        <p class="text-5xl font-bold text-gray-300">503</p>
        <div class="w-8 h-0.5 bg-gray-300 mx-auto my-4"></div>
        <h1 class="text-lg font-bold text-gray-900">System Maintenance</h1>
        <p class="text-sm text-gray-500 mt-2 mb-6">The system is temporarily under maintenance. Please check back shortly.</p>
        <a href="{{ route('login') }}" class="inline-block px-4 py-2 text-sm font-medium text-white bg-slate-800 rounded hover:bg-slate-700">Back to Login</a>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fake Payment Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Fake Payment Gateway</h1>
            <p class="text-gray-500 mt-2">Reference: {{ $reference }}</p>
        </div>

        <div class="space-y-4">
            <p class="text-sm text-gray-600 text-center">
                This is a test payment gateway. Choose an option below to simulate payment result.
            </p>

            <a href="{{ $success_url }}" class="block w-full bg-green-600 text-white text-center py-3 rounded-md hover:bg-green-700 transition font-medium">
                Simulate Successful Payment
            </a>

            <a href="{{ $fail_url }}" class="block w-full bg-red-600 text-white text-center py-3 rounded-md hover:bg-red-700 transition font-medium">
                Simulate Failed Payment
            </a>
        </div>

        <div class="mt-6 pt-6 border-t">
            <p class="text-xs text-gray-400 text-center">
                This is for testing purposes only. In production, you would be redirected to a real payment gateway like Paynow.
            </p>
        </div>
    </div>
</body>
</html>

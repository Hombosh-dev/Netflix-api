<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Успішна оплата</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
            <div class="text-center mb-6">
                <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Оплата успішна!</h1>
                <p class="text-gray-600 mt-2">Дякуємо за оплату підписки.</p>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Деталі платежу:</h2>
                    <div class="mt-2 text-gray-600">
                        <p><span class="font-medium">Сума:</span> {{ $payment->amount }} {{ $payment->currency }}</p>
                        <p><span class="font-medium">Дата:</span> {{ $payment->created_at->format('d.m.Y H:i') }}</p>
                        <p><span class="font-medium">Спосіб оплати:</span> {{ $payment->payment_method }}</p>
                        <p><span class="font-medium">ID транзакції:</span> {{ $payment->transaction_id }}</p>
                    </div>
                </div>

                @if($subscription)
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Деталі підписки:</h2>
                    <div class="mt-2 text-gray-600">
                        <p><span class="font-medium">Тариф:</span> {{ $subscription->tariff->name }}</p>
                        <p><span class="font-medium">Початок:</span> {{ $subscription->start_date->format('d.m.Y') }}</p>
                        <p><span class="font-medium">Закінчення:</span> {{ $subscription->end_date->format('d.m.Y') }}</p>
                        <p><span class="font-medium">Статус:</span> 
                            <span class="text-green-500 font-medium">{{ $subscription->is_active ? 'Активна' : 'Неактивна' }}</span>
                        </p>
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-6 text-center">
                <a href="{{ config('app.frontend_url') ?? '/' }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-150 ease-in-out">
                    Повернутися на головну
                </a>
            </div>
        </div>
    </div>
</body>
</html>

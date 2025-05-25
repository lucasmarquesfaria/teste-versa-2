<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Bem-vindo ao Sistema de Controle de DOs e DNVs</h1>
            <p class="text-lg text-gray-600 mb-8">Acesse o menu para iniciar.</p>
            <a href="{{ route('login') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Entrar</a>
        </div>
    </div>
</body>
</html>

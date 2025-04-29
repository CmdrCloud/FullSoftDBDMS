<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FullSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    {{-- Login landing page --}}
    <nav>
        <ul class="flex space-x-4">
            <li><a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login</a></li>
            <li><a href="{{ route('register') }}" class="text-blue-500 hover:underline">Register</a></li>
        </ul>
    </nav>

    <main>

    </main>

    <section>

    </section>

    <footer>

    </footer>



</body>
</html>

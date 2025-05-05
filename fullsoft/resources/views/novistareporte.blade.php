<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>No se encontraron ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

    <nav class="absolute top-0 left-0 w-full bg-white dark:bg-[#0a0a0a] p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('home')}}" class="inline-block py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-lg leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50">FullSoft</a>
            </div>
            <div>
                <a href="{{ route('dashboard')}}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Regresar</a>
                <a href="{{ route('logout') }}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Cerrar Sesion</a>
            </div>
        </div>
    </nav>
    
    <div class="container mx-auto p-8">
        <h2 class="text-2xl font-semibold mb-6">No se encontraron ventas</h2>
        <p>No se encontraron ventas en el rango de fechas seleccionado.</p>
    </div>
</body>
</html>

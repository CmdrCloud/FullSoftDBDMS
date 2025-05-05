<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Generar Reporte de Ventas</title>

        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>

    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

        {{-- Navbar --}}
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

        {{-- Formulario para generar el reporte --}}
        <div class="container mx-auto mt-16">
            <div class="flex flex-col lg:flex-row justify-center items-center space-x-0 lg:space-x-8 space-y-4 lg:space-y-0">
                <div class="bg-white dark:bg-[#1b1b18] shadow-lg rounded-lg p-6 w-full max-w-md">
                    <h2 class="text-xl font-semibold mb-4">Emitir Reporte de Ventas de Autos</h2>
                    <form action="{{ route('reportes.ventas') }}" method="GET" class="space-y-4">
                        @csrf

                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-[#1b1b18] focus:ring-[#1b1b18]" required>

                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mt-4">Fecha de Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-[#1b1b18] focus:ring-[#1b1b18]" required>

                        <label for="tipo_reporte" class="block text-sm font-medium text-gray-700 mt-4">Tipo de Reporte</label>
                        <select id="tipo_reporte" name="tipo_reporte" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-[#1b1b18] focus:ring-[#1b1b18]" required>
                            <option value="detallado">Detallado</option>
                            <option value="resumido">Resumido</option>
                        </select>

                        

                        <button type="submit" class="w-full bg-red-700 text-white py-2 px-4 rounded hover:bg-red-700 transition duration-300 mt-4">
                            Generar Reporte
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

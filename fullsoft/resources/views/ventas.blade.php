<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>FullSoft</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css'])
</head>
<html class="dark">
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-white flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    {{-- Position the navbar for the logo at the top of the screen --}}
    <nav class="absolute top-0 left-0 w-full bg-white dark:bg-[#0a0a0a] p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('home')}} " class="inline-block py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-lg leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50">FullSoft</a>
            </div>
            <div>
                <a href="{{ route('dashboard')}}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Regresar</a>
                <a href="{{ route('logout') }}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Cerrar Sesion</a>
            </div>
        </div>
    </nav>

{{-- Main content should be a grid with two columns, the left column will display cars and the right column is a search field to look for cars by different filters --}}
<div class="container mx-auto mt-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Left column for car display --}}
        <div class="bg-white dark:bg-[#252525] p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Lista de vehiculos</h2>
            <ul class="space-y-4">
                {{-- @foreach ($cars as $car) --}}
                    <li class="flex items-center bg-gray-100 dark:bg-[#1b1b18] p-4 rounded-lg shadow-sm">
                        <img src="{{-- place asset here later --}}" alt="" class="w-39 h-29 rounded mr-4">
                        <div>
                            <h3 class="text-lg font-semibold">Modelo</h3>
                            <p class="text-gray-600 dark:text-gray-400">Marca </p>
                            <p class="text-gray-600 dark:text-gray-400">Precio </p>
                            <p class="text-gray-600 dark:text-gray-400">Año </p>
                            <p class="text-gray-600 dark:text-gray-400">Cilindrada </p>
                        </div>
                        <button class="ml-auto bg-[#F61500]  text-white px-4 py-2 rounded hover:bg-red-700" onclick="">Seleccionar</button>
                    </li>
                {{-- @endforeach --}}
            </ul>
        </div>

        {{-- Right column for searching for cars by model name, brand, price, year, or cylinders --}}
        <div class="bg-white dark:bg-[#252525] p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Buscar vehiculo</h2>
            <form action="" class="flex flex-col justify-items-start">
                <div><input type="radio" name="Modelo" id="" class=""> <label for="searchByModel" class="px-2">Buscar por modelo</label></div>
                <div><input type="radio" name="Marca" id="" class=""> <label for="searchByBrand" class="px-2">Buscar por marca</label></div>
                <div><input type="radio" name="Ano" id="" class=""> <label for="searchByYear" class="px-2">Buscar por ano</label></div>
                <div><input type="radio" name="Cilindrada" id="" class=""> <label for="searchByCylinders" class="px-2">Buscar por cilindrada</label></div>
            </form>
            <div class="container py-3 mx-auto flex justify-start items-center">
                <form action="" method="GET" class="flex items-center">
                    <input type="text" name="query" placeholder="Buscar..." class="border border-zinc-300  dark:border-gray-600 rounded-l px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <button type="submit" class="bg-[#F61500] text-white rounded-r px-4 py-2 hover:bg-red-700">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</div>



{{-- Footer has copyright --}}
<footer class="flex items-center justify-center w-full h-10 bg-white dark:bg-black dark:text-white text-black mt-4">

</footer>
</body>
</html>

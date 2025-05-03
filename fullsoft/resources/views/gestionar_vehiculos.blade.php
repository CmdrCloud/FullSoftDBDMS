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
<html>
<body class="dark">
    {{-- Navbar --}}
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

    {{-- Table for managing vehicles --}}
    <div class="container mx-auto mt-20">
        <h1 class="text-2xl font-bold mb-4 py-4">Gestionar Vehículos</h1>
        <table class="table-auto w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">Marca</th>
                    <th class="border border-gray-300 px-4 py-2">Modelo</th>
                    <th class="border border-gray-300 px-4 py-2">Ano</th>
                    <th class="border border-gray-300 px-4 py-2">Cilindrada</th>
                    <th class="border border-gray-300 px-4 py-2">Aire Acondicionado</th>
                    <th class="border border-gray-300 px-4 py-2">Pintura Metalizada</th>
                    <th class="border border-gray-300 px-4 py-2">Precio</th>
                    <th class="border border-gray-300 px-4 py-2">Es de parte de pago?</th>
                    <th class="border border-gray-300 px-4 py-2">Placa</th>
                    <th class="border border-gray-300 px-4 py-2">Subir imagen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vehicles as $vehicle)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->brand }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->model }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->year }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->cylinders }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->airConditioning }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->metallicPaint }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->price }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->partOfPayment }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $vehicle->numberPlate }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        @if ($vehicle->image)
                            <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->name }}" class="w-16 h-16 object-cover">
                        @else
                            Sin Imagen
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Editar</a>
                        <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('¿Estás seguro de eliminar este vehículo?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    <a href="{{ route('vehicles.create') }}" class="bg-[#F61500] text-white px-4 py-2 rounded hover:bg-[#ffa39b]">Agregar Nuevo Vehículo</a>
    </div>


    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>

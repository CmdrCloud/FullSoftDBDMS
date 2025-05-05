<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
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
        <h2 class="text-2xl font-semibold mb-6">Reporte de Ventas de Vehículos</h2>

        <h3 class="mb-4 text-lg">Fecha de Inicio: {{ $fecha_inicio->format('d/m/Y') }}</h3>
        <h3 class="mb-4 text-lg">Fecha de Fin: {{ $fecha_fin->format('d/m/Y') }}</h3>

        @if($ventas->isEmpty())
            <p>No se encontraron ventas en el rango de fechas especificado.</p>
        @else
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">ID Venta</th>
                        <th class="border px-4 py-2">Placa Vehículo</th>
                        <th class="border px-4 py-2">Cliente</th>
                        <th class="border px-4 py-2">Usuario</th>
                        <th class="border px-4 py-2">Fecha</th>
                        <th class="border px-4 py-2">Total</th>
                        <th class="border px-4 py-2">Pago Inicial</th>
                        <th class="border px-4 py-2">Pago Parcial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                        <tr>
                            <td class="border px-4 py-2">{{ $venta->id }}</td>
                            <td class="border px-4 py-2">{{ $venta->numberPlate }}</td>
                            <td class="border px-4 py-2">{{ $venta->client_name }}</td>
                            <td class="border px-4 py-2">{{ $venta->user_name }}</td>
                            <td class="border px-4 py-2">{{ $venta->date }}</td>
                            <td class="border px-4 py-2">{{ $venta->totalAmount }}</td>
                            <td class="border px-4 py-2">{{ $venta->totalUpfront }}</td>
                            <td class="border px-4 py-2">{{ $venta->totalPartPayment }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
    </div>
    <!-- Formulario para exportar desde vistareporte.blade.php -->
<div class="fixed bottom-4 right-4">
    <form action="{{ route('reportes.exportar') }}" method="GET">
        <input type="hidden" name="tipo_reporte" value="detallado">
        <button type="submit" class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-700 transition duration-300">
            Exportar Reporte
        </button>
    </form>
</div>

   

</body>
</html>

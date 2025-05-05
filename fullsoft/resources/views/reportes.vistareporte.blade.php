<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

    <h2>Reporte de Ventas de Vehículos</h2>
    <h3>Fecha de Inicio: {{ $fecha_inicio->format('d/m/Y') }}</h3>
    <h3>Fecha de Fin: {{ $fecha_fin->format('d/m/Y') }}</h3>

    <table>
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Placa Vehículo</th>
                <th>Cliente</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Pago Inicial</th>
                <th>Pago Parcial</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->numberPlate }}</td>
                    <td>{{ $venta->client_name }}</td>
                    <td>{{ $venta->user_name }}</td>
                    <td>{{ $venta->date }}</td>
                    <td>{{ $venta->totalAmount }}</td>
                    <td>{{ $venta->totalUpfront }}</td>
                    <td>{{ $venta->totalPartPayment }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>

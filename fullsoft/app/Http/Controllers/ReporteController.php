<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;  // Modelo Sale para acceder a la base de datos
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ReporteController extends Controller
{
    // Método para generar el reporte de ventas
    public function generarReporteVentas(Request $request)
    {
        // Validación de las fechas
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio', // Verifica que la fecha fin sea después de la fecha inicio
        ]);

        // Obtener las fechas del formulario
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        // Convertir las fechas a los formatos correctos
        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();  // 00:00 del día
        $fecha_fin = Carbon::parse($fecha_fin)->addDay()->startOfDay();  // El primer minuto del día siguiente

        // Obtener las ventas que estén dentro del rango de fechas
        $ventas = Sale::join('client', 'sale.IDClient', '=', 'client.id')
            ->join('vehicle', 'sale.IDVehicle', '=', 'vehicle.id')
            ->join('users', 'sale.IDUser', '=', 'users.id') // Aquí se agrega el join con la tabla users
            ->select('sale.id', 'client.name as client_name', 'vehicle.numberPlate', 'users.name as user_name', 'sale.date', 'sale.totalAmount', 'sale.totalUpfront', 'sale.totalPartPayment')
            ->whereBetween('sale.date', [$fecha_inicio, $fecha_fin])
            ->get();

        // Si no hay ventas, retornar un mensaje adecuado
        if ($ventas->isEmpty()) {
            return view('novistareporte');  // Vista para cuando no haya ventas
        }

        // Obtener el tipo de reporte (detallado o resumido)
        $tipo_reporte = $request->input('tipo_reporte');

        // Redirigir a la vista correspondiente
        if ($tipo_reporte == 'detallado') {
            return view('vistareporte', compact('ventas', 'fecha_inicio', 'fecha_fin'));
        } else {
            return view('reporteresumido', compact('ventas', 'fecha_inicio', 'fecha_fin'));
        }
    }
/*
    // Método para exportar el reporte en formato PDF
    public function exportarReporte(Request $request)
    {
          // Obtener el tipo de reporte desde la petición (si es detallado o resumido)
    $tipo_reporte = $request->input('tipo_reporte', 'detallado'); // Por defecto, 'detallado'

        // Crear una nueva instancia de Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer los encabezados del reporte
        $sheet->setCellValue('A1', 'ID Venta');
        $sheet->setCellValue('B1', 'Placa Vehículo');
        $sheet->setCellValue('C1', 'Cliente');
        $sheet->setCellValue('D1', 'Usuario');
        $sheet->setCellValue('E1', 'Fecha');
        $sheet->setCellValue('F1', 'Total');
        $sheet->setCellValue('G1', 'Pago Inicial');
        $sheet->setCellValue('H1', 'Pago Parcial');

        // Obtener los datos de las ventas desde la base de datos
        $ventas = Sale::join('client', 'sale.IDClient', '=', 'client.id')
            ->join('vehicle', 'sale.IDVehicle', '=', 'vehicle.id')
            ->join('users', 'sale.IDUser', '=', 'users.id')
            ->select('sale.id', 'client.name as client_name', 'vehicle.numberPlate', 'users.name as user_name', 'sale.date', 'sale.totalAmount', 'sale.totalUpfront', 'sale.totalPartPayment')
            ->get();

        // Rellenar las filas del reporte con los datos de las ventas
        $row = 2; // Comienza desde la segunda fila
        foreach ($ventas as $venta) {
            $sheet->setCellValue('A' . $row, $venta->id);
            $sheet->setCellValue('B' . $row, $venta->numberPlate);
            $sheet->setCellValue('C' . $row, $venta->client_name);
            $sheet->setCellValue('D' . $row, $venta->user_name);
            $sheet->setCellValue('E' . $row, $venta->date);
            $sheet->setCellValue('F' . $row, $venta->totalAmount);
            $sheet->setCellValue('G' . $row, $venta->totalUpfront);
            $sheet->setCellValue('H' . $row, $venta->totalPartPayment);
            $row++;
        }

        // Crear el escritor para generar el archivo Excel
        $writer = new Xlsx($spreadsheet);

        // Establecer el nombre del archivo y generar la descarga
        $filename = 'reporte_ventas.xlsx';

        // Descargar el archivo generado
        return response()->stream(
            function() use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="reporte_ventas.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

}

*/

public function exportarReporte(Request $request)
{
    // Obtener el tipo de reporte desde la petición (si es detallado o resumido)
    $tipo_reporte = $request->input('tipo_reporte'); // Por defecto, 'detallado'

    // Obtener las ventas (o los datos del reporte)
    $ventasQuery = Sale::join('client', 'sale.IDClient', '=', 'client.id')
        ->join('vehicle', 'sale.IDVehicle', '=', 'vehicle.id')
        ->join('users', 'sale.IDUser', '=', 'users.id');

    // Si el reporte es detallado, seleccionamos todas las columnas
    if ($tipo_reporte == 'detallado') {
        $ventas = $ventasQuery->select('sale.id', 'client.name as client_name', 'vehicle.numberPlate', 'users.name as user_name', 'sale.date', 'sale.totalAmount', 'sale.totalUpfront', 'sale.totalPartPayment')->get();
    } else {
        // Si el reporte es resumido, solo seleccionamos las columnas específicas
        $ventas = $ventasQuery->select('sale.id', 'sale.IDUser', 'sale.date', 'sale.totalAmount')->get();
    }

    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Establecer encabezados
    if ($tipo_reporte == 'detallado') {
        $sheet->setCellValue('A1', 'ID Venta');
        $sheet->setCellValue('B1', 'Cliente');
        $sheet->setCellValue('C1', 'Placa Vehículo');
        $sheet->setCellValue('D1', 'Usuario');
        $sheet->setCellValue('E1', 'Fecha');
        $sheet->setCellValue('F1', 'Total');
        $sheet->setCellValue('G1', 'Pago Inicial');
        $sheet->setCellValue('H1', 'Pago Parcial');
    } else {
        $sheet->setCellValue('A1', 'ID Venta');
        $sheet->setCellValue('B1', 'ID Usuario');
        $sheet->setCellValue('C1', 'Fecha');
        $sheet->setCellValue('D1', 'Total');
    }

    // Llenar los datos de las ventas
    $row = 2; // Comenzamos en la fila 2 porque la fila 1 es para los encabezados
    foreach ($ventas as $venta) {
        $sheet->setCellValue('A' . $row, $venta->id);
        if ($tipo_reporte == 'detallado') {
            $sheet->setCellValue('B' . $row, $venta->client_name);
            $sheet->setCellValue('C' . $row, $venta->numberPlate);
            $sheet->setCellValue('D' . $row, $venta->user_name);
            $sheet->setCellValue('E' . $row, $venta->date);
            $sheet->setCellValue('F' . $row, $venta->totalAmount);
            $sheet->setCellValue('G' . $row, $venta->totalUpfront);
            $sheet->setCellValue('H' . $row, $venta->totalPartPayment);
        } else {
            $sheet->setCellValue('B' . $row, $venta->IDUser);
            $sheet->setCellValue('C' . $row, $venta->date);
            $sheet->setCellValue('D' . $row, $venta->totalAmount);
        }
        $row++;
    }

    // Crear un archivo Excel y devolverlo como descarga
    $writer = new Xlsx($spreadsheet);
    $filename = 'reporte_ventas_' . now()->format('Ymd_His') . '.xlsx';

    // Configurar las cabeceras para la descarga
    return response()->stream(
        function () use ($writer) {
            $writer->save('php://output');
        },
        200,
        [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]
    );
}
}
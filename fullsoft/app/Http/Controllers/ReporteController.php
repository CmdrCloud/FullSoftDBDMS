<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;  // Modelo Sale para acceder a la base de datos
use Carbon\Carbon;

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

        // Pasar los datos de las ventas a la vista
        //return view('vistareporte', compact('ventas', 'fecha_inicio', 'fecha_fin'));

          // Obtener el tipo de reporte (detallado o resumido)
    $tipo_reporte = $request->input('tipo_reporte');

    // Redirigir a la vista correspondiente
    if ($tipo_reporte == 'detallado') {
        return view('vistareporte', compact('ventas', 'fecha_inicio', 'fecha_fin'));
    } else {
        return view('reporteresumido', compact('ventas', 'fecha_inicio', 'fecha_fin'));
    }
    }
}

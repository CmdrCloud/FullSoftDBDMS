<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Sale;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentasController extends Controller
{
    /**
     * Display the sales page with vehicles
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Apply search filters if provided
        if ($request->filled('search_type') && $request->filled('query')) {
            $searchType = $request->input('search_type');
            $searchQuery = $request->input('query');

            switch ($searchType) {
                case 'model':
                    $query->where('model', 'like', "%{$searchQuery}%");
                    break;
                case 'brand':
                    $query->where('brand', 'like', "%{$searchQuery}%");
                    break;
                case 'year':
                    $query->where('year', $searchQuery);
                    break;
                case 'cylinders':
                    $query->where('cylinders', 'like', "%{$searchQuery}%");
                    break;
            }
        }

        $vehicles = $query->get();

        return view('ventas', compact('vehicles'));
    }

    /**
     * Get vehicle details for the modal
     */
    public function getVehicleDetails($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    /**
     * Process a vehicle sale
     */
    public function processSale(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicle,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'air_conditioning' => 'boolean',
            'metallic_paint' => 'boolean',
            'part_of_payment' => 'boolean',
            'part_payment_details' => 'nullable|required_if:part_of_payment,1',
            'part_payment_value' => 'nullable|required_if:part_of_payment,1|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'upfront_payment' => 'required|numeric|min:0',
        ]);

        // Create or find client
        $client = Client::firstOrCreate(
            ['email' => $validated['client_email'] ?? ''],
            [
                'name' => $validated['client_name'],
                'phone' => $validated['client_phone'] ?? null,
            ]
        );

        // Calculate commission (3% of final price)
        $commission = $validated['final_price'] * 0.03;

        // Create sale record
        $sale = Sale::create([
            'IDVehicle' => $validated['vehicle_id'],
            'IDClient' => $client->id,
            'IDUser' => Auth::id(),
            'date' => now(),
            'totalAmount' => $validated['final_price'],
            'totalUpfront' => $validated['upfront_payment'],
            'totalPartPayment' => $validated['part_of_payment'] ? $validated['part_payment_value'] : 0
        ]);

        // Update user commission if needed
        // You can add this functionality based on your requirements

        return response()->json([
            'success' => true,
            'commission' => $commission,
            'message' => 'Venta procesada exitosamente'
        ]);
    }
}

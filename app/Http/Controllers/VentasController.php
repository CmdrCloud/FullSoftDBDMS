<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VentasController extends Controller
{
    /**
     * Display the sales page with available vehicles.
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::query();

        // Optional: apply simple search filters
        if ($request->filled('search_type') && $request->filled('query')) {
            $type  = $request->input('search_type');
            $value = $request->input('query');
            if (in_array($type, ['model', 'brand', 'year', 'cylinders'])) {
                $vehicles->where($type, 'like', "%{$value}%");
            }
        }

        $vehicles = $vehicles->get();
        return view('ventas', compact('vehicles'));
    }

    /**
     * Return recent sales as JSON.
     */
    public function getRecentSales()
    {
        $sales = Sale::with(['vehicle', 'client', 'user'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($sale) {
                return [
                    'id'            => $sale->id,
                    'date'          => $sale->date,
                    'vehicleBrand'  => $sale->vehicle->brand,
                    'vehicleModel'  => $sale->vehicle->model,
                    'vehicleYear'   => $sale->vehicle->year,
                    'vehiclePlate'  => $sale->vehicle->numberPlate ?? 'N/A',
                    'clientName'    => $sale->client->name,
                    'finalPrice'    => $sale->totalAmount,
                    'sellerName'    => $sale->user->name,
                ];
            });

        return response()->json($sales);
    }

    /**
     * Get details for a single vehicle.
     */
    public function getVehicleDetails($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log request data for debugging
            Log::info('Sale request data:', $request->all());

            // Validate input with reduced requirements
            $validated = $request->validate([
                'vehicle_id'       => 'required|exists:vehicle,id',
                'client_name'      => 'required|string|max:255',
                'client_email'     => 'nullable|email|max:255',
                'client_phone'     => 'nullable|string|max:20',
                'client_address'   => 'nullable|string|max:255',
                'client_dni'       => 'nullable|string|max:20',
                'client_rfc'       => 'nullable|string|max:20',
                'air_conditioning' => 'boolean',
                'metallic_paint'   => 'boolean',
                'has_part_payment' => 'boolean',
                'part_payment_data'=> 'nullable|array',
                'base_price'       => 'required|numeric|min:0',
                'options_price'    => 'required|numeric|min:0',
                'part_payment_value'=> 'required|numeric|min:0',
                'final_price'      => 'required|numeric|min:0',
                'upfront_payment'  => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Create or get the client - Fix: Handle empty email properly
            $clientData = [
                'name'      => $validated['client_name'],
                'lastName'  => '', // if no last name, leave empty or adjust form
                'telephone' => $validated['client_phone'] ?? null,
                'address'   => $validated['client_address'] ?? null,
                'DNI'       => $validated['client_dni'] ?? null,
                'RFC'       => $validated['client_rfc'] ?? null,
            ];

            // Fix: Handle empty email case
            if (!empty($validated['client_email'])) {
                $client = Client::firstOrCreate(
                    ['email' => $validated['client_email']],
                    $clientData
                );
            } else {
                // For clients without email, create new record
                $client = Client::create($clientData);
            }

            // Calculate commission
            $commission = $validated['final_price'] * 0.03;

            // Handle user/seller - Fix: Default user handling
            $userId = Auth::check() ? Auth::id() : 1; // Default to user ID 1 if not authenticated

            // If seller_id was provided in request and not empty, use that instead
            if ($request->filled('seller_id') && !empty($request->input('seller_id'))) {
                $userId = $request->input('seller_id');
            }

            // Update user's commission if user exists
            $user = User::find($userId);
            if ($user) {
                $user->commission = ($user->commission ?? 0) + $commission;
                $user->save();
            } else {
                // If no valid user is found, use a default user ID
                Log::warning('No valid user found for sale, using default user');
                $userId = 1; // Default admin/system user
            }

            // Create the sale
            $sale = new Sale();
            $sale->IDVehicle         = $validated['vehicle_id'];
            $sale->IDClient          = $client->id;
            $sale->IDUser            = $userId;
            $sale->date              = now();
            $sale->totalAmount       = $validated['final_price'];
            $sale->totalUpfront      = $validated['upfront_payment'];
            $sale->totalPartPayment  = $validated['part_payment_value'];
            $sale->save();

            // Handle part-payment vehicle info
            if ($validated['has_part_payment'] && isset($validated['part_payment_data'])) {
                // Store part payment data as JSON in a column if you have one
                // $sale->part_payment_info = json_encode($validated['part_payment_data']);
                // $sale->save();

                // Or just log it for now
                Log::info('Part payment data received:', $validated['part_payment_data']);
            }

            // Mark vehicle as sold
            $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
            $vehicle->save();

            DB::commit();

            return response()->json([
                'id'      => $sale->id,
                'date'    => $sale->date,
                'message' => 'Venta registrada con éxito',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale processing error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Error al guardar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
}

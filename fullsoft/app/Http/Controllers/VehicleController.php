<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Vehicle;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::all();
        return view('gestionar_vehiculos', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($validated)
    {
        return view('gestionar_vehiculos');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'cylinders' => 'required|string|max:255',
            'numberPlate' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'imgPath' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'airConditioning' => 'boolean',
            'metallicPaint' => 'boolean',
            'partOfPayment' => 'boolean',
            'price' => 'required|numeric|min:0'
        ]);
        if ($request->hasFile('imgPath')) {
            $file = $request->file('imgPath');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['imgPath'] = 'images/' . $filename;
        }

        Vehicle::create($validated);
        return redirect()->route('gestionar_vehiculos')->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('gestionar_vehiculos', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('gestionar_vehiculos', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'cylinders' => 'required|string|max:255',
            'numberPlate' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'imgPath' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'airConditioning' => 'boolean',
            'metallicPaint' => 'boolean',
            'partOfPayment' => 'boolean',
            'price' => 'required|numeric|min:0'
        ]);
        if ($request->hasFile('imgPath')) {
            $file = $request->file('imgPath');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['imgPath'] = 'images/' . $filename;
        }
        $vehicle->update($validated);
        return redirect()->route('gestionar_vehiculos')->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return redirect()->route('gestionar_vehiculos')->with('success', 'Vehicle deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the catalog page with vehicles listing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Handle search
        if ($request->has('search_type') && $request->has('query') && !empty($request->input('query'))) {
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
                    $query->where('year', 'like', "%{$searchQuery}%");
                    break;
                case 'cylinders':
                    $query->where('cylinders', 'like', "%{$searchQuery}%");
                    break;
                default:
                    break;
            }
        }

        $vehicles = $query->get();

        return view('catalogo', compact('vehicles'));
    }
}

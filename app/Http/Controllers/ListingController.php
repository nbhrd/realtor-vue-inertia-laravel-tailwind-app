<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'priceFrom', 'priceTo', 'beds', 'baths', 'areaFrom', 'areaTo'
        ]);

        // if($filters['priceFrom'] ?? false) {
        //     $query->where('price', '>=', $filters['priceFrom']);
        // }
        // if($filters['priceTo'] ?? false) {
        //     $query->where('price', '<=', $filters['priceTo']);
        // }
        // if($filters['beds'] ?? false) {
        //     $query->where('beds', (int)$filters['beds'] < 6 ? '=' : '>=', $filters['beds']);
        // }
        // if($filters['baths'] ?? false) {
        //     $query->where('baths', (int)$filters['baths'] < 6 ? '=' : '>=', $filters['baths']);
        // }
        // if($filters['areaFrom'] ?? false) {
        //     $query->where('area', '>=', $filters['areaFrom']);
        // }
        // if($filters['areaTo'] ?? false) {
        //     $query->where('area', '<=', $filters['areaTo']);
        // }

        return inertia(
            'Listing/Index',
            [
                'filters' => $filters,
                'listings' => Listing::mostRecent()->filter($filters)->paginate(10)->withQueryString(),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia(
            'Listing/Create',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->user()->listings()->create(
            $request->validate([
                'beds' => 'required|integer|min:0|max:20',
                'baths' => 'required|integer|min:0|max:20',
                'area' => 'required|integer|min:15|max:1500',
                'city' => 'required',
                'code' => 'required',
                'street' => 'required',
                'street_nr' => 'required|min:1|max:1000',
                'price' => 'required|integer|min:1|max:200000000',
            ]),
        );

        return redirect()->route('listing.index')->with('success', 'Listing was created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        return inertia(
            'Listing/Show',
            [
                'listing' => $listing,
            ]
        );
    }

    public function edit(Listing $listing)
    {
        $response = Gate::inspect('update', $listing);
        if (!$response->allowed()) {
            return redirect()->route('listing.index')
                ->with('error', 'Listing not accesible.');
        }

        return inertia(
            'Listing/Edit',
            [
                'listing' => $listing,
            ]
        );
    }

    public function update(Request $request, Listing $listing)
    {
        $listing->update(
            $request->validate([
                'beds' => 'required|integer|min:0|max:20',
                'baths' => 'required|integer|min:0|max:20',
                'area' => 'required|integer|min:15|max:1500',
                'city' => 'required',
                'code' => 'required',
                'street' => 'required',
                'street_nr' => 'required|min:1|max:1000',
                'price' => 'required|integer|min:1|max:200000000',
            ]),
        );

        return redirect()->route('listing.index')->with('success', 'Listing was updated!');
    }

    public function destroy(Listing $listing)
    {
        $response = Gate::inspect('delete', $listing);
        if (!$response->allowed()) {
            return redirect()->route('listing.index')
                ->with('error', 'Listing not accesible.');
        }

        $listing->delete();

        return redirect()->back()->with('success', 'Listing was deleted!');
    }
}

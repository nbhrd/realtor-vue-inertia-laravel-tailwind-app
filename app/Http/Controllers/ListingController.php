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
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $listing->load(['images']);

        return inertia(
            'Listing/Show',
            [
                'listing' => $listing,
            ]
        );
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

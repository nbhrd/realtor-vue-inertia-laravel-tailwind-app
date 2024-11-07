<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RealtorListingController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'deleted' => $request->boolean('deleted'),
            ...$request->only(['by', 'order'])
        ];

        return inertia(
            'Realtor/Index',
            [
                'filters' => $filters,
                'listings' => Auth::user()
                    ->listings()
                    ->filter($filters)
                    ->withCount('images')
                    ->withCount('offers')
                    ->paginate(5)
                    ->withQueryString()
            ],
        );
    }

    public function show(Listing $listing)
    {
        return inertia('Realtor/Show', ['listing'=>$listing->load('offers', 'offers.bidder')]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia(
            'Realtor/Create',
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

        return redirect()->route('realtor.listing.index')->with('success', 'Listing was created!');
    }

    public function edit(Listing $listing)
    {
        $response = Gate::inspect('update', $listing);
        if (!$response->allowed()) {
            return redirect()->route('realtor.listing.index')
                ->with('error', 'Realtor not accesible.');
        }

        return inertia(
            'Realtor/Edit',
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

        return redirect()->route('realtor.listing.index')->with('success', 'Listing was updated!');
    }

    public function destroy(Listing $listing)
    {
        $response = Gate::inspect('delete', $listing);
        if (!$response->allowed()) {
            return redirect()->route('listing.index')
                ->with('error', 'Listing not accesible.');
        }

        $listing->deleteOrFail();

        return redirect()->back()->with('success', 'Listing was deleted!');
    }

    public function restore(Listing $listing)
    {
        $listing->restore();

        return redirect()->back()->with('success', 'Listing was restored');
    }
}

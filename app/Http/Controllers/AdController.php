<?php

namespace App\Http\Controllers;

use App\Enums\AdStatusType;
use App\Models\Ad;
use App\Rules\HateSpeech;
use App\Rules\ImageHateSpeech;
use App\Utils\ImageHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $ads = $user->ads()
            ->latest()
            ->paginate(10);

        return Inertia::render('ad/Index', [
            'ads' => $ads,
            'adStatusTypes' => AdStatusType::values()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ad/Create', [
            'adStatusTypes' => AdStatusType::values(),
            'costPerDay' => config('services.ad.cost_per_day')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', new HateSpeech],
            'content' => ['required', 'string', 'max:1000', new HateSpeech],
            'target_url' => ['required', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', new ImageHateSpeech],
            'status' => ['required', 'in:' . implode(',', AdStatusType::values())],
            'starts_at' => ['required', 'date', 'after_or_equal:today'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        // Calculate ad duration in days
        $startDate = Carbon::parse($validated['starts_at']);
        $endDate = Carbon::parse($validated['ends_at']);
        $days = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end days

        // Calculate total cost
        $costPerDay = config('services.ad.cost_per_day'); // Default to 30 if not set
        $totalCost = $costPerDay * $days;

        // Check if user has enough points
        if ($request->user()->points < $totalCost) {
            return back()->withErrors([
                'points' => "You need {$totalCost} points to run this ad for {$days} days, but you only have {$request->user()->points} points."
            ]);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $webp_path = ImageHelper::convert_to_webp($request->file('image'));
            $validated['image_path'] = $webp_path;
        }

        // Set the calculated points spent
        $validated['points_spent'] = $totalCost;

        // Deduct points from user
        $request->user()->decrement('points', $totalCost);

        // Create the ad
        $request->user()->ads()->create($validated);

        return redirect()->route('ad.index')
            ->with('success', 'Ad created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ad $ad)
    {
        Gate::authorize('update', $ad);

        return Inertia::render('ad/Create', [
            'ad' => $ad,
            'adStatusTypes' => AdStatusType::values(),
            'costPerDay' => config('services.ad.cost_per_day')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        Gate::authorize('update', $ad);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', new HateSpeech],
            'content' => ['required', 'string', 'max:1000', new HateSpeech],
            'target_url' => ['required', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', new ImageHateSpeech],
            'status' => ['required', 'in:' . implode(',', AdStatusType::values())],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($ad->image_path) {
                Storage::delete($ad->image_path);
            }

            $webp_path = ImageHelper::convert_to_webp($request->file('image'));
            $validated['image_path'] = $webp_path;
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($ad->image_path) {
                Storage::delete($ad->image_path);
                $validated['image_path'] = null;
            }
        }

        $ad->update($validated);

        return redirect()->route('ad.index')
            ->with('success', 'Ad updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ad $ad)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreFacilityRequest;
use App\Http\Requests\Admin\UpdateFacilityRequest;
use App\Http\Resources\Admin\FacilityResource;
use App\Models\Facility;
use App\Services\Admin\FacilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacilityController extends Controller
{
    public function __construct(private FacilityService $facilityService)
    {
    }

    /**
     * Display a listing of facilities.
     */
    public function index(): AnonymousResourceCollection
    {
        $facilities = $this->facilityService->getAllFacilities();
        return FacilityResource::collection($facilities);
    }

    /**
     * Store newly created facility in storage.
     */
    public function store(StoreFacilityRequest $request): JsonResponse
    {
        $facility = $this->facilityService->createFacility($request->validated());
        
        return response()->json([
            'message' => 'Facility created successfully',
            'data' => new FacilityResource($facility)
        ], 201);
    }

    /**
     * Display the specified facility.
     */
    public function show(Facility $facility): FacilityResource
    {
        return new FacilityResource($facility);
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility): JsonResponse
    {
        $updatedFacility = $this->facilityService->updateFacility($facility, $request->validated());
        
        return response()->json([
            'message' => 'Facility updated successfully',
            'data' => new FacilityResource($updatedFacility)
        ]);
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Facility $facility): JsonResponse
    {
        $this->facilityService->deleteFacility($facility);
        
        return response()->json(['message' => 'Facility deleted successfully']);
    }
}
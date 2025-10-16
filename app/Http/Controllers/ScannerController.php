<?php

namespace App\Http\Controllers;

use App\Models\EquipmentItem;
use App\Models\RequestedEquipment;
use App\Models\RequisitionForm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScannerController extends Controller
{
    /**
     * Handle barcode scanning and return equipment details
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = $request->input('barcode');

        try {
            // Find equipment item by barcode
            $item = EquipmentItem::with([
                'equipment.category',
                'equipment.department',
                'equipment.status',
                'equipment.images',
                'condition'
            ])->where('barcode_number', $barcode)->first();

            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment item not found'
                ], 404);
            }

            // Get current active bookings for this item
            $currentBookings = RequestedEquipment::with([
                'requisitionForm' => function($query) {
                    $query->where('is_closed', false)
                          ->where('is_finalized', true);
                }
            ])->where('equipment_id', $item->equipment_id)
              ->get()
              ->filter(function($requestedEquipment) {
                  return $requestedEquipment->requisitionForm !== null;
              });

            // Calculate available stock
            $totalItems = EquipmentItem::where('equipment_id', $item->equipment_id)
                                     ->whereNull('deleted_at')
                                     ->count();
            
            $bookedItems = $currentBookings->sum('quantity');
            $availableStock = $totalItems - $bookedItems;

            return response()->json([
                'status' => 'success',
                'item' => [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'barcode_number' => $item->barcode_number,
                    'condition' => $item->condition,
                    'image_url' => $item->image_url,
                    'equipment_details' => [
                        'equipment_id' => $item->equipment->equipment_id,
                        'name' => $item->equipment->equipment_name,
                        'description' => $item->equipment->description,
                        'brand' => $item->equipment->brand,
                        'storage_location' => $item->equipment->storage_location,
                        'external_fee' => $item->equipment->external_fee,
                        'rate_type' => $item->equipment->rate_type,
                        'department_id' => $item->equipment->department->department_name ?? 'N/A',
                        'category' => $item->equipment->category->category_name ?? 'N/A',
                    ],
                    'availability_status' => $item->equipment->status
                ],
                'current_bookings' => $currentBookings->map(function($booking) {
                    return [
                        'request_id' => $booking->requisitionForm->request_id,
                        'title' => $booking->requisitionForm->calendar_title,
                        'start_date' => $booking->requisitionForm->start_date,
                        'end_date' => $booking->requisitionForm->end_date,
                        'start_time' => $booking->requisitionForm->start_time,
                        'end_time' => $booking->requisitionForm->end_time,
                        'quantity' => $booking->quantity,
                        'requester' => $booking->requisitionForm->first_name . ' ' . $booking->requisitionForm->last_name
                    ];
                }),
                'available_stock' => max(0, $availableStock),
                'total_stock' => $totalItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to scan barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle borrow request
     */
    public function borrow(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'requisition_form_id' => 'required|exists:requisition_forms,request_id'
        ]);

        try {
            $item = EquipmentItem::where('barcode_number', $request->barcode)->first();

            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment item not found'
                ], 404);
            }

            // Check availability
            $availableStock = $this->calculateAvailableStock($item->equipment_id);
            
            if ($availableStock < $request->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient stock. Available: ' . $availableStock
                ], 400);
            }

            // Create or update requested equipment
            $requestedEquipment = RequestedEquipment::updateOrCreate(
                [
                    'request_id' => $request->requisition_form_id,
                    'equipment_id' => $item->equipment_id
                ],
                [
                    'quantity' => $request->quantity,
                    'is_waived' => false
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Equipment borrowed successfully',
                'data' => $requestedEquipment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process borrow request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle return request
     */
    public function return(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $item = EquipmentItem::where('barcode_number', $request->barcode)->first();

            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment item not found'
                ], 404);
            }

            // Find and update the requested equipment
            $requestedEquipment = RequestedEquipment::where('equipment_id', $item->equipment_id)
                ->where('quantity', '>=', $request->quantity)
                ->first();

            if (!$requestedEquipment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No matching borrow record found'
                ], 404);
            }

            // Reduce quantity or delete if returning all
            if ($requestedEquipment->quantity <= $request->quantity) {
                $requestedEquipment->delete();
            } else {
                $requestedEquipment->quantity -= $request->quantity;
                $requestedEquipment->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Equipment returned successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process return request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate available stock for equipment
     */
    private function calculateAvailableStock($equipmentId): int
    {
        $totalItems = EquipmentItem::where('equipment_id', $equipmentId)
                                 ->whereNull('deleted_at')
                                 ->count();

        $bookedItems = RequestedEquipment::whereHas('requisitionForm', function($query) {
                $query->where('is_closed', false)
                      ->where('is_finalized', true);
            })
            ->where('equipment_id', $equipmentId)
            ->sum('quantity');

        return max(0, $totalItems - $bookedItems);
    }
}
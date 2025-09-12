<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentItem;
use App\Models\AvailabilityStatus;
use App\Models\RequestedEquipment;
use App\Models\RequisitionForm;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScannerController extends Controller
{
    // Scan barcode and get equipment information
    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = $request->input('barcode');
        
        // Check if barcode exists in our system
        $item = EquipmentItem::with(['equipment', 'availabilityStatus'])
                            ->where('barcode', $barcode)
                            ->first();

        if (!$item) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Barcode not recognized. This item is not registered in the system.'
            ], 404);
        }

        // Get available stock count for this equipment type
        $availableStock = EquipmentItem::where('equipment_id', $item->equipment_id)
                                    ->where('availability_status_id', 1) // Assuming 1 is "Available"
                                    ->count();

        return response()->json([
            'status' => 'success',
            'item' => $item,
            'available_stock' => $availableStock,
            'equipment_details' => $item->equipment
        ]);
    }

    // Process borrowing with confirmation delay
    public function borrow(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'sometimes|integer|min:1',
            'requisition_form_id' => 'required|exists:requisition_forms,id'
        ]);

        $barcode = $request->input('barcode');
        $quantity = $request->input('quantity', 1);
        $requisitionFormId = $request->input('requisition_form_id');
        
        // Get the equipment item to determine equipment_id
        $item = EquipmentItem::where('barcode', $barcode)->first();
        
        if (!$item) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Equipment not found'
            ], 404);
        }

        // Check available stock
        $availableItems = EquipmentItem::where('equipment_id', $item->equipment_id)
                                    ->where('availability_status_id', 1) // Available
                                    ->get();

        if ($availableItems->count() < $quantity) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Insufficient stock. Only ' . $availableItems->count() . ' items available.'
            ], 400);
        }

        // For single item scan
        if ($quantity == 1) {
            if ($item->availability_status_id != 1) { // Not Available
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Item is not available for borrowing'
                ], 400);
            }

            // Return confirmation required response with delay
            return response()->json([
                'status' => 'confirmation_required',
                'message' => 'Please confirm borrowing this item',
                'item' => $item,
                'confirmation_timeout' => 10, // seconds
                'confirmation_id' => uniqid() // Generate unique confirmation ID
            ]);
        } else {
            // For bulk quantity - process immediately
            $borrowedItems = [];
            
            DB::beginTransaction();
            try {
                for ($i = 0; $i < $quantity; $i++) {
                    if (isset($availableItems[$i])) {
                        $availableItems[$i]->update([
                            'availability_status_id' => 2, // Assuming 2 is "Borrowed"
                            'borrowed_at' => Carbon::now()
                        ]);
                        
                        // Create requested equipment record
                        RequestedEquipment::create([
                            'requisition_form_id' => $requisitionFormId,
                            'equipment_item_id' => $availableItems[$i]->id,
                            'quantity' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                        
                        $borrowedItems[] = $availableItems[$i];
                    }
                }
                
                DB::commit();
                
                return response()->json([
                    'status' => 'success',
                    'message' => $quantity . ' items borrowed successfully',
                    'borrowed_items' => $borrowedItems
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Failed to process borrowing: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    // Confirm borrowing after delay
    public function confirmBorrow(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'confirmation_id' => 'required|string',
            'requisition_form_id' => 'required|exists:requisition_forms,id'
        ]);

        $barcode = $request->input('barcode');
        $confirmationId = $request->input('confirmation_id');
        $requisitionFormId = $request->input('requisition_form_id');
        
        $item = EquipmentItem::where('barcode', $barcode)->first();
        
        if (!$item) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Equipment not found'
            ], 404);
        }

        if ($item->availability_status_id != 1) { // Not Available
            return response()->json([
                'status' => 'error', 
                'message' => 'Item is no longer available'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update item status
            $item->update([
                'availability_status_id' => 2, // Borrowed
                'borrowed_at' => Carbon::now()
            ]);

            // Create requested equipment record
            RequestedEquipment::create([
                'requisition_form_id' => $requisitionFormId,
                'equipment_item_id' => $item->id,
                'quantity' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Item borrowed successfully',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error', 
                'message' => 'Failed to confirm borrowing: ' . $e->getMessage()
            ], 500);
        }
    }

    // Process returning with confirmation delay
    public function returnItem(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'sometimes|integer|min:1'
        ]);

        $barcode = $request->input('barcode');
        $quantity = $request->input('quantity', 1);
        
        // Get the equipment item to determine equipment_id
        $item = EquipmentItem::where('barcode', $barcode)->first();
        
        if (!$item) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Equipment not found'
            ], 404);
        }

        // Check borrowed items for this equipment
        $borrowedItems = EquipmentItem::where('equipment_id', $item->equipment_id)
                                    ->where('availability_status_id', 2) // Borrowed
                                    ->get();

        if ($borrowedItems->count() < $quantity) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Only ' . $borrowedItems->count() . ' items are currently borrowed.'
            ], 400);
        }

        // For single item return
        if ($quantity == 1) {
            if ($item->availability_status_id != 2) { // Not Borrowed
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Item is not currently borrowed'
                ], 400);
            }

            // Return confirmation required response with delay
            return response()->json([
                'status' => 'confirmation_required',
                'message' => 'Please confirm returning this item',
                'item' => $item,
                'confirmation_timeout' => 10, // seconds
                'confirmation_id' => uniqid() // Generate unique confirmation ID
            ]);
        } else {
            // For bulk quantity - process immediately
            $returnedItems = [];
            
            DB::beginTransaction();
            try {
                for ($i = 0; $i < $quantity; $i++) {
                    if (isset($borrowedItems[$i])) {
                        $borrowedItems[$i]->update([
                            'availability_status_id' => 1, // Available
                            'borrowed_at' => null
                        ]);
                        
                        $returnedItems[] = $borrowedItems[$i];
                    }
                }
                
                DB::commit();
                
                return response()->json([
                    'status' => 'success',
                    'message' => $quantity . ' items returned successfully',
                    'returned_items' => $returnedItems
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Failed to process return: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    // Confirm return after delay
    public function confirmReturn(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'confirmation_id' => 'required|string'
        ]);

        $barcode = $request->input('barcode');
        $confirmationId = $request->input('confirmation_id');
        
        $item = EquipmentItem::where('barcode', $barcode)->first();
        
        if (!$item) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Equipment not found'
            ], 404);
        }

        if ($item->availability_status_id != 2) { // Not Borrowed
            return response()->json([
                'status' => 'error', 
                'message' => 'Item is not currently borrowed'
            ], 400);
        }

        try {
            // Update item status
            $item->update([
                'availability_status_id' => 1, // Available
                'borrowed_at' => null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Item returned successfully',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Failed to confirm return: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get borrowing history by requisition form ID
    public function getHistory(Request $request)
    {
        $request->validate([
            'requisition_form_id' => 'required|exists:requisition_forms,id'
        ]);

        $requisitionFormId = $request->input('requisition_form_id');
        
        $requisitionForm = RequisitionForm::with([
            'requestedEquipment.equipmentItem.equipment',
            'requester',
            'approvals'
        ])->find($requisitionFormId);

        if (!$requisitionForm) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Requisition form not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'requisition_form' => $requisitionForm
        ]);
    }

    // Check equipment status
    public function status(Request $request)
    {
        $barcode = $request->input('barcode');
        $item = EquipmentItem::with(['equipment', 'availabilityStatus'])
                            ->where('barcode', $barcode)
                            ->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Equipment not found']);
        }

        return response()->json([
            'status' => 'success',
            'equipment' => $item
        ]);
    }
}
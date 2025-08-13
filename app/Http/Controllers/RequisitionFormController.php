<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionForm;
use App\Models\RequestedEquipment;
use App\Models\RequestedFacility;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\FormStatus;
use App\Models\AvailabilityStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/* CONTROLLER DOCUMENTATION (expand to view):

--> COMPLETE CONTROLLER USE CASE: 

- to be able to fill up a requisition form using laravel cookies in the user's session, add items to the cart, check availability to avoid booking conflicts (important), and submit the form.

- This controller handles the requisition form process:

    first, everything is saved in the user's session, 
    then it checks for conflicts with existing bookings. data that is saved in the session includes:

    - saving user info, including booking schedule, even if the user leaves the page and comes back later or leaving the website entirely.
    - Set a start_date, start_time, end_date, end_time schedule combo and press a 'Check availability' button that checks it's availability against other existing schedules (loops through schedules of selected items in the user's session via the checkAvailability method in a single call and displays a UI modal of the facility/equipment_id that's conflicting). The time fields in the frontend have a dropdown of a fixed, 12-hour format with am and pm options for consistency in 30 minute intervals (e.g., 1:00 PM, 1:30 PM, 2:00 PM...). In the backend, this should convert the time to a 24-hour format for consistency (H:i: only, no seconds). Seconds are not needed for this use case.
    - adding/removing facility/equipment items to their form
    - uploading temp files to Cloudinary, such as formal letter and facility layout

- after the user has filled up the form, they can submit it. 
in the frontend, clicking on the Submit button will call the submitForm method, which will:

    - in the frontend, display a disclaimer of terms and conditions, and a confirmation dialog to the user. it is required to tick the terms and conditions checkbox before submitting. If checkbox is not ticked, the modal's submit button will be disabled.
    - after accepting the terms and conditions, change submit form button to -> "[loading icon] Processing...]" as post-checks are made:

        - validate the form data
        - check for conflicts once again with existing bookings via the checkAvailability method  (request_id and their requested_facilities_id and requested_equipment_id via eloquent relationships), looping through the selected items' existing booking schedules in the session and the form-level date and time fields. if there are any conflicts, it will return an error message to the frontend and prevent submission.
        - create a new requisition form in the database marked as 'Pending Approval' (status_id = 1). 
        - save the selected items in the requested_facilities (requested_facilities_id) and requested_equipment tables (requested_equipment_id).
        - clear the session data

    - Once validation is complete, update the terms and conditions modal into a success message modal with the following:
        - a success message that the requisition form has been submitted successfully
        - return a success response with the requisition form's randomly generated access_code (10 digits) and request ID (request_id).

Lookup Tables:

    AvailabilityStatus Model (status_id in availability_statuses table):
    '1', 'Available', '#28a745', '1'
    '2', 'Unavailable', '#dc3545', '1'
    '3', 'Under Maintenance', '#ffc107', '1'
    '4', 'Reserved', '#007bff', '1'
    '5', 'Hidden', '#343a40', '1'

    FormStatus Model (status_id in form_statuses table):
    '1', 'Pending Approval', '#FFA500'
    '2', 'In Review', '#00BFFF'
    '3', 'Awaiting Payment', '#FF69B4'
    '4', 'Scheduled', '#9370DB'
    '5', 'Ongoing', '#1E90FF'
    '6', 'Returned', '#20B2AA'
    '7', 'Late Return', '#DC143C'
    '8', 'Completed', '#32CD32'
    '9', 'Rejected', '#B22222'
    '10', 'Cancelled', '#A9A9A9'

    RequisitionPurpose Model (purpose_id in requisition_purposes table):
    '8', 'Alumni - Class Reunion'
    '9', 'Alumni - Personal Events'
    '7', 'Alumni-Organized Events'
    '5', 'CPU Organization Led Activity'
    '2', 'Equipment Rental'
    '10', 'External Event'
    '1', 'Facility Rental'
    '6', 'Student-Organized Activity'
    '3', 'Subject Requirement - Class, Seminar, Conference'
    '4', 'University Program/Activity'

    Condition Model (condition_id in conditions table):
    '1', 'New', '#28a745'
    '2', 'Good', '#20c997'
    '3', 'Fair', '#ffc107'
    '4', 'Needs Maintenance', '#fd7e14'
    '5', 'Damaged', '#dc3545'
    '6', 'In Use', '#6f42c1'

    Note: Condition applies only to equipment. This is used to determine what equipment_item is available for booking. Only New, good, and fair conditions are available for booking.

User should fill in these fields (Upon form submission, the following data will be saved in DB: requisition_forms table):

    (this info is copied from app/Models/RequisitionForm.php)

    protected $fillable = [

        // User information
        'user_type', // 'Internal' or 'External'
        'first_name', // required
        'last_name', // required
        'email', // required
        'school_id', // null for external, required for internal users
        'organization_name',  // optional
        'contact_number', // optional

        // Requisition details
        'num_participants', // required, must be at least 1
        'purpose_id', // this is the purpose of the requisition, e.g., 'Facility Rental', 'Equipment Rental', etc.
        'additional_requests', // optional, additional requests or notes
        'endorser', // optional, name of the endorser (if applicable)
        'date_endorsed', // optional, date endorsed by the endorser (if applicable)

        // User uploads. Formal letter is required, facility layout is optional. Acceptable formats: png, jpg, jpeg, pdf.

        'formal_letter_url', // this is the formal letter uploaded by the user.
        'formal _letter_public_id', // this is the public ID of the formal letter uploaded to Cloudinary.
        'facility_layout_url', // this is the facility layout uploaded by the user.
        'facility_layout_public_id', // this is the public ID of the facility layout uploaded to
        'upload_token', // this is a unique token generated for the upload, used to reference the file later.

        // Booking schedule. this must NOT conflict with existing bookings. Ensure no overlapping dates/times in the controller logic. 

        'start_date',
        'end_date',
        'start_time',
        'end_time',

        // Requested items. This will be saved in the requested_facilities and requested_equipment tables:

        'requested_facilities', // this is an array of facility IDs requested by the user.
        'requested_equipment', // this is an array of equipment IDs requested by the user.

        // Requisition status tracking. Left null if not applicable, status_id will be set to 'Pending Approval' (1) upon submission.

        'status_id', // this is the status of the requisition form, e.g., 'Pending Approval', 'In Review', etc.
        'is_late', // this is a boolean field that indicates if the requisition form is late. Set to true if the form is submitted after the end date.
        'late_penalty_fee', // this is the late penalty fee manually applied by the admins if the form is late.
        'returned_at', date and time that the requested_equipment was returned.
        'is_finalized', // this is a boolean field that indicates if the requisition form is finalized. Set to true if the form is finalized by an admin.
        'finalized_at', // date and time that the requisition form was finalized.
        'finalized_by', // this is the ID of the admin who finalized the requisition form.
        'is_closed', // this is a boolean field that indicates if the requisition form is closed. Set to true if the form is closed by an admin.
        'closed_at', // date and time that the requisition form was closed.
        'closed_by', // this is the ID of the admin who closed the requisition form.
        'official_receipt_no', this is the official receipt number issued by the admin upon finalization of the requisition form.
        'official_receipt_url', // this is the official receipt URL uploaded by the admin upon finalization of the requisition form.
        'official_receipt_public_id', // this is the public ID of the official receipt uploaded to Cloudinary.
        'calendar_title', // this is the title of the calendar event created for the requisition form.
        'calendar_description', // this is the description of the calendar event created for the requisition form.

        'tentative_fee', // this is the total calculated fee of all selected items upon submission.
        'approved_fee' // this is the final approved fee after review. left null for now, until the admins have finalized the requisition form and set the approved fee.

    ];

    protected $casts = [
        'start_date' => 'string',
        'end_date' => 'string',
        'start_time' => 'string',
        'end_time' => 'string',
        'returned_at' => 'datetime',
        'finalized_at' => 'datetime',
        'closed_at' => 'datetime',
        'date_endorsed' => 'datetime',
        'is_late' => 'boolean',
        'is_finalized' => 'boolean',
        'is_closed' => 'boolean',
        'tentative_fee' => 'decimal:2',
        'approved_fee' => 'decimal:2',
    ];  

*/


class RequisitionFormController extends Controller
{
    public function activeSchedules()
    {
        $activeStatuses = [2, 3, 4]; // awaiting payment, scheduled, ongoing

        $schedules = RequisitionForm::whereIn('status_id', $activeStatuses)
            ->get(['start_date', 'start_time', 'end_date', 'end_time', 'status_id'])
            ->map(function ($schedule) {
                $schedule->start_time = substr($schedule->start_time, 0, 5); // keep HH:MM
                $schedule->end_time = substr($schedule->end_time, 0, 5);
                return $schedule;
            });

        return response()->json([
            'schedules' => $schedules
        ]);
    }

    // Common response structure
    protected function jsonResponse($success, $message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    // ----- Save form details in session ----- //
    public function saveRequestInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User information
            'user_type' => 'required|in:Internal,External',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'contact_number' => 'nullable|string|max:15',
            'organization_name' => 'nullable|string|max:100',
            'school_id' => 'nullable|string|max:20',
            // Requisition details
            'additional_requests' => 'nullable|string|max:250',
            'num_participants' => 'required|integer|min:1',
            'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
            'endorser' => 'nullable|string|max:50',
            'date_endorsed' => 'nullable|date_format:Y-m-d',
            // Booking schedule
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $requestInfo = $request->only([
            'user_type',
            'first_name',
            'last_name',
            'email',
            'school_id',
            'organization_name',
            'contact_number',
            'num_participants',
            'purpose_id',
            'additional_requests',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
        ]);

        // Sanitize inputs
        $requestInfo['email'] = filter_var($requestInfo['email'], FILTER_SANITIZE_EMAIL);
        $requestInfo['first_name'] = htmlspecialchars($requestInfo['first_name'], ENT_QUOTES);
        $requestInfo['last_name'] = htmlspecialchars($requestInfo['last_name'], ENT_QUOTES);

        session(['request_info' => $requestInfo]);

        return $this->jsonResponse(true, 'Form details saved successfully.', ['request_info' => $requestInfo]);
    }

    // ----- Add items to session ----- //
    public function addToForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required_without:equipment_id|exists:facilities,facility_id',
            'equipment_id' => 'required_without:facility_id|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment',
            'quantity' => 'required_if:type,equipment|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        try {
            $selectedItems = Session::get('selected_items', []);
            $type = $request->type;
            $idField = $type . '_id';
            $id = $request->input($idField);
            $quantity = $request->quantity ?? 1;

            // Check for duplicate item
            $existingIndex = collect($selectedItems)->search(function ($item) use ($id, $type, $idField) {
                return isset($item[$idField]) && $item[$idField] == $id && $item['type'] === $type;
            });

            if ($existingIndex !== false) {
                if ($type === 'equipment') {
                    $selectedItems[$existingIndex]['quantity'] = $quantity;
                    Session::put('selected_items', $selectedItems);
                    return $this->jsonResponse(true, 'Equipment quantity updated.', [
                        'selected_items' => $selectedItems,
                        'cart_count' => count($selectedItems)
                    ]);
                }
                return $this->jsonResponse(false, 'This item is already in your requisition.', [], 422);
            }

            if (count($selectedItems) >= 10) {
                return $this->jsonResponse(false, 'Maximum item limit (10) reached.', [], 422);
            }

            // Get item details
            if ($type === 'facility') {
                $item = Facility::with(['images', 'category', 'status'])->find($id);
            } else {
                $item = Equipment::with(['images', 'category', 'status'])->find($id);
            }

            if (!$item) {
                return $this->jsonResponse(false, 'Item not found.', [], 404);
            }

            $newItem = [
                'type' => $type,
                $idField => $id,
                'quantity' => $quantity,
                'name' => $type === 'facility' ? $item->facility_name : $item->equipment_name,
                'description' => $item->description,
                'external_fee' => $item->external_fee,
                'rate_type' => $item->rate_type,
                'images' => $item->images->toArray(),
                'added_at' => now()->toDateTimeString()
            ];

            $selectedItems[] = $newItem;
            Session::put('selected_items', $selectedItems);

            return $this->jsonResponse(true, ucfirst($type) . ' added successfully.', [
                'selected_items' => $selectedItems,
                'cart_count' => count($selectedItems)
            ]);

        } catch (\Exception $e) {
            Log::error('Add to form error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'An error occurred.', [], 500);
        }
    }
    // ----- Remove items from session ----- //
    public function removeFromForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required_without:equipment_id|exists:facilities,facility_id',
            'equipment_id' => 'required_without:facility_id|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        try {
            $selectedItems = Session::get('selected_items', []);
            $type = $request->type;
            $idField = $type . '_id'; // Create the correct field name
            $id = $request->input($idField);

            $updatedItems = array_values(array_filter(
                $selectedItems,
                fn($item) => !(isset($item[$idField]) && $item[$idField] == $id && $item['type'] === $type)
            ));

            Session::put('selected_items', $updatedItems);

            return $this->jsonResponse(true, ucfirst($type) . ' removed successfully.', [
                'selected_items' => $updatedItems,
                'cart_count' => count($updatedItems)
            ]);

        } catch (\Exception $e) {
            Log::error('Cart removal error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'An error occurred while removing item.', [], 500);
        }
    }

    // Updated getItems method
    public function getItems(Request $request)
    {
        $selectedItems = Session::get('selected_items', []);

        // Ensure consistent data structure
        $formattedItems = array_map(function ($item) {
            $base = [
                'type' => $item['type'],
                'name' => $item['name'],
                'description' => $item['description'],
                'external_fee' => $item['external_fee'],
                'rate_type' => $item['rate_type'],
                'images' => $item['images'],
            ];

            if ($item['type'] === 'facility') {
                $base['facility_id'] = $item['facility_id'];
            } else {
                $base['equipment_id'] = $item['equipment_id'];
                $base['quantity'] = $item['quantity'] ?? 1;
            }

            return $base;
        }, $selectedItems);

        return response()->json([
            'success' => true,
            'data' => [
                'selected_items' => $formattedItems
            ]
        ]);
    }
    // ----- Check for booking schedule conflicts ----- //
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:facility,equipment',
            'items.*.facility_id' => 'required_if:items.*.type,facility|exists:facilities,facility_id',
            'items.*.equipment_id' => 'required_if:items.*.type,equipment|exists:equipment,equipment_id',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $requestStart = Carbon::createFromFormat('Y-m-d H:i', $request->start_date . ' ' . $request->start_time);
        $requestEnd = Carbon::createFromFormat('Y-m-d H:i', $request->end_date . ' ' . $request->end_time);

        if ($request->start_date === $request->end_date && $requestStart >= $requestEnd) {
            return $this->jsonResponse(false, 'End time must be after start time for the same day.', [], 422);
        }


        $conflictStatusIds = FormStatus::whereIn('status_name', ['Awaiting Payment', 'Scheduled', 'Ongoing'])
            ->pluck('status_id')
            ->toArray();

        $conflicts = false;
        $conflictItems = [];

        foreach ($request->items as $item) {
            $query = RequisitionForm::whereIn('status_id', $conflictStatusIds)
                ->where(function ($q) use ($requestStart, $requestEnd) {
                    // Check for date overlap first (optimization)
                    $q->where(function ($dateQ) use ($requestStart, $requestEnd) {
                        $dateQ->where('start_date', '<=', $requestEnd->format('Y-m-d'))
                            ->where('end_date', '>=', $requestStart->format('Y-m-d'));
                    })
                        // Then check for time overlap (excluding edge cases where one ends when another starts)
                        ->where(function ($timeQ) use ($requestStart, $requestEnd) {
                        $timeQ->where(function ($inner) use ($requestStart, $requestEnd) {
                            $inner->where('start_time', '<', $requestEnd->format('H:i'))
                                ->where('end_time', '>', $requestStart->format('H:i'));
                        });
                    });
                });

            if ($item['type'] === 'facility') {
                $query->whereHas('requestedFacilities', function ($q) use ($item) {
                    $q->where('facility_id', $item['facility_id']);
                });
            } else {
                $query->whereHas('requestedEquipment', function ($q) use ($item) {
                    $q->where('equipment_id', $item['equipment_id']);
                });
            }

            if ($query->exists()) {
                $conflicts = true;
                $itemName = $item['type'] === 'facility'
                    ? Facility::find($item['facility_id'])->facility_name
                    : Equipment::find($item['equipment_id'])->equipment_name;

                $conflictItems[] = [
                    'type' => $item['type'],
                    'id' => $item[$item['type'] . '_id'],
                    'name' => $itemName
                ];
            }
        }

        return $this->jsonResponse(
            true,
            $conflicts ? 'Time slot conflicts with existing booking(s).' : 'Time slot is available.',
            [
                'available' => !$conflicts,
                'conflict_items' => $conflictItems
            ]
        );
    }

    /* // ----- Temporary user uploads before submission ----- //

     // This method handles temporary uploads to Cloudinary

     // Use case: User first uploads files (formal letter, facility layout) in their form session. This will generate a randomized upload_token, and the file will be immediately uploaded to Cloudinary as a temporary file. That upload_token will be used later on in the SubmitForm method to reference the uploaded file when saving the requisition form to the database


    public function tempUpload(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'formal_letter_url' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
             'facility_layout_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
         ]);

         if ($validator->fails()) {
             return response()->json([
                 'success' => false,
                 'errors' => $validator->errors(),
             ], 422);
         }

         try {

             \Log::debug('Cloudinary Config:', [
                 'cloud_name' => config('cloudinary.cloud_name'),
                 'api_key' => config('cloudinary.api_key'),
                 'api_secret' => config('cloudinary.api_secret'),
             ]);

             // Determine which field was uploaded
             $field = $request->hasFile('formal_letter_url') ? 'formal_letter_url' : 'facility_layout_url';
             $file = $request->file($field);

             // Define upload folder based on field
             $folder = $field === 'formal_letter_url'
                 ? 'user-uploads/user-letters'
                 : 'user-uploads/user-setups';

             // Upload to Cloudinary
             $upload = Cloudinary::upload($file->getRealPath(), [
                 'folder' => $folder,
                 'resource_type' => 'auto',
             ]);

             if (!$upload->getSecurePath()) {
                 throw new \Exception('Cloudinary upload failed.');
             }

             // Generate a unique token for this upload
             $uploadToken = Str::random(40);

             // Store in session (to reference later when saving to DB)
             $tempUploads = session('temp_uploads', []);
             $tempUploads[$field] = [
                 'file_url' => $upload->getSecurePath(),
                 'public_id' => $upload->getPublicId(),
                 'upload_token' => $uploadToken,
             ];
             session(['temp_uploads' => $tempUploads]);

             return response()->json([
                 'success' => true,
                 'message' => 'File uploaded successfully.',
                 'data' => [
                     'field' => $field,
                     'file_url' => $upload->getSecurePath(),
                     'public_id' => $upload->getPublicId(),
                     'upload_token' => $uploadToken,
                 ],
             );
         } catch (\Exception $e) {
             return response()->json([
                 'success' => false,
                 'message' => 'Upload failed: ' . $e->getMessage(),
             ], 500);
         }
     } */


    // This method handles temporary uploads to Cloudinary



    // Use case: User first uploads files (formal letter, facility layout) in their form session. This will generate a randomized upload_token, and the file will be immediately uploaded to Cloudinary as a temporary file. That upload_token will be used later on in the SubmitForm method to reference the uploaded file when saving the requisition form to the database


    public function tempUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formal_letter_url' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'facility_layout_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Log session before upload
            \Log::debug('Pre-upload session data', ['session' => session()->all()]);

            $field = $request->hasFile('formal_letter_url') ? 'formal_letter_url' : 'facility_layout_url';
            $file = $request->file($field);

            $folder = $field === 'formal_letter_url'
                ? 'user-uploads/user-letters'
                : 'user-uploads/user-setups';

            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto',
            ]);

            if (!$upload->getSecurePath()) {
                throw new \Exception('Cloudinary upload failed.');
            }

            $uploadToken = Str::random(40);

            // Store upload in session with clear structure
            $tempUploads = session('temp_uploads', []);
            $tempUploads[$field] = [
                'url' => $upload->getSecurePath(),
                'public_id' => $upload->getPublicId(),
                'token' => $uploadToken,
                'type' => $field === 'formal_letter_url' ? 'Letter' : 'Setup'
            ];
            session(['temp_uploads' => $tempUploads]);

            // Log session after upload
            \Log::debug('Post-upload session data', ['session' => session()->all()]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data' => $tempUploads[$field],
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ----- Submit requisition form with overbooking protection ----- //
    public function submitForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:Internal,External',
            'school_id' => 'required_if:user_type,Internal|nullable|string|max:20',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'contact_number' => ['nullable', 'regex:/^\d{1,15}$/', 'max:15'],
            'organization_name' => 'nullable|string|max:100',
            'num_participants' => 'required|integer|min:1',
            'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
            'additional_requests' => 'nullable|string|max:250',
            'endorser' => 'nullable|string|max:50',
            'date_endorsed' => 'nullable|date_format:Y-m-d',
            'formal_letter_url' => 'required|url',
            'formal_letter_public_id' => 'required|string|max:255',
            'facility_layout_url' => 'nullable|string|max:255',
            'facility_layout_public_id' => 'nullable|string|max:255',
            // ...other fields as needed...
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray() // Return full error details
            ], 422);
        }

        DB::beginTransaction();

        try {

            // Get selected items from session (still needed)
            $selectedItems = session('selected_items', []);
            if (empty($selectedItems)) {
                throw new \Exception('Your booking cart is empty. Add items before submitting.');
            }

            // Debug session data
            \Log::debug('Submission session data', [
                'request_info' => session('request_info'),
                'selected_items' => session('selected_items'),
                'temp_uploads' => session('temp_uploads'),
                'all_session' => session()->all()
            ]);

            $requestInfo = session('request_info');
            $selectedItems = session('selected_items', []);
            $tempUploads = session('temp_uploads', []);

            if (!$request->first_name || !$request->last_name || !$request->email) {
                throw new \Exception('User information not found. Please fill in all required fields.');
            }

            $conflictCheck = $this->checkAvailability(new Request([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'items' => array_map(function ($item) {
                    return [
                        'type' => $item['type'],
                        $item['type'] . '_id' => $item[$item['type'] . '_id']
                    ];
                }, $selectedItems)
            ]));

            $conflictData = $conflictCheck->getData();
            if (!$conflictData->success || !$conflictData->data->available) {
                throw new \Exception($conflictData->message ?? 'Time slot no longer available. Please choose another.');
            }

            // Get uploads - handle null case explicitly
            if (!$request->formal_letter_url) {
                throw new \Exception('A formal letter must be uploaded.');
            }

            $accessCode = Str::upper(Str::random(10));
            \Log::debug('Generated access code', ['code' => $accessCode]);
            \Log::debug('Submitting form data', [
                'facility_layout_url' => $request->facility_layout_url,
                'facility_layout_public_id' => $request->facility_layout_public_id,
                'all_data' => $request->all()
            ]);
            

            // Create requisition form
            $requisitionForm = RequisitionForm::create([
                'user_type' => $request->user_type, // Use submitted value
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'organization_name' => $request->organization_name,
                'school_id' => $request->school_id,
                'access_code' => Str::upper(Str::random(10)),
                'purpose_id' => $request->purpose_id,
                'num_participants' => $request->num_participants,
                'additional_requests' => $request->additional_requests,
                'formal_letter_url' => $request->formal_letter_url,
                'formal_letter_public_id' => $request->formal_letter_public_id,
                'facility_layout_url' => $request->facility_layout_url ?? null,
                'facility_layout_public_id' => $request->facility_layout_public_id ?? null,
                'upload_token' => Str::random(40), // assume same token used for both
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status_id' => FormStatus::where('status_name', 'Pending Approval')->value('status_id'),
                'tentative_fee' => session('fee_summary.total_fee', 0),
            ]);

            // Save selected items
            foreach ($selectedItems as $item) {
                if ($item['type'] === 'facility') {
                    RequestedFacility::create([
                        'request_id' => $requisitionForm->request_id,
                        'facility_id' => $item['facility_id'] ?? $item['id'], // Handle both formats
                        'is_waived' => false,
                    ]);
                } elseif ($item['type'] === 'equipment') {
                    $equipmentItems = \App\Models\EquipmentItem::where('equipment_id', $item['equipment_id'] ?? $item['id'])
                        ->whereIn('condition_id', [1, 2, 3])
                        ->whereNull('deleted_at')
                        ->limit($item['quantity'] ?? 1)
                        ->get();

                    if ($equipmentItems->count() < ($item['quantity'] ?? 1)) {
                        throw new \Exception("Not enough items available for equipment ID {$item['id']}.");
                    }

                    foreach ($equipmentItems as $equipmentItem) {
                        RequestedEquipment::create([
                            'request_id' => $requisitionForm->request_id,
                            'equipment_id' => $item['id'],
                            'item_id' => $equipmentItem->item_id,
                            'is_waived' => false,
                        ]);

                        DB::table('equipment_items')
                            ->where('item_id', $equipmentItem->item_id)
                            ->update(['deleted_at' => now()]);
                    }
                }
            }

            // Clear session
            session()->forget(['request_info', 'selected_items', 'fee_summary', 'temp_uploads']);

            DB::commit();

            return $this->jsonResponse(true, 'Requisition submitted successfully!', [
                'access_code' => $requisitionForm->access_code,
                'request_id' => $requisitionForm->request_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Submission error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Submission failed: ' . $e->getMessage(), [], 500);
        }
    }

    public function clearSession()
    {
        session()->forget(['request_info', 'selected_items', 'fee_summary', 'temp_uploads']);
        return response()->json(['success' => true]);
    }

}
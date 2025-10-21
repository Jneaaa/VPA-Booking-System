@extends('layouts.admin')

@section('title', 'Equipment Scanner')

@section('content')

    <style>
        /* Equipment Item Image Styles */
        #equipment-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1rem 0;
            padding: 0.40rem;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.36);
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        #equipment-item-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Button disabled states */
        .button-small:disabled {
            opacity: 0.6;
            cursor: not-allowed !important;
        }

        .button-small:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Ensure both status badges display inline and have proper spacing */
        .info-item .badge-status {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* Make sure the info items have proper spacing */
        .info-item {
            margin: 0.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Scanner layout */
        #scannerContainer {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 72px);
            justify-content: flex-start;
            align-items: center;
            padding: 1rem;
            gap: 1rem;
        }

        .scanner-box {
            padding: 1.5rem;
            width: 100%;
            max-width: 700px;
            text-align: center;
        }

        #reader {
            width: 100%;
            max-width: 420px;
            height: 300px;
            margin: 0.75rem auto;
            border: 3px solid #fff;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
        }

        /* small control buttons */
        .btn-controls {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 0.75rem;
        }

        .button-small {
            padding: 0.45rem 0.75rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        #stop-scan {
            background: #ff6b6b;
            color: white;
        }

        #resume-scan {
            background: #ffd43b;
            color: #012952;
        }

        /* Info Box */
        .info-box {
            background: #fff;
            color: #333;
            border-radius: 16px 16px 0 0;
            padding: 1.25rem;
            width: 100%;
            max-width: 700px;
            margin-top: auto;
            box-shadow: 0 -6px 20px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: bold;
        }

        .info-value {
            float: right;
        }

        .info-item {
            margin: 0.35rem 0;
            display: flex;
            justify-content: space-between;
        }

        .badge-status {
            padding: 0.3rem 0.75rem;
            border-radius: 12px;
            font-weight: 700;
        }

        .status-available {
            background: #28a745;
            color: white;
        }

        .status-used {
            background: #ffc107;
            color: #222;
        }

        .status-maintenance {
            background: #17a2b8;
            color: white;
        }

        .status-unavailable {
            background: #dc3545;
            color: white;
        }

        /* Confirmation Dialog */
        #confirmation-dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            color: #012952;
            text-align: center;
            min-width: 300px;
        }

        #confirmation-dialog button {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #confirmation-dialog #confirm-action {
            background: #28a745;
            color: white;
        }

        #confirmation-dialog #cancel-action {
            background: #dc3545;
            color: white;
        }

        /* Responsive */
        @media(max-width: 768px) {
            #reader {
                height: 240px;
                max-width: 100%;
            }

            #uploadInput {
                max-width: 100%;
            }

            .scanner-box,
            .info-box {
                padding: 1rem;
            }

            h2 {
                font-size: 1.2rem;
            }

            p {
                font-size: 0.95rem;
            }
        }

        @media(max-width: 420px) {
            #reader {
                height: 200px;
            }

            body,
            html {
                font-size: 14px;
            }

            #back-btn {
                margin: 0.5rem;
                padding: 0.45rem 0.75rem;
            }
        }
    </style>
    <!-- Pass conditions data from backend to frontend -->
    @php
        $conditions = \App\Models\LookupTables\Condition::all()->pluck('condition_name', 'condition_id')->toArray();
    @endphp
    <script>
        window.conditions = @json($conditions);
    </script>

    <main>
        <div id="scannerContainer">
            <!-- Scanner Section -->
            <div class="scanner-box">
                <h2 class="fw-bold">Start Scanning</h2>
                <p>Use camera to scan an equipment's barcode</p>

                <div id="reader"></div>

                <div class="btn-controls">
                    <button id="stop-scan" class="button-small" type="button">Stop Scan</button>
                    <button id="resume-scan" class="button-small" type="button" style="display:none;">Resume Scan</button>
                </div>

                <div id="scan-result" class="scan-result mt-3" style="margin-top:0.75rem;">
                    Scanned Code: <strong><span id="scanned-value">None</span></strong>
                </div>
            </div>

        <!-- Equipment Details Section -->
<div class="info-box" id="equipment-info" style="display:none;">
    <h5>Equipment Details</h5>

    <!-- Equipment Item Image -->
    <div id="equipment-image-container">
        <img id="equipment-item-image" src="" alt="Equipment Item" style="display:none;">
    </div>

    <div class="info-item"><span class="info-label">Name:</span> <span class="info-value" id="eq-name"></span></div>
    <div class="info-item"><span class="info-label">Department:</span> <span class="info-value" id="eq-department"></span></div>

    <!-- Condition Status -->
    <div class="info-item"><span class="info-label">Condition:</span> <span class="info-value"><span id="eq-condition" class="badge-status"></span></span></div>

    <div class="info-item"><span class="info-label">Available Stock:</span> <span class="info-value" id="eq-stock"></span></div>
    <div class="info-item"><span class="info-label">Price:</span> <span class="info-value">₱<span id="eq-price"></span></span></div>
    
    <!-- Changed from "Description" to "Item Notes" -->
    <div class="info-item"><span class="info-label">Item Notes:</span> <span class="info-value" id="eq-description"></span></div>

    <!-- Action buttons -->
    <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center;">
        <button id="borrow-btn" class="button-small" style="background: #28a745; color: white;">Borrow</button>
        <button id="return-btn" class="button-small" style="background: #17a2b8; color: white;">Return</button>
    </div>
</div>
        </div>
    </main>

@endsection

@section('scripts')
    <script src="{{ asset('js/admin/toast.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // DOM refs
            const resultSpan = document.getElementById("scanned-value");
            const infoBox = document.getElementById("equipment-info");
            const uploadInput = document.getElementById("uploadInput");

            const eqName = document.getElementById("eq-name");
            const eqDepartment = document.getElementById("eq-department");
            const eqStatus = document.getElementById("eq-status");
            const eqStock = document.getElementById("eq-stock");
            const eqPrice = document.getElementById("eq-price");
            const eqDescription = document.getElementById("eq-description");
            const eqCondition = document.getElementById("eq-condition");
            const equipmentImage = document.getElementById("equipment-item-image");
            const equipmentImageContainer = document.getElementById("equipment-image-container");


            const stopBtn = document.getElementById("stop-scan");
            const resumeBtn = document.getElementById("resume-scan");
            const borrowBtn = document.getElementById("borrow-btn");
            const returnBtn = document.getElementById("return-btn");

            const token = localStorage.getItem("adminToken");
            let currentBarcode = null;
            let confirmationTimeout = null;

            // html5-qrcode instance
            const html5QrCode = new Html5Qrcode("reader");
            let scannerRunning = false;

            // Choose prefix used in your system. Change if different.
            const SYSTEM_PREFIX = "EQ-";

            function getConditionName(conditionId) {
                // Use the conditions data passed from Laravel
                return window.conditions[conditionId] || "Unknown";
            }

            function getStatusClass(conditionName) {
                if (!conditionName) return "status-unavailable";

                // Map condition names to appropriate status classes
                const conditionMap = {
                    'new': 'status-available',
                    'good': 'status-available',
                    'fair': 'status-available',
                    'available': 'status-available',
                    'needs maintenance': 'status-maintenance',
                    'under maintenance': 'status-maintenance',
                    'damaged': 'status-unavailable',
                    'in use': 'status-used',
                    'unavailable': 'status-unavailable'
                };

                const normalizedCondition = conditionName.toLowerCase().trim();
                return conditionMap[normalizedCondition] || "status-unavailable";
            }

async function fetchEquipmentDetails(code) {
    try {
        const response = await fetch(`/api/scanner/scan`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ barcode: code })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || "Equipment not found");
        }

        if (data.status === 'error') {
            throw new Error(data.message);
        }

        // Update to match the response structure from ScannerController
        const item = data.item;
        const equipment = item.equipment_details;

        // FIX: Use item_name from equipment_items table instead of equipment name
        eqName.textContent = item.item_name || "N/A";
        eqDepartment.textContent = equipment.department_id || "N/A";

        // Display Condition Status
        const conditionName = item.condition_name || getConditionName(item.condition_id);
        eqCondition.textContent = conditionName;
        eqCondition.className = "badge-status " + getConditionStatusClass(conditionName);

        // FIX: Use the accurate stock calculation
        eqStock.textContent = data.available_stock + " / " + data.total_stock;
        eqPrice.textContent = equipment.external_fee || "0.00";
        
        // Display item_notes instead of equipment description
        eqDescription.textContent = item.item_notes || "No notes available";

        // Display Equipment Item Image
        displayEquipmentItemImage(item);

        // Update action buttons based on item condition
        updateActionButtons(item);

        // STORE THE EQUIPMENT ID FOR LATER USE IN UPDATE FUNCTION
        window.currentEquipmentId = equipment.equipment_id;

        infoBox.style.display = "block";

    } catch (error) {
        console.error("Error fetching equipment:", error);
        eqName.textContent = "Not Found";
        eqDepartment.textContent = "-";
        eqCondition.textContent = "Unknown";
        eqCondition.className = "badge-status status-unavailable";
        eqStock.textContent = "0";
        eqPrice.textContent = "0.00";
        eqDescription.textContent = error.message || "No notes available";

        // Hide image on error
        equipmentImage.style.display = "none";

        infoBox.style.display = "block";
        showToast(error.message || 'Equipment not found in database', 'error');
    }
}
            function displayEquipmentItemImage(item) {
                if (item.cloudinary_public_id && item.cloudinary_public_id !== 'oxvsxogzu9koqhctnf7s') {
                    // Construct Cloudinary URL with transformations
                    const imageUrl = `https://res.cloudinary.com/dn98ntlkd/image/upload/w_300,h_200,c_fill/${item.cloudinary_public_id}.webp`;

                    equipmentImage.src = imageUrl;
                    equipmentImage.alt = item.item_name || 'Equipment Item';
                    equipmentImage.style.display = 'block';

                    // Add error handling for broken images
                    equipmentImage.onerror = function () {
                        console.warn('Failed to load equipment item image, using fallback');
                        equipmentImage.style.display = 'none';
                    };
                } else {
                    // Hide image if no valid public_id or using default placeholder
                    equipmentImage.style.display = 'none';
                }
            }

            function getConditionStatusClass(conditionName) {
                if (!conditionName) return "status-unavailable";

                // Map condition names to appropriate status classes
                const conditionMap = {
                    'new': 'status-available',
                    'good': 'status-available',
                    'fair': 'status-available',
                    'needs maintenance': 'status-maintenance',
                    'damaged': 'status-unavailable',
                    'in use': 'status-used'
                };

                const normalizedCondition = conditionName.toLowerCase().trim();
                return conditionMap[normalizedCondition] || "status-unavailable";
            }

            function updateActionButtons(itemData) {
                const borrowBtn = document.getElementById('borrow-btn');
                const returnBtn = document.getElementById('return-btn');

                // Disable borrow button if condition is 'In Use' (condition_id = 6)
                if (itemData.condition_id === 6) {
                    borrowBtn.disabled = true;
                    borrowBtn.style.opacity = '0.6';
                    borrowBtn.style.cursor = 'not-allowed';
                    borrowBtn.title = 'Cannot borrow - Item is already in use';
                } else {
                    borrowBtn.disabled = false;
                    borrowBtn.style.opacity = '1';
                    borrowBtn.style.cursor = 'pointer';
                    borrowBtn.title = 'Borrow this item';
                }

                // Enable return button only if item is "In Use" (condition_id = 6)
                if (itemData.condition_id === 6) {
                    returnBtn.disabled = false;
                    returnBtn.style.opacity = '1';
                    returnBtn.style.cursor = 'pointer';
                    returnBtn.title = 'Return this item';
                } else {
                    returnBtn.disabled = true;
                    returnBtn.style.opacity = '0.6';
                    returnBtn.style.cursor = 'not-allowed';
                    returnBtn.title = 'Cannot return - Item is not in use';
                }
            }

            // Called when a barcode/QR is decoded
            async function onScanSuccess(decodedText) {
                console.log('Raw scanned text:', decodedText);

                if (!decodedText) {
                    showToast("No barcode data detected", "error");
                    return;
                }

                // Clean and normalize the barcode for our system
                let cleanBarcode = decodedText.toString().trim();

                // Remove any whitespace and special characters
                cleanBarcode = cleanBarcode.replace(/\s/g, '');

                // Ensure it starts with EQ- (our system format)
                if (!cleanBarcode.startsWith('EQ-')) {
                    // Try to find EQ pattern in various formats
                    const eqMatch = cleanBarcode.match(/(EQ[A-Z0-9\-]{5,})/i);
                    if (eqMatch) {
                        let extractedCode = eqMatch[1];
                        // Convert to proper EQ- format
                        if (!extractedCode.startsWith('EQ-')) {
                            cleanBarcode = 'EQ-' + extractedCode.substring(2);
                        } else {
                            cleanBarcode = extractedCode;
                        }
                    } else {
                        showToast(`Scanned: "${decodedText}"\nOur system uses EQ-XXXXXXX format`, "error");
                        return;
                    }
                }

                // Final cleanup - only allow alphanumeric and hyphen
                cleanBarcode = cleanBarcode.replace(/[^A-Z0-9\-]/gi, '');

                console.log('Cleaned barcode for lookup:', cleanBarcode);

                // Store the current barcode for later use
                currentBarcode = cleanBarcode;
                resultSpan.textContent = cleanBarcode;

                // Stop camera scanning to avoid duplicate scans
                if (scannerRunning) {
                    try {
                        await html5QrCode.stop();
                    } catch (e) {
                        console.log('Stop scanner error:', e);
                    }
                    scannerRunning = false;
                    stopBtn.style.display = "none";
                    resumeBtn.style.display = "inline-block";
                }

                // Verify from DB and show details
                await fetchEquipmentDetails(cleanBarcode);
            }

            // Start camera scanning
            async function startScanner() {
                if (scannerRunning) return;
                try {
                    // prefer facingMode environment for mobile back camera
                    await html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 300, height: 200 } },
                        (decodedText, decodedResult) => {
                            // html5-qrcode returns both; we use decodedText
                            onScanSuccess(decodedText);
                        },
                        (errorMessage) => {
                            // optional: console.debug("QR error", errorMessage);
                        }
                    );
                    scannerRunning = true;
                    stopBtn.style.display = "inline-block";
                    resumeBtn.style.display = "none";
                } catch (err) {
                    console.error("Scanner start error:", err);
                    alert("Unable to start camera scanner. Check camera permissions or try uploading an image.");
                }
            }

            // Stop scanning
            async function stopScanner() {
                if (!scannerRunning) return;
                try {
                    await html5QrCode.stop();
                } catch (err) {
                    console.warn("Stop scanner error:", err);
                } finally {
                    scannerRunning = false;
                    stopBtn.style.display = "none";
                    resumeBtn.style.display = "inline-block";
                }
            }

            async function handleBorrow() {
                if (!currentBarcode) return;

                try {
                    const response = await fetch('/api/scanner/borrow', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            barcode: currentBarcode
                        })
                    });

                    // Check if response is OK before parsing JSON
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        showToast(data.message || 'Item borrowed successfully.', 'success');
                        fetchEquipmentDetails(currentBarcode); // Refresh data
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                } catch (error) {
                    console.error('Borrow error:', error);
                    showToast('Failed to process borrow request: ' + error.message, 'error');
                }
            }

async function handleReturn() {
    if (!currentBarcode) return;

    try {
        const response = await fetch('/api/scanner/return', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                barcode: currentBarcode
            })
        });

        // Check if response is OK before parsing JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            showUpdateItemModal(data.item);
            
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    } catch (error) {
        console.error('Return error:', error);
        showToast('Failed to process return request: ' + error.message, 'error');
    }
}

            // Function to show update item modal
      function showUpdateItemModal(itemData) {
    // Store the item data for later use
    window.currentItemData = itemData;
    
    // Create Bootstrap modal if it doesn't exist
    let updateModal = document.getElementById('update-item-modal');
    if (!updateModal) {
        updateModal = document.createElement('div');
        updateModal.id = 'update-item-modal';
        updateModal.className = 'modal fade';
        updateModal.tabIndex = -1;
        updateModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Equipment Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="update-item-form">
                            <div class="mb-3">
                                <label class="form-label">Item Name:</label>
                                <input type="text" id="item-name" name="item_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Condition:</label>
                                <select id="item-condition" name="condition_id" class="form-select">
                                    <option value="1">New</option>
                                    <option value="2">Good</option>
                                    <option value="3">Fair</option>
                                    <option value="4">Needs Maintenance</option>
                                    <option value="5">Damaged</option>
                                    <option value="6">In Use</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Item Notes:</label>
                                <textarea id="item-notes" name="item_notes" class="form-control" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="save-update" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(updateModal);

        // Add event listener for save button
        document.getElementById('save-update').addEventListener('click', async () => {
            await updateItem(window.currentItemData.item_id);
        });

        // Remove the old hidden.bs.modal event listener and add a new one
        updateModal.addEventListener('hidden.bs.modal', function () {
            // Only refresh if we didn't just save changes (handled in updateItem)
            if (!window.itemWasJustUpdated) {
                fetchEquipmentDetails(currentBarcode);
            }
            window.itemWasJustUpdated = false;
        });
    }

    // Populate form with current item data
    document.getElementById('item-name').value = itemData.item_name || '';
    document.getElementById('item-condition').value = itemData.condition_id || 2;
    document.getElementById('item-notes').value = itemData.item_notes || '';

    // Reset the update flag
    window.itemWasJustUpdated = false;

    // Show the modal using Bootstrap
    const bootstrapModal = new bootstrap.Modal(updateModal);
    bootstrapModal.show();
}
            // Function to update item
    async function updateItem(itemId) {
    try {
        const formData = {
            item_name: document.getElementById('item-name').value,
            condition_id: parseInt(document.getElementById('item-condition').value),
            item_notes: document.getElementById('item-notes').value
        };

        console.log('Updating item:', { itemId, formData }); // Debug log

        // Use the scanner update route
        const response = await fetch(`/api/scanner/update-item/${itemId}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        // Check if response is OK before parsing JSON
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            showToast('Item updated successfully.', 'success');
            
            // Hide the modal using Bootstrap
            const updateModal = document.getElementById('update-item-modal');
            const bootstrapModal = bootstrap.Modal.getInstance(updateModal);
            bootstrapModal.hide();
            
            // IMPORTANT: Refresh the equipment details to show updated state
            // Wait a brief moment for modal to close completely
            setTimeout(() => {
                fetchEquipmentDetails(currentBarcode);
            }, 300);
            
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }

    } catch (error) {
        console.error('Update item error:', error);
        showToast('Failed to update item: ' + error.message, 'error');
    }
}

            // Show confirmation dialog
            function showConfirmationUI(action, timeoutSeconds) {
                // Create or show confirmation dialog
                let confirmationDialog = document.getElementById('confirmation-dialog');
                if (!confirmationDialog) {
                    confirmationDialog = document.createElement('div');
                    confirmationDialog.id = 'confirmation-dialog';
                    confirmationDialog.style.position = 'fixed';
                    confirmationDialog.style.top = '50%';
                    confirmationDialog.style.left = '50%';
                    confirmationDialog.style.transform = 'translate(-50%, -50%)';
                    confirmationDialog.style.backgroundColor = 'white';
                    confirmationDialog.style.padding = '20px';
                    confirmationDialog.style.borderRadius = '10px';
                    confirmationDialog.style.zIndex = '1000';
                    confirmationDialog.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
                    confirmationDialog.style.color = '#012952';

                    confirmationDialog.innerHTML = `
                                            <h4>Confirm ${action === 'borrow' ? 'Borrow' : 'Return'}</h4>
                                            <p>Please confirm this action. Auto-cancelling in <span id="countdown">${timeoutSeconds}</span> seconds.</p>
                                            <button id="confirm-action">Confirm</button>
                                            <button id="cancel-action">Cancel</button>
                                        `;

                    document.body.appendChild(confirmationDialog);

                    // Add event listeners
                    document.getElementById('confirm-action').addEventListener('click', () => {
                        clearTimeout(confirmationTimeout);
                        confirmAction(action);
                        document.body.removeChild(confirmationDialog);
                    });

                    document.getElementById('cancel-action').addEventListener('click', () => {
                        clearTimeout(confirmationTimeout);
                        document.body.removeChild(confirmationDialog);
                    });
                }

                // Start countdown
                let secondsLeft = timeoutSeconds;
                const countdownElement = document.getElementById('countdown');

                confirmationTimeout = setInterval(() => {
                    secondsLeft--;
                    countdownElement.textContent = secondsLeft;

                    if (secondsLeft <= 0) {
                        clearTimeout(confirmationTimeout);
                        document.body.removeChild(confirmationDialog);
                        alert('Action cancelled due to timeout');
                    }
                }, 1000);
            }

            // Confirm action
            async function confirmAction(action) {
                try {
                    const endpoint = action === 'borrow' ? '/api/scanner/confirm-borrow' : '/api/scanner/confirm-return';

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            barcode: currentBarcode,
                            confirmation_id: 'temp-id', // You should generate and store this
                            requisition_form_id: action === 'borrow' ? 1 : undefined // Get from somewhere
                        })
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        alert(`Item ${action === 'borrow' ? 'borrowed' : 'returned'} successfully.`);
                        fetchEquipmentDetails(currentBarcode); // Refresh data
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Confirm action error:', error);
                    alert('Failed to confirm action');
                }
            }

            // Wire up event listeners
            stopBtn.addEventListener("click", stopScanner);
            resumeBtn.addEventListener("click", async () => {
                // clear previous results and info to scan new
                resultSpan.textContent = "None";
                infoBox.style.display = "none";
                await startScanner();
            });

            borrowBtn.addEventListener("click", handleBorrow);
            returnBtn.addEventListener("click", handleReturn);

            // Try to start scanner on load
            startScanner();

            // Initialize Dynamsoft Barcode Reader
            let scanner = null;
            async function initDynamsoftScanner() {
                try {
                    // Configure Dynamsoft (free tier available)
                    Dynamsoft.DBR.BarcodeReader.license = 'DLS2eyJvcmdhbml6YXRpb25JRCI6IjIwMDAwMSJ9';
                    Dynamsoft.DBR.BarcodeReader.engineResourcePath = "https://cdn.jsdelivr.net/npm/dynamsoft-javascript-barcode@9.6.20/dist/";
                    scanner = await Dynamsoft.DBR.BarcodeScanner.createInstance();
                    console.log('Dynamsoft Barcode Scanner initialized');
                } catch (ex) {
                    console.warn('Dynamsoft initialization failed, using Quagga fallback:', ex);
                }
            }

            // Initialize on load
            initDynamsoftScanner();



            async function scanWithQuagga(imageData) {
                return new Promise((resolve) => {
                    // Configuration optimized for EQ-XXXXXXX format (CODE128)
                    const config = {
                        src: imageData,
                        numOfWorkers: 4, // Use workers for better performance
                        inputStream: {
                            size: 800,
                            type: "ImageStream",
                            area: { // Define scan area for better accuracy
                                top: "0%",    // Top position
                                right: "0%",  // Right position
                                left: "0%",   // Left position
                                bottom: "0%"  // Bottom position
                            }
                        },
                        locator: {
                            patchSize: "x-large", // Larger patches for better detection
                            halfSample: true
                        },
                        decoder: {
                            readers: [
                                "code_128_reader", // Primary - for EQ- format
                                "ean_reader",
                                "ean_8_reader",
                                "code_39_reader",
                                "code_39_vin_reader",
                                "codabar_reader",
                                "upc_reader",
                                "upc_e_reader"
                            ]
                        },
                        locate: true,
                        debug: {
                            drawBoundingBox: false,
                            showFrequency: false,
                            drawScanline: false,
                            showPattern: false
                        }
                    };

                    Quagga.decodeSingle(config, function (result) {
                        if (result && result.codeResult && result.codeResult.code) {
                            console.log('Quagga barcode result:', result.codeResult);
                            resolve(result.codeResult.code);
                        } else {
                            console.log('No barcode found in first attempt');
                            resolve(null);
                        }
                    });
                });
            }

            async function scanWithImagePreprocessing(img) {
                // Create canvas for image processing
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;

                // Try different image processing techniques
                const processingTechniques = [
                    { method: 'original', filter: 'none' },
                    { method: 'enhanced_contrast', filter: 'contrast(1.5) brightness(1.1)' },
                    { method: 'grayscale', filter: 'grayscale(1) contrast(1.2)' },
                    { method: 'high_contrast', filter: 'contrast(2) brightness(0.9)' }
                ];

                for (let technique of processingTechniques) {
                    console.log('Trying processing technique:', technique.method);

                    // Apply filter
                    ctx.filter = technique.filter;
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    // Convert to grayscale if needed for better barcode detection
                    if (technique.method.includes('grayscale')) {
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const data = imageData.data;
                        for (let i = 0; i < data.length; i += 4) {
                            const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
                            data[i] = data[i + 1] = data[i + 2] = gray;
                        }
                        ctx.putImageData(imageData, 0, 0);
                    }

                    const processedImageData = canvas.toDataURL();
                    const result = await scanWithQuagga(processedImageData);

                    if (result) {
                        console.log('Found barcode with technique:', technique.method);
                        return result;
                    }

                    // Reset filter for next iteration
                    ctx.filter = 'none';
                }

                return null;
            }
        });
    </script>

@endsection
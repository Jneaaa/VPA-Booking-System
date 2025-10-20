@extends('layouts.admin')

@section('title', 'Equipment Scanner')

@section('content')

    <style>
        /* Add to your existing CSS */
        #update-item-modal {
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
            min-width: 400px;
            max-width: 90vw;
        }

        #update-item-modal input,
        #update-item-modal select,
        #update-item-modal textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        #update-item-modal label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
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
                <div class="info-item"><span class="info-label">Name:</span> <span class="info-value" id="eq-name"></span>
                </div>
                <div class="info-item"><span class="info-label">Department:</span> <span class="info-value"
                        id="eq-department"></span></div>
                <div class="info-item"><span class="info-label">Status:</span> <span class="info-value"><span id="eq-status"
                            class="badge-status"></span></span></div>
                <div class="info-item"><span class="info-label">Available Stock:</span> <span class="info-value"
                        id="eq-stock"></span></div>
                <div class="info-item"><span class="info-label">Price:</span> <span class="info-value">₱<span
                            id="eq-price"></span></span></div>
                <div class="info-item"><span class="info-label">Description:</span> <span class="info-value"
                        id="eq-description"></span></div>

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

            function getStatusClass(status) {
                if (!status) return "status-unavailable";
                switch (status.toLowerCase()) {
                    case "available": return "status-available";
                    case "used": return "status-used";
                    case "under maintenance": return "status-maintenance";
                    case "unavailable": return "status-unavailable";
                    default: return "status-unavailable";
                }
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

                    eqName.textContent = equipment.name || "N/A";
                    eqDepartment.textContent = equipment.department_id || "N/A";

                    // Display condition instead of availability status for better accuracy
                    const conditionName = getConditionName(item.condition_id);
                    eqStatus.textContent = conditionName;
                    eqStatus.className = "badge-status " + getStatusClass(conditionName);

                    eqStock.textContent = data.available_stock + " / " + data.total_stock;
                    eqPrice.textContent = equipment.external_fee || "0.00";
                    eqDescription.textContent = equipment.description || "No description";

                    // Update action buttons based on item condition
                    updateActionButtons(item);

                    // STORE THE EQUIPMENT ID FOR LATER USE IN UPDATE FUNCTION
                    window.currentEquipmentId = equipment.equipment_id;

                    infoBox.style.display = "block";
                    showToast('Equipment found successfully!', 'success');

                } catch (error) {
                    console.error("Error fetching equipment:", error);
                    eqName.textContent = "Not Found";
                    eqDepartment.textContent = "-";
                    eqStatus.textContent = "Unavailable";
                    eqStatus.className = "badge-status status-unavailable";
                    eqStock.textContent = "0";
                    eqPrice.textContent = "0.00";
                    eqDescription.textContent = error.message || "No data available";
                    infoBox.style.display = "block";
                    showToast(error.message || 'Equipment not found in database', 'error');
                }
            }

            function updateActionButtons(itemData) {
                const returnBtn = document.getElementById('return-btn');

                // Enable return button only if item is "In Use" (condition_id = 6)
                if (itemData.condition_id === 6) {
                    returnBtn.disabled = false;
                    returnBtn.style.opacity = '1';
                    returnBtn.style.cursor = 'pointer';
                } else {
                    returnBtn.disabled = true;
                    returnBtn.style.opacity = '0.6';
                    returnBtn.style.cursor = 'not-allowed';
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
                        showToast(data.message || 'Item borrowed successfully! Condition changed to "In Use"', 'success');
                        fetchEquipmentDetails(currentBarcode); // Refresh data
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                } catch (error) {
                    console.error('Borrow error:', error);
                    showToast('Failed to process borrow request: ' + error.message, 'error');
                }
            }

            // Updated return function - changes condition back to 'Available' and opens update modal
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
                        showToast(data.message || 'Item returned successfully! Condition changed to "Available"', 'success');

                        // Open the update item modal with the returned item data
                        showUpdateItemModal(data.item);

                        fetchEquipmentDetails(currentBarcode); // Refresh data
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
                // Create or show update modal
                let updateModal = document.getElementById('update-item-modal');
                if (!updateModal) {
                    updateModal = document.createElement('div');
                    updateModal.id = 'update-item-modal';
                    updateModal.style.position = 'fixed';
                    updateModal.style.top = '50%';
                    updateModal.style.left = '50%';
                    updateModal.style.transform = 'translate(-50%, -50%)';
                    updateModal.style.backgroundColor = 'white';
                    updateModal.style.padding = '20px';
                    updateModal.style.borderRadius = '10px';
                    updateModal.style.zIndex = '1000';
                    updateModal.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
                    updateModal.style.color = '#012952';
                    updateModal.style.minWidth = '400px';
                    updateModal.style.maxWidth = '90vw';

                    updateModal.innerHTML = `
                        <h4>Update Equipment Item</h4>
                        <form id="update-item-form">
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Item Name:</label>
                                <input type="text" id="item-name" name="item_name" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;" required>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Status:</label>
                                <select id="item-status" name="status_id" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                                    <option value="1">Available</option>
                                    <option value="2">In Use</option>
                                    <option value="3">Under Maintenance</option>
                                    <option value="4">Unavailable</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Condition:</label>
                                <select id="item-condition" name="condition_id" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                                    <option value="1">New</option>
                                    <option value="2">Good</option>
                                    <option value="3">Fair</option>
                                    <option value="4">Needs Maintenance</option>
                                    <option value="5">Damaged</option>
                                    <option value="6">In Use</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Item Notes:</label>
                                <textarea id="item-notes" name="item_notes" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; min-height: 80px;"></textarea>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" id="cancel-update" style="padding: 8px 16px; border: 1px solid #ddd; border-radius: 5px; background: #f8f9fa; cursor: pointer;">Cancel</button>
                                <button type="submit" id="save-update" style="padding: 8px 16px; border: none; border-radius: 5px; background: #28a745; color: white; cursor: pointer;">Save Changes</button>
                            </div>
                        </form>
                    `;

                    document.body.appendChild(updateModal);

                    // Add event listeners
                    document.getElementById('cancel-update').addEventListener('click', () => {
                        document.body.removeChild(updateModal);
                    });

                    document.getElementById('update-item-form').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        await updateItem(itemData.item_id);
                    });
                }

                // Populate form with current item data
                document.getElementById('item-name').value = itemData.item_name || '';
                document.getElementById('item-status').value = itemData.status_id || 1;
                document.getElementById('item-condition').value = itemData.condition_id || 2;
                document.getElementById('item-notes').value = itemData.item_notes || '';

                updateModal.style.display = 'block';
            }

            // Function to update item
            async function updateItem(itemId) {
                try {
                    const formData = {
                        item_name: document.getElementById('item-name').value,
                        status_id: parseInt(document.getElementById('item-status').value),
                        condition_id: parseInt(document.getElementById('item-condition').value),
                        item_notes: document.getElementById('item-notes').value
                    };

                    // Get equipment_id from the stored value
                    const equipmentId = window.currentEquipmentId;

                    if (!equipmentId) {
                        throw new Error('Equipment ID not found. Please scan the item again.');
                    }

                    const response = await fetch(`/api/equipment/${equipmentId}/items/${itemId}`, {
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
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    showToast('Item updated successfully!', 'success');
                    document.body.removeChild(document.getElementById('update-item-modal'));
                    fetchEquipmentDetails(currentBarcode); // Refresh data

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
                        alert(`Item ${action === 'borrow' ? 'borrowed' : 'returned'} successfully!`);
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
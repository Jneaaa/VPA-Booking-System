@extends('layouts.admin')

@section('title', 'Equipment Scanner')

@section('content')

    <style>
        /* ---------- Page styles ---------- */
        body,
        html {
            background: linear-gradient(135deg, #012952ff, #d2d3bfff);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Back button */
        #back-btn {
            display: inline-block;
            margin: 3rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: #fff;
            color: #012952;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transition: 0.15s ease;
        }

        #back-btn:hover {
            background: #e9ecef;
            color: #012952;
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
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 1.5rem;
            width: 100%;
            max-width: 700px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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

        /* Upload Button */
        #uploadInput {
            margin-top: 0.75rem;
            background: #fff;
            color: #012952;
            border: none;
            border-radius: 8px;
            padding: 0.5rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            max-width: 420px;
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

    <main>
        <div id="scannerContainer">
            <!-- Scanner Section -->
            <div class="scanner-box">
                <h2>Start Scanning</h2>
                <p>Use camera or upload a barcode Image / PDF</p>

                <div id="reader"></div>

                <div class="btn-controls">
                    <input type="file" id="uploadInput" accept="image/*,application/pdf">
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
                    eqStatus.textContent = item.availability_status?.status_name || "Unavailable";
                    eqStatus.className = "badge-status " + getStatusClass(item.availability_status?.status_name || "");
                    eqStock.textContent = data.available_stock + " / " + data.total_stock;
                    eqPrice.textContent = equipment.external_fee || "0.00";
                    eqDescription.textContent = equipment.description || "No description";

                    // Show current bookings if any
                    if (data.current_bookings && data.current_bookings.length > 0) {
                        eqDescription.textContent += `\n\nCurrent Bookings:`;
                        data.current_bookings.forEach(booking => {
                            eqDescription.textContent += `\n• ${booking.requester} (${booking.start_date} to ${booking.end_date})`;
                        });
                    }

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

            // Borrow function
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
                            barcode: currentBarcode,
                            quantity: 1,
                            requisition_form_id: 1 // You'll need to get this from somewhere
                        })
                    });

                    const data = await response.json();

                    if (data.status === 'confirmation_required') {
                        // Show confirmation UI with countdown
                        showConfirmationUI('borrow', data.confirmation_timeout);
                    } else if (data.status === 'success') {
                        alert('Item borrowed successfully!');
                        fetchEquipmentDetails(currentBarcode); // Refresh data
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Borrow error:', error);
                    alert('Failed to process borrow request');
                }
            }

            // Return function
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
                            barcode: currentBarcode,
                            quantity: 1
                        })
                    });

                    const data = await response.json();

                    if (data.status === 'confirmation_required') {
                        // Show confirmation UI with countdown
                        showConfirmationUI('return', data.confirmation_timeout);
                    } else if (data.status === 'success') {
                        alert('Item returned successfully!');
                        fetchEquipmentDetails(currentBarcode); // Refresh data
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Return error:', error);
                    alert('Failed to process return request');
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

            // ---------------- File upload / image / PDF scanning ----------------
            uploadInput.addEventListener("change", async function (e) {
                const file = e.target.files[0];
                if (!file) return;

                // If currently scanning via camera, stop to decode file
                if (scannerRunning) {
                    try { await html5QrCode.stop(); } catch (e) { }
                    scannerRunning = false;
                    stopBtn.style.display = "none";
                    resumeBtn.style.display = "inline-block";
                }

                showToast('Processing uploaded image...', 'info');

                try {
                    if (file.type === "application/pdf") {
                        await scanPDFBarcode(file);
                    } else {
                        await scanImageBarcode(file);
                    }
                } catch (error) {
                    console.error('File scanning error:', error);
                    showToast('Failed to process file: ' + error.message, 'error');
                }
            });

            async function scanPDFBarcode(file) {
                try {
                    const pdfjsLib = window['pdfjsLib'];
                    const pdf = await pdfjsLib.getDocument(URL.createObjectURL(file)).promise;
                    const page = await pdf.getPage(1);
                    const viewport = page.getViewport({ scale: 3.0 });
                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    await page.render({
                        canvasContext: ctx,
                        viewport: viewport
                    }).promise;

                    const barcodeData = await scanWithQuagga(canvas.toDataURL());

                    if (barcodeData) {
                        await onScanSuccess(barcodeData);
                    } else {
                        showToast("No barcode found in PDF. Try a clearer image.", "error");
                    }
                } catch (err) {
                    console.error("PDF decode error:", err);
                    showToast("Failed to decode PDF file.", "error");
                }
            }

            async function scanImageBarcode(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = async function () {
                        try {
                            const img = new Image();
                            img.onload = async function () {
                                console.log('Image loaded:', img.width, 'x', img.height);

                                // Try scanning with original image
                                let barcodeData = await scanWithQuagga(reader.result);

                                if (!barcodeData) {
                                    // Try with image preprocessing
                                    barcodeData = await scanWithImagePreprocessing(img);
                                }

                                if (barcodeData) {
                                    await onScanSuccess(barcodeData);
                                    resolve(true);
                                } else {
                                    showToast("No barcode detected. Tips:\n• Use high-contrast barcodes\n• Ensure good lighting\n• Crop to just the barcode\n• Save as PNG for best quality", "error");
                                    resolve(false);
                                }
                            };
                            img.src = reader.result;
                        } catch (error) {
                            console.error("Image processing error:", error);
                            showToast("Error processing image file.", "error");
                            resolve(false);
                        }
                    };
                    reader.onerror = function () {
                        showToast("Failed to read image file.", "error");
                        resolve(false);
                    };
                    reader.readAsDataURL(file);
                });
            }

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
@extends('layouts.admin')

@section('title', 'Equipment Scanner')

@section('content')

<style>
body, html {
    height: 100%;
    margin: 0;
    background: linear-gradient(135deg, #012952ff, #d2d3bfff);
    color: #fff;
    font-family: 'Segoe UI', sans-serif;
}

#scannerContainer {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
}

.scanner-box {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 1.5rem;
    width: 100%;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

#reader {
    width: 100%;
    max-width: 350px;
    margin: auto;
    border: 3px solid #fff;
    border-radius: 12px;
    overflow: hidden;
}

/* Upload Button */
#uploadInput {
    margin-top: 1rem;
    background: #fff;
    color: #012952;
    border: none;
    border-radius: 8px;
    padding: 0.5rem;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    max-width: 350px;
}

/* Info Box */
.info-box {
    background: #fff;
    color: #333;
    border-radius: 16px 16px 0 0;
    padding: 1.5rem;
    width: 100%;
    max-width: 500px;
    margin-top: auto;
    box-shadow: 0 -6px 20px rgba(0,0,0,0.1);
}
.info-label { font-weight: bold; }
.info-value { float: right; }
.badge-status { padding: 0.3rem 0.75rem; border-radius: 12px; }
.status-available { background: #28a745; color: white; }
.status-used { background: #ffc107; color: #222; }
.status-maintenance { background: #17a2b8; color: white; }
.status-unavailable { background: #dc3545; color: white; }

@media(max-width: 576px) {
    .scanner-box, .info-box { padding: 1rem; }
}
</style>

<div id="scannerContainer">
    <!-- Scanner Section -->
    <div class="scanner-box">
        <h2>Start Scanning</h2>
        <p>Use camera or upload a barcode Image/PDF</p>
        <div id="reader"></div>
        <input type="file" id="uploadInput" accept="image/*,application/pdf">
        <div id="scan-result" class="scan-result mt-3">Scanned Code: <span id="scanned-value">None</span></div>
    </div>

    <!-- Equipment Details Section -->
    <div class="info-box" id="equipment-info" style="display:none;">
        <h5>Equipment Details</h5>
        <div class="info-item"><span class="info-label">Name:</span> <span class="info-value" id="eq-name"></span></div>
        <div class="info-item"><span class="info-label">Department:</span> <span class="info-value" id="eq-department"></span></div>
        <div class="info-item"><span class="info-label">Status:</span> <span class="info-value"><span id="eq-status" class="badge-status"></span></span></div>
        <div class="info-item"><span class="info-label">Available Stock:</span> <span class="info-value" id="eq-stock"></span></div>
        <div class="info-item"><span class="info-label">Price:</span> <span class="info-value">₱<span id="eq-price"></span></span></div>
        <div class="info-item"><span class="info-label">Description:</span> <span class="info-value" id="eq-description"></span></div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Scanner libraries -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const resultSpan = document.getElementById("scanned-value");
    const infoBox = document.getElementById("equipment-info");
    const uploadInput = document.getElementById("uploadInput");

    const eqName = document.getElementById("eq-name");
    const eqDepartment = document.getElementById("eq-department");
    const eqStatus = document.getElementById("eq-status");
    const eqStock = document.getElementById("eq-stock");
    const eqPrice = document.getElementById("eq-price");
    const eqDescription = document.getElementById("eq-description");

    const token = localStorage.getItem("adminToken");

    function getStatusClass(status) {
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
            const response = await fetch(`http://127.0.0.1:8000/api/equipment/${code}`, {
                headers: { Authorization: `Bearer ${token}`, Accept: "application/json" }
            });

            if (!response.ok) throw new Error("Not found");
            const data = await response.json();
            const eq = data.data;

            eqName.textContent = eq.equipment_name;
            eqDepartment.textContent = eq.department?.department_name || "N/A";
            eqStatus.textContent = eq.status.status_name;
            eqStatus.className = "badge-status " + getStatusClass(eq.status.status_name);
            eqStock.textContent = eq.stock || "0";
            eqPrice.textContent = eq.price || "0.00";
            eqDescription.textContent = eq.description || "No description";

            infoBox.style.display = "block";
        } catch (error) {
            eqName.textContent = "Not Found";
            eqDepartment.textContent = "-";
            eqStatus.textContent = "Unavailable";
            eqStatus.className = "badge-status status-unavailable";
            eqStock.textContent = "0";
            eqPrice.textContent = "0.00";
            eqDescription.textContent = "No data available";
            infoBox.style.display = "block";
        }
    }

    function onScanSuccess(decodedText) {
        resultSpan.textContent = decodedText;
        fetchEquipmentDetails(decodedText);
    }

    // Camera Scanner
    const html5QrCode = new Html5Qrcode("reader");
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                onScanSuccess
            ).catch(err => console.error("Scanner error:", err));
        }
    });

    // File Upload Scanner
    uploadInput.addEventListener("change", async function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.type === "application/pdf") {
            // Decode barcode from PDF (first page)
            const pdf = await pdfjsLib.getDocument(URL.createObjectURL(file)).promise;
            const page = await pdf.getPage(1);
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const viewport = page.getViewport({ scale: 2 });
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            await page.render({ canvasContext: ctx, viewport }).promise;

            Quagga.decodeSingle({
                src: canvas.toDataURL(),
                numOfWorkers: 0,
                inputStream: { size: 800 },
                decoder: { readers: ["code_128_reader", "ean_reader", "upc_reader"] }
            }, function(result) {
                if (result && result.codeResult) onScanSuccess(result.codeResult.code);
                else alert("No barcode found in PDF");
            });

        } else {
            // Decode barcode from image
            const reader = new FileReader();
            reader.onload = function () {
                Quagga.decodeSingle({
                    src: reader.result,
                    numOfWorkers: 0,
                    inputStream: { size: 800 },
                    decoder: { readers: ["code_128_reader", "ean_reader", "upc_reader"] }
                }, function(result) {
                    if (result && result.codeResult) onScanSuccess(result.codeResult.code);
                    else alert("No barcode found in image");
                });
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection

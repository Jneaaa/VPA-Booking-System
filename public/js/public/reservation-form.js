document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const facilityList = document.getElementById('facilityList');
    const equipmentList = document.getElementById('equipmentList');
    const feeDisplay = document.getElementById('feeDisplay');
    const submitBtn = document.getElementById('submitFormBtn');
    const checkAvailabilityBtn = document.getElementById('checkAvailabilityBtn');
    const attachLetter = document.getElementById('attachLetter');
    const availabilityResult = document.getElementById('availabilityResult');
    const applicantType = document.getElementById('applicantType');
    const studentIdField = document.getElementById('studentIdField');

    // Initialize the form
    initForm();

    // Toggle student ID field based on applicant type
    if (applicantType) {
        applicantType.addEventListener('change', function() {
            if (this.value === 'Internal') {
                studentIdField.style.display = 'block';
            } else {
                studentIdField.style.display = 'none';
            }
        });
    }

    async function initForm() {
        try {
            // Fetch and cache item details first
            await Promise.all([
                fetchItemDetails('facilities'),
                fetchItemDetails('equipment')
            ]);

            // Then load everything else
            await Promise.all([
                renderSelectedItems(),
                renderTotalFees(),
                loadPurposes()
            ]);

            // Set up event listeners
            setupEventListeners();
            
            // Initialize date and time pickers
            initDateTimePickers();

            // Start auto-refresh
            startAutoRefresh();

        } catch (error) {
            console.error('Error initializing form:', error);
            showToast('Failed to initialize form', 'error');
        }
    }

    function setupEventListeners() {
        if (checkAvailabilityBtn) {
            checkAvailabilityBtn.addEventListener('click', checkAvailability);
        }
        
        if (attachLetter) {
            attachLetter.addEventListener('change', tempUploadFormalLetter);
        }
        
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                validateForm().then(isValid => {
                    if (isValid) showPolicyModal(submitForm);
                });
            });
        }
        
        // Listen for changes from catalog pages
        window.addEventListener('storage', async (e) => {
            if (e.key === 'formUpdated') {
                // Wait a bit for the server to process the changes
                await new Promise(resolve => setTimeout(resolve, 150));
                
                // Fetch latest data and update UI
                await Promise.all([
                    renderSelectedItems(),
                    renderTotalFees()
                ]);
            }
        });

        const toggleReservationBtn = document.getElementById('toggleReservationBtn');
        if (toggleReservationBtn) {
            toggleReservationBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const card = document.querySelector('.form-section-card');
                const content = document.getElementById('reservationContent');
                const chevron = toggleReservationBtn.querySelector('.bi');
                
                card.classList.toggle('collapsed');
                chevron.classList.toggle('rotated');
                
                // Save state to localStorage
                const isCollapsed = card.classList.contains('collapsed');
                localStorage.setItem('reservationSectionCollapsed', isCollapsed);
            });
            
            // Restore previous state
            const wasCollapsed = localStorage.getItem('reservationSectionCollapsed') === 'true';
            if (wasCollapsed) {
                const card = document.querySelector('.form-section-card');
                const chevron = toggleReservationBtn.querySelector('.bi');
                card.classList.add('collapsed');
                chevron.classList.add('rotated');
            }
        }
    }

    function initDateTimePickers() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('startDateField').min = today;
        document.getElementById('endDateField').min = today;

        // Set default times
        document.getElementById('startTimeField').value = '08:00 AM';
        document.getElementById('endTimeField').value = '05:00 PM';
    }

    async function loadPurposes() {
        try {
            const response = await fetch('/api/requisition-purposes');
            if (!response.ok) throw new Error('Failed to load purposes');
            
            const data = await response.json();
            const purposeField = document.getElementById('activityPurposeField');
            
            purposeField.innerHTML = '<option selected disabled>Select Activity/Purpose</option>';
            data.forEach(purpose => {
                const option = document.createElement('option');
                option.value = purpose.purpose_id;
                option.textContent = purpose.purpose_name;
                purposeField.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading purposes:', error);
            document.getElementById('activityPurposeField').innerHTML = 
                '<option disabled>Error loading purposes</option>';
        }
    }

    async function validateForm() {
        // Basic validation
        const requiredFields = [
            'applicantType', 'first_name', 'last_name', 'email',
            'num_participants', 'purpose_id', 'start_date', 'end_date',
            'start_time', 'end_time'
        ];
        
        let isValid = true;
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId) || 
                          document.querySelector(`[name="${fieldId}"]`);
            if (!field || !field.value) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Check if any items are selected
        const response = await fetch('/api/requisition/calculate-fees', { credentials: 'same-origin' });
        const data = await response.json();
        if (data.data?.selected_items?.length === 0) {
            showToast('Please add at least one facility or equipment', 'error');
            isValid = false;
        }

        return isValid;
    }

    async function renderSelectedItems() {
        try {
            const response = await fetch('/api/requisition/calculate-fees', { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Failed to fetch selected items');
            
            const data = await response.json();
            const items = data.data?.selected_items || [];
            
            // Render facilities
            renderItemsList(facilityList, items.filter(i => i.type === 'facility'), 'facility');
            
            // Render equipment
            renderItemsList(equipmentList, items.filter(i => i.type === 'equipment'), 'equipment');
            
        } catch (error) {
            console.error('Error rendering selected items:', error);
            showToast('Failed to load selected items', 'error');
        }
    }

    // Cache for facilities and equipment data
    let facilitiesCache = new Map();
    let equipmentCache = new Map();

    // Fetch and cache facility/equipment data
    async function fetchItemDetails(type) {
        try {
            const response = await fetch(`/api/${type}`);
            const data = await response.json();
            
            data.data.forEach(item => {
                const cache = type === 'facilities' ? facilitiesCache : equipmentCache;
                cache.set(item[`${type.slice(0, -3)}_id`], item);
            });
            
            return data.data;
        } catch (error) {
            console.error(`Error fetching ${type}:`, error);
            return [];
        }
    }

    // Update renderItemsList function
    async function renderItemsList(container, items, type) {
        if (!container) return;
        
        container.innerHTML = '';
        
        if (items.length === 0) {
            container.innerHTML = `<div class="text-muted empty-message">No ${type}s added yet.</div>`;
            return;
        }

        try {
            // Fetch current data from API
            const response = await fetch(`/api/${type}s`);
            if (!response.ok) throw new Error(`Failed to fetch ${type} data`);
            const apiData = await response.json();
            
            // Create a map for quick lookup
            const itemsMap = new Map(apiData.data.map(item => [
                item[`${type}_id`], 
                item
            ]));

            items.forEach(item => {
                const itemDetails = itemsMap.get(parseInt(item.id));
                if (!itemDetails) return;

                const card = document.createElement('div');
                card.className = 'selected-item-card';
                
                card.innerHTML = `
                    <div class="selected-item-details">
                        <h6>${itemDetails[`${type}_name`]}</h6>
                        <div class="fee">₱${parseFloat(itemDetails.external_fee).toLocaleString('en-US', {minimumFractionDigits: 2})} per ${itemDetails.rate_type || 'booking'}</div>
                        ${type === 'equipment' ? `
                            <div class="quantity-control">
                                <span class="text-muted">Quantity: ${item.quantity || 1}</span>
                            </div>
                        ` : ''}
                    </div>
                    <button type="button" class="delete-item-btn" onclick="removeSelectedItem('${item.id}', '${type}')">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                
                container.appendChild(card);
            });
        } catch (error) {
            console.error(`Error rendering ${type} list:`, error);
            showToast(`Failed to load ${type} details`, 'error');
        }
    }

    // Update removeSelectedItem function to trigger storage event
    async function removeSelectedItem(id, type) {
        try {
            const response = await fetch('/api/requisition/remove-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    [`${type}_id`]: id,
                    type: type
                })
            });

            if (!response.ok) {
                throw new Error('Failed to remove item');
            }

            // Re-render items and update fees
            await Promise.all([
                renderSelectedItems(),
                renderTotalFees()
            ]);

            // Trigger storage event for cross-page sync
            localStorage.setItem('formUpdated', Date.now().toString());

            showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} removed successfully`, 'success');
        } catch (error) {
            console.error('Error removing item:', error);
            showToast('Failed to remove item', 'error');
        }
    }

    // Add auto-refresh function to keep data in sync
    function startAutoRefresh() {
        setInterval(async () => {
            try {
                await Promise.all([
                    renderSelectedItems(),
                    renderTotalFees()
                ]);
            } catch (error) {
                console.error('Error refreshing data:', error);
            }
        }, 5000); // Refresh every 5 seconds
    }

    async function renderTotalFees() {
        if (!feeDisplay) return;
        
        // Show loading state
        feeDisplay.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';

        try {
            // Fetch selected items and fees
            const [feesResponse, facilitiesResponse, equipmentResponse] = await Promise.all([
                fetch('/api/requisition/calculate-fees', { credentials: 'same-origin' }),
                fetch('/api/facilities'),
                fetch('/api/equipment')
            ]);

            if (!feesResponse.ok || !facilitiesResponse.ok || !equipmentResponse.ok) {
                throw new Error('Failed to fetch required data');
            }

            const feesData = await feesResponse.json();
            const facilitiesData = await facilitiesResponse.json();
            const equipmentData = await equipmentResponse.json();

            // Create lookup maps
            const facilityMap = new Map(facilitiesData.data.map(f => [f.facility_id, f]));
            const equipmentMap = new Map(equipmentData.data.map(e => [e.equipment_id, e]));

            const feeSummary = feesData.data?.fee_summary || {};
            const items = feesData.data?.selected_items || [];

            let htmlContent = '<div class="fee-items">';

            // Add facilities breakdown
            const facilities = items.filter(i => i.type === 'facility');
            if (facilities.length > 0) {
                htmlContent += '<div class="fee-section"><h6 class="mb-3">Facilities</h6>';
                facilities.forEach(facility => {
                    const facilityDetails = facilityMap.get(parseInt(facility.id));
                    if (!facilityDetails) return;

                    htmlContent += `
                        <div class="fee-item d-flex justify-content-between mb-2">
                            <span>${facilityDetails.facility_name}</span>
                            <span>₱${parseFloat(facilityDetails.external_fee).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </div>
                    `;
                });
                htmlContent += `
                    <div class="subtotal d-flex justify-content-between mt-2 pt-2 border-top">
                        <strong>Subtotal</strong>
                        <strong>₱${feeSummary.facilityTotalFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>
                    </div>
                </div>`;
            }
            
            // Add equipment breakdown
            const equipment = items.filter(i => i.type === 'equipment');
            if (equipment.length > 0) {
                htmlContent += '<div class="fee-section mt-3"><h6 class="mb-3">Equipment</h6>';
                equipment.forEach(equip => {
                    const equipDetails = equipmentMap.get(parseInt(equip.id));
                    if (!equipDetails) return;

                    const itemTotal = parseFloat(equipDetails.external_fee) * (equip.quantity || 1);
                    htmlContent += `
                        <div class="fee-item d-flex justify-content-between mb-2">
                            <span>${equipDetails.equipment_name} ${equip.quantity ? `(x${equip.quantity})` : ''}</span>
                            <div class="text-end">
                                <div>₱${parseFloat(equipDetails.external_fee).toLocaleString('en-US', {minimumFractionDigits: 2})} × ${equip.quantity || 1}</div>
                                <strong>₱${itemTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>
                            </div>
                        </div>
                    `;
                });
                htmlContent += `
                    <div class="subtotal d-flex justify-content-between mt-2 pt-2 border-top">
                        <strong>Subtotal</strong>
                        <strong>₱${feeSummary.equipmentTotalFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>
                    </div>
                </div>`;
            }
            
            // Add total
            if (facilities.length > 0 || equipment.length > 0) {
                htmlContent += `
                    <div class="total-fee d-flex justify-content-between mt-4 pt-3 border-top">
                        <h6 class="mb-0">Total Amount</h6>
                        <h6 class="mb-0">₱${feeSummary.totalFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</h6>
                    </div>
                `;
            } else {
                htmlContent += '<div class="text-muted text-center">No items added yet.</div>';
            }

            htmlContent += '</div>';
            feeDisplay.innerHTML = htmlContent;

        } catch (error) {
            console.error('Error rendering total fees:', error);
            showToast('Failed to load fee summary', 'error');
            feeDisplay.innerHTML = '<div class="alert alert-danger">Error loading fee breakdown</div>';
        }
    }

    async function checkAvailability() {
        const startDate = document.getElementById('startDateField').value;
        const endDate = document.getElementById('endDateField').value;
        const startTime = document.getElementById('startTimeField').value;
        const endTime = document.getElementById('endTimeField').value;
        
        if (!startDate || !endDate || !startTime || !endTime) {
            showToast('Please select a complete schedule', 'error');
            return;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const response = await fetch('/api/requisition/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate,
                    start_time: convertTo24Hour(startTime),
                    end_time: convertTo24Hour(endTime)
                })
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to check availability');
            }

            if (data.data?.available) {
                availabilityResult.innerHTML = `
                    <i class="bi bi-check-circle-fill text-success"></i> 
                    <span class="text-success">Time slot is available.</span>
                `;
            } else {
                availabilityResult.innerHTML = `
                    <i class="bi bi-x-circle-fill text-danger"></i> 
                    <span class="text-danger">Time slot conflicts with existing booking.</span>
                `;
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            showToast(error.message || 'Error checking availability', 'error');
            if (availabilityResult) {
                availabilityResult.innerHTML = `
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i> 
                    <span class="text-warning">Error checking availability</span>
                `;
            }
        }
    }

    function convertTo24Hour(time12h) {
        if (!time12h) return '';
        
        const [time, modifier] = time12h.split(' ');
        let [hours, minutes] = time.split(':');
        
        // Convert hours to number for calculation
        hours = parseInt(hours, 10);
        
        if (hours === 12) {
            hours = 0;
        }
        
        if (modifier === 'PM') {
            hours = hours + 12;
        }
        
        // Convert back to string and ensure 2 digits
        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    async function tempUploadFormalLetter() {
        const file = attachLetter.files[0];
        if (!file) return;

        // Validate file type and size
        const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!validTypes.includes(file.type)) {
            showToast('Please upload a JPG, PNG, or PDF file', 'error');
            return;
        }
        
        if (file.size > maxSize) {
            showToast('File size must be less than 5MB', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('formal_letter_url', file);

        try {
            showToast('Uploading file...', 'info');
            
            const response = await fetch('/api/requisition/temp-upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Upload failed');
            }

            showToast('Formal letter uploaded successfully');
        } catch (error) {
            console.error('Error uploading file:', error);
            showToast(error.message || 'Error uploading file', 'error');
            attachLetter.value = '';
        }
    }

    function showPolicyModal(onAccept) {
        const modalId = 'policyModal';
        let modal = document.getElementById(modalId);
        
        if (!modal) {
            modal = document.createElement('div');
            modal.id = modalId;
            modal.className = 'modal fade';
            modal.tabIndex = -1;
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Reservation Terms & Policies</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Please read and accept the following policies before submitting your reservation:</p>
                            <ul class="policy-list">
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Ensure your booking schedule doesn't conflict with existing bookings</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Return all borrowed items on time to avoid penalties</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Abide by all university policies regarding facility use</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Cancellation must be done at least 5 days before the scheduled date</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Any damages will be charged to the requesting party</li>
                            </ul>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="acceptPolicies">
                                <label class="form-check-label" for="acceptPolicies">
                                    I have read and accept the terms and policies.
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="policyAcceptBtn" class="btn btn-primary" disabled>Accept & Submit</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        const bsModal = new bootstrap.Modal(modal);
        
        // Reset the checkbox when modal is shown
        modal.querySelector('#acceptPolicies').checked = false;
        modal.querySelector('#policyAcceptBtn').disabled = true;
        
        modal.querySelector('#acceptPolicies').onchange = function() {
            modal.querySelector('#policyAcceptBtn').disabled = !this.checked;
        };
        
        modal.querySelector('#policyAcceptBtn').onclick = function() {
            bsModal.hide();
            onAccept();
        };
        
        bsModal.show();
    }

    async function submitForm() {
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Submitting...
            `;

            // Gather form data
            const formData = {
                user_type: document.getElementById('applicantType').value,
                first_name: document.querySelector('input[name="first_name"]').value,
                last_name: document.querySelector('input[name="last_name"]').value,
                email: document.querySelector('input[type="email"]').value,
                contact_number: document.querySelector('input[name="contact_number"]').value,
                organization_name: document.querySelector('input[name="organization_name"]').value,
                school_id: document.querySelector('input[name="school_id"]')?.value || '',
                num_participants: document.querySelector('input[type="number"]').value,
                purpose_id: document.getElementById('activityPurposeField').value,
                additional_requests: document.querySelector('textarea').value,
                start_date: document.getElementById('startDateField').value,
                end_date: document.getElementById('endDateField').value,
                start_time: convertTo24Hour(document.getElementById('startTimeField').value),
                end_time: convertTo24Hour(document.getElementById('endTimeField').value),
                endorser: document.querySelector('input[name="endorser"]')?.value || null,
                date_endorsed: document.querySelector('input[name="date_endorsed"]')?.value || null
            };

            // First save request info
            const saveResponse = await fetch('/api/requisition/save-request-info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData)
            });

            const saveData = await saveResponse.json();
            
            if (!saveResponse.ok) {
                throw new Error(saveData.message || 'Failed to save request info');
            }

            // Then submit the form
            const submitResponse = await fetch('/api/requisition/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData)
            });

            const submitData = await submitResponse.json();
            
            if (!submitResponse.ok) {
                throw new Error(submitData.message || 'Submission failed');
            }

            // Show success and redirect
            showToast('Form submitted successfully!', 'success');
            
            setTimeout(() => {
                window.location.href = `/your-bookings?access_code=${submitData.data.access_code}`;
            }, 1500);

        } catch (error) {
            console.error('Error submitting form:', error);
            showToast(error.message || 'Error submitting form', 'error');
            
            // Reset submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Form';
        }
    }

    function showToast(message, type = 'success') {
        // Remove existing toasts
        document.querySelectorAll('.toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '1100';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
});
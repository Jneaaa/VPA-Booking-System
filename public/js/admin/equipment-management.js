// public/js/equipment-management.js

// Global equipment management functions
window.EquipmentManagement = {
    // Global variables
    equipmentItems: [],
    currentEditingItemId: null,
    pendingImageUploads: [],
    pendingImageDeletions: [],
    pendingItemPhotoChanges: new Map(),
    newEquipmentId: null,
    pendingDeleteItem: null,

    // Toast notification function
    showToast: function(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');

        // Toast base styles
        toast.className = `toast align-items-center border-0 position-fixed start-0 mb-2`;
        toast.style.zIndex = '1100';
        toast.style.bottom = '0';
        toast.style.left = '0';
        toast.style.margin = '1rem';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        toast.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        // Colors
        const bgColor = type === 'success' ? '#004183ff' : '#dc3545';
        toast.style.backgroundColor = bgColor;
        toast.style.color = '#fff';
        toast.style.minWidth = '250px';
        toast.style.borderRadius = '0.3rem';

        toast.innerHTML = `
            <div class="d-flex align-items-center px-3 py-1"> 
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
                <div class="toast-body flex-grow-1" style="padding: 0.25rem 0;">${message}</div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="loading-bar" style="
                height: 3px;
                background: rgba(255,255,255,0.7);
                width: 100%;
                transition: width ${duration}ms linear;
            "></div>
        `;

        document.body.appendChild(toast);

        // Bootstrap toast instance
        const bsToast = new bootstrap.Toast(toast, { autohide: false });
        bsToast.show();

        // Float up appear animation
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        // Start loading bar animation
        const loadingBar = toast.querySelector('.loading-bar');
        requestAnimationFrame(() => {
            loadingBar.style.width = '0%';
        });

        // Remove after duration
        setTimeout(() => {
            // Float down disappear animation
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';

            setTimeout(() => {
                bsToast.hide();
                toast.remove();
            }, 400);
        }, duration);
    },

    // Delete item function
    deleteItem: async function(itemId, cloudinaryPublicId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Convert itemId to string for consistent comparison
        const itemIdStr = itemId.toString();

        // Store the item details for deletion
        this.pendingDeleteItem = {
            itemId: itemIdStr,
            publicId: cloudinaryPublicId
        };

        // Show the confirmation modal
        const deleteItemModal = new bootstrap.Modal('#deleteItemModal');
        deleteItemModal.show();
    },

    // Open edit item modal function
    openEditItemModal: function(itemId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Convert itemId to string for consistent comparison
        const itemIdStr = itemId.toString();
        const item = this.equipmentItems.find(i => i.item_id.toString() === itemIdStr);

        if (!item) {
            console.error('Item not found:', itemId);
            return;
        }

        // Fill the form with existing data
        document.getElementById('itemName').value = item.item_name;
        document.getElementById('itemCondition').value = item.condition_id;
        document.getElementById('barcode').value = item.barcode_number || '';
        document.getElementById('itemNotes').value = item.item_notes || '';

        document.getElementById('barcode').setAttribute('readonly', true);

        // Set the photo preview
        const itemPhotoPreview = document.getElementById('itemPhotoPreview');
        const itemPhotoDropzone = document.getElementById('itemPhotoDropzone');
        const removePhotoBtn = document.getElementById('removePhotoBtn');

        if (item.image_url && item.image_url !== 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp') {
            itemPhotoPreview.innerHTML = `<img src="${item.image_url}" class="img-thumbnail" style="max-height: 150px;">`;
            itemPhotoDropzone.style.display = 'none';
            removePhotoBtn.classList.remove('d-none');
        } else {
            itemPhotoPreview.innerHTML = '';
            itemPhotoDropzone.style.display = 'block';
            removePhotoBtn.classList.add('d-none');
        }

        // Set the barcode field and handle download button
        const barcodeInput = document.getElementById('barcode');
        const downloadExistingBarcodeBtn = document.getElementById('downloadExistingBarcodeBtn');
        const generateBarcodeBtn = document.getElementById('generateBarcodeBtn');
        const barcodeContainer = document.getElementById('barcodeContainer');

        if (item.barcode_number) {
            barcodeInput.value = item.barcode_number;
            barcodeInput.setAttribute('readonly', true);

            // Show download button for existing barcode
            if (downloadExistingBarcodeBtn) {
                downloadExistingBarcodeBtn.classList.remove('d-none');
            }

            // Hide generate button in edit mode when barcode exists
            if (generateBarcodeBtn) {
                generateBarcodeBtn.style.display = 'none';
            }

            // Hide barcode container (for newly generated barcodes)
            if (barcodeContainer) {
                barcodeContainer.classList.add('d-none');
            }
        } else {
            // No existing barcode
            barcodeInput.value = '';
            if (downloadExistingBarcodeBtn) {
                downloadExistingBarcodeBtn.classList.add('d-none');
            }
        }

        // Store the current editing item ID
        this.currentEditingItemId = itemId;

        // Change modal title and button text for editing
        document.getElementById('inventoryItemModalTitle').textContent = 'Edit Inventory Item';
        document.getElementById('saveItemBtn').textContent = 'Update Item';

        // Show the modal
        bootstrap.Modal.getOrCreateInstance('#inventoryItemModal').show();
    },

    // Cloudinary upload functions
    uploadToCloudinary: async function(file, equipmentId) {
        const CLOUD_NAME = 'dn98ntlkd';
        const UPLOAD_PRESET = 'equipment-photos';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('upload_preset', UPLOAD_PRESET);
        formData.append('folder', `equipment-photos/${equipmentId}`);
        formData.append('tags', `equipment_${equipmentId}`);

        try {
            const response = await fetch(`https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error?.message || 'Upload failed');
            }

            const data = await response.json();
            console.log('Cloudinary upload successful:', data);
            return data;

        } catch (error) {
            console.error('Cloudinary upload error:', error);
            this.showToast('Cloudinary upload failed: ' + error.message, 'error');
            throw error;
        }
    },

    // Save image to database
    saveImageToDatabase: async function(equipmentId, imageUrl, publicId) {
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/images/save`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    image_url: imageUrl,
                    cloudinary_public_id: publicId,
                    description: 'Equipment photo'
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Database save failed:', response.status, errorText);
                throw new Error(`Failed to save image to database: ${response.status} ${errorText}`);
            }

            const result = await response.json();
            console.log('Image saved to database:', result);
            return result;

        } catch (error) {
            console.error('Error saving image to database:', error);
            this.showToast('Warning: Image uploaded but database save failed', 'warning');
            throw error;
        }
    },

    // Delete image from Cloudinary
    deleteImageFromCloudinary: async function(publicId) {
        try {
            const token = localStorage.getItem('adminToken');

            const formData = new FormData();
            formData.append('public_id', publicId);

            const response = await fetch(`http://127.0.0.1:8000/api/admin/cloudinary/delete`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Cloudinary delete failed:', response.status, errorText);
                throw new Error(`Failed to delete image from Cloudinary: ${response.status} ${errorText}`);
            }

            const result = await response.json();

            if (result.result && (result.result.deleted || result.result === 'ok' || result.result === 'success')) {
                return result;
            } else {
                console.warn('Unexpected Cloudinary response format:', result);
                return result;
            }

        } catch (error) {
            console.error('Error deleting image from Cloudinary:', error);
            this.showToast('Failed to delete from storage: ' + error.message, 'error');
            throw error;
        }
    },

    // Upload item to Cloudinary
    uploadItemToCloudinary: async function(file, equipmentId) {
        const CLOUD_NAME = 'dn98ntlkd';
        const UPLOAD_PRESET = 'equipment-photos';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('upload_preset', UPLOAD_PRESET);
        formData.append('folder', `equipment-items/${equipmentId}`);
        formData.append('tags', `equipment_item_${equipmentId}`);

        try {
            const response = await fetch(`https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error?.message || 'Upload failed');
            }

            return await response.json();

        } catch (error) {
            console.error('Cloudinary upload error:', error);
            throw new Error('Failed to upload image: ' + error.message);
        }
    },

    // Check if items container is empty
    checkItemsContainerEmpty: function() {
        const itemsContainer = document.getElementById('itemsContainer');
        const itemsEmptyState = document.getElementById('itemsEmptyState');
        
        if (!itemsContainer || !itemsEmptyState) return;

        const hasItems = itemsContainer.querySelector('.equipment-item');
        
        if (!hasItems) {
            itemsContainer.classList.add('d-none');
            itemsEmptyState.classList.remove('d-none');
        } else {
            itemsContainer.classList.remove('d-none');
            itemsEmptyState.classList.add('d-none');
        }
    },

    // Initialize common functionality
    initializeCommon: function() {
        // Authentication check
        const token = localStorage.getItem('adminToken');
        if (!token) {
            window.location.href = '/admin/admin-login';
            return;
        }

        // Initialize delete item modal handler
        const deleteItemModal = bootstrap.Modal.getOrCreateInstance('#deleteItemModal');
        document.getElementById('confirmDeleteItemBtn').addEventListener('click', async function() {
            if (!window.EquipmentManagement.pendingDeleteItem) return;

            const { itemId, publicId } = window.EquipmentManagement.pendingDeleteItem;

            try {
                // Mark for deletion (will be processed on form submit)
                window.EquipmentManagement.pendingItemPhotoChanges.set(itemId, {
                    action: 'delete',
                    publicId: publicId
                });

                // Remove from UI immediately
                document.querySelector(`.equipment-item[data-item-id="${itemId}"]`)?.remove();

                // Hide the modal FIRST
                deleteItemModal.hide();

                // Clear the pending delete
                window.EquipmentManagement.pendingDeleteItem = null;

                // Check if container is empty and show empty state
                window.EquipmentManagement.checkItemsContainerEmpty();

                window.EquipmentManagement.showToast('Item marked for deletion. Click "Update Equipment" to confirm.', 'info');

            } catch (error) {
                console.error('Error staging item deletion:', error);
                window.EquipmentManagement.showToast('Failed to stage item deletion: ' + error.message, 'error');
            }
        });

        // Initialize image deletion modal
        const deleteImageModal = new bootstrap.Modal('#deleteImageModal', {
            backdrop: 'static',
            keyboard: false
        });

        document.getElementById('confirmDeleteImageBtn').addEventListener('click', async function() {
            try {
                const equipmentId = document.getElementById('equipmentId')?.value;

                // Delete from Cloudinary first if public ID exists
                if (window.EquipmentManagement.currentDeletePublicId) {
                    await window.EquipmentManagement.deleteImageFromCloudinary(window.EquipmentManagement.currentDeletePublicId);
                }

                // Then delete from database if it's a saved image (has photoId)
                if (window.EquipmentManagement.currentDeletePhotoId && typeof window.EquipmentManagement.currentDeletePhotoId === 'number') {
                    // This would be handled in the specific form submission
                }

                // Remove the preview element
                if (window.EquipmentManagement.currentDeletePreviewElement) {
                    window.EquipmentManagement.currentDeletePreviewElement.remove();
                }

                // Update the uploadedPhotos array
                window.EquipmentManagement.uploadedPhotos = window.EquipmentManagement.uploadedPhotos.filter(
                    photo => photo.id !== window.EquipmentManagement.currentDeletePhotoId
                );

                // Hide the modal
                deleteImageModal.hide();

            } catch (error) {
                console.error('Error deleting image:', error);
                window.EquipmentManagement.showToast('Failed to delete image: ' + error.message, 'error');
            }
        });
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.EquipmentManagement.initializeCommon();
});
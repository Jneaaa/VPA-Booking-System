@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
    <style>
        #department-buttons-container button {
            display: inline-flex !important;
            width: auto !important;
        }

        /* Ensure proper column sizing */
        #password-full-width-container .form-control,
        #password-half-width-container .form-control {
            width: 100%;
        }
    </style>

    <div class="profile-wrapper position-relative">
        <!-- Hero/Wallpaper Section -->
        <div class="profile-hero position-relative mb-5" style="height: 200px; background-color: #f8f9fa;">
            <div id="wallpaper-container" class="w-100 h-100" style="background: url('https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033948/verzp7lqedwsfn3hz8xf.jpg')
                                                                    center center / cover no-repeat;">
                <!-- Wallpaper will be loaded here -->
            </div>
            <button class="btn btn-light position-absolute bottom-0 end-0 m-3">
                <i class="bi bi-image me-2"></i>Change Cover
            </button>
        </div>
        <input type="file" id="wallpaper-upload" class="d-none" accept="image/*">

        <div class="container position-relative">
            <!-- Profile Avatar -->
            <div class="position-absolute" style="top: -70px; left: 50px; z-index: 10;">
                <div class="position-relative">
                    <div class="avatar-container rounded-circle border border-4 border-white"
                        style="width: 150px; height: 150px; overflow: hidden;">
                        <img id="profile-photo"
                            src="{{ url('https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png') }}"
                            class="w-100 h-100 object-fit-cover">
                    </div>
                    <button class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm"
                        style="width: 32px; height: 32px;" onclick="document.getElementById('photo-upload').click()">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <input type="file" id="photo-upload" class="d-none" accept="image/*">
                </div>
            </div>

            <!-- Profile Content -->
            <div class="row mt-5 pt-5 g-3"> <!-- Changed spacing -->
                <!-- Main Info Card -->
                <div class="col-md-8">
                    <div class="card shadow-sm h-100"> <!-- Added h-100 -->
                        <div class="card-body p-3"> <!-- Reduced padding -->
                            <div id="main-info-loading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div id="main-info-content" style="display: none; margin-top: 15px; margin-left: 10px;">
                                <h2 class="card-title mb-4" id="admin-full-name"></h2>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p><strong>School ID:</strong> <span id="admin-school-id"></span></p>
                                        <p><strong>Email:</strong> <span id="admin-email"></span></p>
                                        <p><strong>Contact:</strong> <span id="admin-contact"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Member Since:</strong> <span id="admin-created"></span></p>
                                        <p><strong>Last Updated:</strong> <span id="admin-updated"></span></p>
                                    </div>
                                </div>

                                <!-- ✨ Edit Profile button -->
                                <!-- ✨ Edit Profile button -->
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" id="editProfileBtn">
                                        <i class="bi bi-pencil me-1"></i> Edit Profile
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Role & Departments Card -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Role Details</h5> <!-- mb-3 adds the gap -->
                            <div id="role-content"></div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Managing Departments</h5>
                            <div id="departments-content">
                                <div class="text-muted">No departments assigned</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="row g-3">
                            <!-- Names on one row -->
                            <div class="col-md-4">
                                <label for="edit-first-name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="edit-first-name" name="first_name"
                                    placeholder="First Name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-middle-name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="edit-middle-name" name="middle_name"
                                    placeholder="Middle Name">
                            </div>
                            <div class="col-md-4">
                                <label for="edit-last-name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="edit-last-name" name="last_name"
                                    placeholder="Last Name" required>
                            </div>

                         <div class="col-md-6">
    <label for="edit-school-id" class="form-label d-flex align-items-center">
        School ID
        <small class="text-muted ms-2">(Format: 00-0000-00)</small>
    </label>
    <input type="text"
           class="form-control"
           id="edit-school-id"
           name="school_id"
           placeholder="00-0000-00"
           pattern="\d{2}-\d{4}-\d{2}"
           maxlength="10"
           minlength="10"
           required>
</div>


                            <div class="col-md-6">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit-email" name="email"
                                    placeholder="samplemail@gmail.com" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit-contact" class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" id="edit-contact" name="contact_number"
                                    placeholder="e.g. 09123456789" pattern="\d{11,}" minlength="11" required>
                            </div>

                            <!-- Role Dropdown - Only for Head Admin -->
                            <div class="col-md-6" id="role-field-container" style="display: none;">
                                <label for="edit-role" class="form-label">Role</label>
                                <select class="form-select" id="edit-role" name="role_id" required>
                                    <option value="">Loading roles...</option>
                                </select>
                            </div>

                            <!-- New Password - Position changes based on role visibility -->
                            <div class="col-12" id="password-full-width-container" style="display: none;">
                                <label for="edit-password" class="form-label d-flex align-items-center">
                                    New Password
                                    <small class="text-muted ms-2">(Leave blank to keep current password)</small>
                                </label>
                                <input type="password" class="form-control" id="edit-password" name="password"
                                    placeholder="New Password">
                            </div>

                            <div class="col-md-6" id="password-half-width-container" style="display: none;">
                                <label for="edit-password-half" class="form-label d-flex align-items-center">
                                    New Password
                                    <small class="text-muted ms-2">(Leave blank to keep current)</small>
                                </label>
                                <input type="password" class="form-control" id="edit-password-half" name="password"
                                    placeholder="New Password">
                            </div>

                            <!-- Departments Section - Only for Head Admin -->
                            <div class="col-12" id="departments-section-container" style="display: none;">
                                <label class="form-label">Departments</label>
                                <div id="department-buttons-container" class="d-flex flex-wrap gap-2"
                                    style="flex-direction: row !important; align-items: flex-start;">
                                    <div class="text-muted">Loading departments...</div>
                                </div>
                                <input type="hidden" id="selected-departments" name="department_ids">
                                <div class="form-text">
                                    Click to select/deselect departments. First selected becomes primary.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProfileChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Include toast.js -->
    <script src="{{ asset('js/admin/toast.js') }}"></script>

    <script>
        document.getElementById('edit-school-id').addEventListener('input', function (e) {
            // keep only digits
            let digits = e.target.value.replace(/\D/g, '');
            // add dashes after 2 and 6 digits
            if (digits.length > 2 && digits.length <= 6) {
                digits = digits.slice(0, 2) + '-' + digits.slice(2);
            } else if (digits.length > 6) {
                digits = digits.slice(0, 2) + '-' + digits.slice(2, 6) + '-' + digits.slice(6, 8);
            }
            e.target.value = digits;
        });

        function isHeadAdmin(adminData) {
            return adminData && adminData.role && adminData.role.role_id === 1;
        }

        // Cloudinary configuration
        const cloudinaryConfig = {
            cloudName: 'dn98ntlkd',
            apiKey: '545682193957699',
            uploadPresetPhoto: 'admin-photos',
            uploadPresetWallpaper: 'admin-wallpapers'
        };

        // Function to delete old image from Cloudinary via backend
        async function deleteOldCloudinaryImage(publicId, type) {
            if (!publicId) return true;

            // Skip deletion for default images
            const defaultIds = ['ksdmh4mmpxdtjogdgjmm', 'verzp7lqedwsfn3hz8xf'];
            if (defaultIds.includes(publicId)) {
                console.log('Skipping deletion of default image:', publicId);
                return true;
            }

            try {
                const token = localStorage.getItem('adminToken');
                const response = await fetch('http://127.0.0.1:8000/api/admin/delete-cloudinary-image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        public_id: publicId,
                        type: type
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.warn('Failed to delete old image:', errorData);
                    // Don't throw error - continue with upload even if deletion fails
                    return false;
                }

                const result = await response.json();
                console.log('Old image deleted successfully:', result);
                return result.deleted;
            } catch (error) {
                console.error('Error deleting old image from Cloudinary:', error);
                // Continue with upload even if deletion fails
                return false;
            }
        }

        // Load roles and departments for the modal
        async function loadRolesAndDepartments() {
            const token = localStorage.getItem('adminToken');

            try {
                // Load roles
                const rolesResponse = await fetch('http://127.0.0.1:8000/api/admin-role', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!rolesResponse.ok) throw new Error('Failed to fetch roles');
                const roles = await rolesResponse.json();

                const roleSelect = document.getElementById('edit-role');
                roleSelect.innerHTML = '<option value="">Select Role</option>';
                roles.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.role_id;
                    option.textContent = role.role_title;
                    roleSelect.appendChild(option);
                });

                // Load departments for later use
                const deptResponse = await fetch('http://127.0.0.1:8000/api/departments', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!deptResponse.ok) throw new Error('Failed to fetch departments');
                window.departmentsData = await deptResponse.json(); // Store for later use

            } catch (error) {
                console.error('Error loading modal options:', error);
                throw error;
            }
        }

        // Function to update hidden input with selected departments
        function updateSelectedDepartments() {
            const deptContainer = document.getElementById('department-buttons-container');
            if (!deptContainer) return;

            const selectedButtons = deptContainer.querySelectorAll('.btn.active');
            const selectedDeptIds = Array.from(selectedButtons).map(btn => btn.dataset.deptId);
            document.getElementById('selected-departments').value = JSON.stringify(selectedDeptIds);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const token = localStorage.getItem('adminToken');
            if (!token) {
                window.location.href = '/admin/admin-login';
                return;
            }

            let currentAdminData = null;

            // Function to create department buttons (call this when modal is shown)
            function createDepartmentButtons() {
                const deptContainer = document.getElementById('department-buttons-container');
                if (!deptContainer) {
                    console.error('Department buttons container not found - retrying in 50ms');
                    setTimeout(createDepartmentButtons, 50);
                    return;
                }

                deptContainer.innerHTML = '';

                if (!window.departmentsData || window.departmentsData.length === 0) {
                    deptContainer.innerHTML = '<div class="text-muted">No departments available</div>';
                    return;
                }

                window.departmentsData.forEach(dept => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-primary btn-sm';
                    button.textContent = `${dept.department_name} (${dept.department_code})`;
                    button.dataset.deptId = dept.department_id;

                    button.addEventListener('click', function () {
                        this.classList.toggle('active');
                        updateSelectedDepartments();
                    });

                    deptContainer.appendChild(button);
                });

                // Pre-select current departments - add debug logging
                console.log('Current admin data:', currentAdminData);
                if (currentAdminData && currentAdminData.departments && currentAdminData.departments.length > 0) {
                    console.log('Pre-selecting departments:', currentAdminData.departments);
                    currentAdminData.departments.forEach(dept => {
                        const button = deptContainer.querySelector(`[data-dept-id="${dept.department_id}"]`);
                        if (button) {
                            button.classList.add('active');
                            console.log('Selected department:', dept.department_id, dept.department_name);
                        } else {
                            console.log('Department button not found for ID:', dept.department_id);
                        }
                    });
                    updateSelectedDepartments();
                } else {
                    console.log('No departments to pre-select');
                }

                // Add role change listener for Head Admin and Vice President auto-select
                const roleSelect = document.getElementById('edit-role');
                if (roleSelect) {
                    // Remove any existing event listeners first
                    roleSelect.replaceWith(roleSelect.cloneNode(true));
                    const newRoleSelect = document.getElementById('edit-role');

                    newRoleSelect.addEventListener('change', function () {
                        const selectedRoleId = parseInt(this.value);
                        console.log('Role changed to:', selectedRoleId);

                        // Role IDs for Head Admin and Vice President of Administration
                        const autoSelectRoleIds = [1, 2]; // Assuming 1 = Head Admin, 2 = Vice President

                        if (autoSelectRoleIds.includes(selectedRoleId)) {
                            console.log('Auto-selecting all departments for role:', selectedRoleId);
                            // Select all departments
                            const allButtons = deptContainer.querySelectorAll('.btn');
                            allButtons.forEach(button => {
                                button.classList.add('active');
                            });
                            updateSelectedDepartments();
                        }
                    });
                }
            }

            // Load profile data
            fetch('http://127.0.0.1:8000/api/admin/profile', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                credentials: 'include'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch profile data');
                    }
                    return response.json();
                })
                .then(data => {
                    currentAdminData = data;
                    console.log('Profile data:', data);

                    // Load profile photo
                    if (data.photo_url) {
                        document.getElementById('profile-photo').src = data.photo_url;
                    }

                    // Load wallpaper if exists
                    if (data.wallpaper_url) {
                        document.getElementById('wallpaper-container').style.backgroundImage = `url(${data.wallpaper_url})`;
                        document.getElementById('wallpaper-container').style.backgroundSize = 'cover';
                        document.getElementById('wallpaper-container').style.backgroundPosition = 'center';
                    }

                    // Update main info
                    document.getElementById('admin-full-name').textContent = `${data.first_name} ${data.last_name}`;
                    document.getElementById('admin-school-id').textContent = data.school_id || 'Not set';
                    document.getElementById('admin-email').textContent = data.email;
                    document.getElementById('admin-contact').textContent = data.contact_number || 'Not set';
                    document.getElementById('admin-created').textContent = new Date(data.created_at).toLocaleDateString();
                    document.getElementById('admin-updated').textContent = new Date(data.updated_at).toLocaleDateString();

                    // Update role details
                    if (data.role) {
                        document.getElementById('role-content').innerHTML = `
                                                                        <div class="d-flex align-items-center mb-3">
                                                                            <span class="badge bg-primary me-2">${data.role.role_title}</span>
                                                                        </div>
                                                                        <p class="text-muted small">${data.role.description}</p>
                                                                    `;
                    }

                    // Update departments
                    if (data.departments && data.departments.length > 0) {
                        const deptList = data.departments.map(dept => {
                            const isPrimary = dept.pivot.is_primary ? ' (Primary)' : '';
                            return `<div class="badge bg-light text-dark me-2 mb-2">${dept.department_name}${isPrimary}</div>`;
                        }).join('');
                        document.getElementById('departments-content').innerHTML = deptList;
                    } else {
                        document.getElementById('departments-content').innerHTML =
                            '<div class="text-muted">No departments assigned</div>';
                    }

                    // Show content
                    document.getElementById('main-info-loading').style.display = 'none';
                    document.getElementById('main-info-content').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching profile:', error);
                    document.getElementById('main-info-loading').innerHTML =
                        '<div class="text-danger">Error loading profile data</div>';
                });

            // Edit Profile Modal Functionality
            document.getElementById('editProfileBtn').addEventListener('click', function () {
                const modalElement = document.getElementById('editProfileModal');
                const modal = new bootstrap.Modal(modalElement);

                // Load roles and departments into the modal
                loadRolesAndDepartments().then(() => {
                    // Pre-fill form with current admin data
                    if (currentAdminData) {
                        document.getElementById('edit-first-name').value = currentAdminData.first_name || '';
                        document.getElementById('edit-last-name').value = currentAdminData.last_name || '';
                        document.getElementById('edit-middle-name').value = currentAdminData.middle_name || '';
                        document.getElementById('edit-school-id').value = currentAdminData.school_id || '';
                        document.getElementById('edit-email').value = currentAdminData.email || '';
                        document.getElementById('edit-contact').value = currentAdminData.contact_number || '';

                        // Set the current admin's role after a brief delay to ensure options are populated
                        setTimeout(() => {
                            const roleSelect = document.getElementById('edit-role');
                            if (roleSelect && currentAdminData.role_id) {
                                roleSelect.value = currentAdminData.role_id;
                                console.log('Set role to:', currentAdminData.role_id);
                            }
                        }, 100);
                    }

                    // Use Bootstrap modal event to create buttons when modal is shown
                    modalElement.addEventListener('shown.bs.modal', function onModalShow() {
    // Show/hide sections based on Head Admin status
    const isHead = isHeadAdmin(currentAdminData);
    console.log('Is Head Admin:', isHead, 'Current Admin Data:', currentAdminData);
    
    // Role field - only for Head Admin
    const roleField = document.getElementById('role-field-container');
    if (roleField) {
        roleField.style.display = isHead ? 'block' : 'none';
        console.log('Role field display:', roleField.style.display);
    }
    
    // Departments section - only for Head Admin
    const deptSection = document.getElementById('departments-section-container');
    if (deptSection) {
        deptSection.style.display = isHead ? 'block' : 'none';
        console.log('Dept section display:', deptSection.style.display);
    }
    
    // Password field positioning
    const passwordFull = document.getElementById('password-full-width-container');
    const passwordHalf = document.getElementById('password-half-width-container');
    
    if (passwordFull && passwordHalf) {
        if (isHead) {
            // Head Admin: Role takes half width, password goes full width below
            passwordFull.style.display = 'block';
            passwordHalf.style.display = 'none';
            // Sync password values between both fields
            const currentPassword = document.getElementById('edit-password-half').value;
            document.getElementById('edit-password').value = currentPassword;
        } else {
            // Non-Head Admin: Password takes the role's position (half width)
            passwordFull.style.display = 'none';
            passwordHalf.style.display = 'block';
            // Sync password values between both fields
            const currentPassword = document.getElementById('edit-password').value;
            document.getElementById('edit-password-half').value = currentPassword;
        }
        console.log('Password full display:', passwordFull.style.display);
        console.log('Password half display:', passwordHalf.style.display);
    }

    // Only create department buttons if Head Admin
    if (isHead) {
        createDepartmentButtons();
    } else {
        // Clear department buttons for non-Head Admin
        const deptContainer = document.getElementById('department-buttons-container');
        if (deptContainer) {
            deptContainer.innerHTML = '<div class="text-muted">Department management not available</div>';
        }
    }

    // Also set the role value again when modal is fully shown (only for Head Admin)
    if (isHead && currentAdminData && currentAdminData.role_id) {
        const roleSelect = document.getElementById('edit-role');
        if (roleSelect) {
            roleSelect.value = currentAdminData.role_id;
            console.log('Modal shown - set role to:', currentAdminData.role_id);
        }
    }

    modalElement.removeEventListener('shown.bs.modal', onModalShow);
});

                    modal.show();
                }).catch(error => {
                    console.error('Error loading modal data:', error);
                    showToast('Failed to load edit form data', 'error', 3000);
                });
            });


            // Save profile changes
            document.getElementById('saveProfileChanges').addEventListener('click', async function () {
                const token = localStorage.getItem('adminToken');
                const isHead = isHeadAdmin(currentAdminData);

                // ✅ Validate School ID format ##-####-##
                const schoolId = document.getElementById('edit-school-id').value.trim();
                const schoolIdPattern = /^\d{2}-\d{4}-\d{2}$/;
                if (!schoolIdPattern.test(schoolId)) {
                    showToast('School ID must follow the format ##-####-##', 'error', 4000);
                    return;  // stop save
                }

                // Get selected department values from hidden input (only for Head Admin)
                let selectedDepartments = [];
                if (isHead) {
                    selectedDepartments = JSON.parse(document.getElementById('selected-departments').value || '[]');
                }

                // Get role ID (only for Head Admin, otherwise use current role)
                let selectedRoleId;
                if (isHead) {
                    selectedRoleId = parseInt(document.getElementById('edit-role').value);
                } else {
                    selectedRoleId = currentAdminData.role_id; // Keep current role for non-Head Admin
                }

                // Validate department selection (only for Head Admin and except for certain roles)
                if (isHead) {
                    const noDeptRequiredRoleIds = [1, 2];
                    if (selectedDepartments.length === 0 && !noDeptRequiredRoleIds.includes(selectedRoleId)) {
                        showToast('Please select at least one department', 'error', 3000);
                        return;
                    }
                }

                // Prepare data for API
                const jsonData = {
                    first_name: document.getElementById('edit-first-name').value,
                    last_name: document.getElementById('edit-last-name').value,
                    middle_name: document.getElementById('edit-middle-name').value,
                    school_id: schoolId,  // already validated
                    email: document.getElementById('edit-email').value,
                    contact_number: document.getElementById('edit-contact').value,
                    role_id: selectedRoleId
                };

                // Only include departments if Head Admin
                if (isHead) {
                    jsonData.department_ids = selectedDepartments;
                }

                // Get password from the appropriate field based on Head Admin status
                let password = '';
                if (isHead) {
                    password = document.getElementById('edit-password').value;
                } else {
                    password = document.getElementById('edit-password-half').value;
                }

                if (password) jsonData.password = password;

                try {
                    const response = await fetch(
                        `http://127.0.0.1:8000/api/admin/update/${currentAdminData.admin_id}`,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify(jsonData)
                        }
                    );

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to update profile');
                    }

                    showToast('Profile updated successfully!', 'success', 3000);
                    bootstrap.Modal.getInstance(document.getElementById('editProfileModal')).hide();
                    setTimeout(() => location.reload(), 1000);

                } catch (error) {
                    console.error('Error updating profile:', error);
                    showToast('Failed to update profile: ' + error.message, 'error', 4000);
                }
            });


            // Handle photo upload with Cloudinary
            document.getElementById('photo-upload').addEventListener('change', async (e) => {
                const file = e.target.files[0];
                if (!file) return;

                let oldPublicId = null;

                try {
                    // Show loading state
                    const originalSrc = document.getElementById('profile-photo').src;
                    document.getElementById('profile-photo').src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjM1ZW0iPlVwbG9hZGluZy4uLjwvdGV4dD48L3N2Zz4=';

                    // Store old public_id for deletion after successful upload
                    if (currentAdminData && currentAdminData.photo_public_id) {
                        oldPublicId = currentAdminData.photo_public_id;
                    }

                    // Upload new photo to Cloudinary
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', cloudinaryConfig.uploadPresetPhoto);

                    console.log('Uploading photo to Cloudinary...');

                    const uploadResponse = await fetch(`https://api.cloudinary.com/v1_1/${cloudinaryConfig.cloudName}/upload`, {
                        method: 'POST',
                        body: formData
                    });

                    if (!uploadResponse.ok) {
                        const errorText = await uploadResponse.text();
                        console.error('Cloudinary upload failed:', errorText);
                        throw new Error('Failed to upload to Cloudinary');
                    }

                    const cloudinaryResult = await uploadResponse.json();
                    console.log('Cloudinary upload result:', cloudinaryResult);

                    if (!cloudinaryResult.secure_url || !cloudinaryResult.public_id) {
                        throw new Error('Invalid response from Cloudinary');
                    }

                    // Update database records
                    console.log('Updating database records...');
                    const updateResponse = await fetch('http://127.0.0.1:8000/api/admin/update-photo-records', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            photo_url: cloudinaryResult.secure_url,
                            photo_public_id: cloudinaryResult.public_id,
                            type: 'photo'
                        })
                    });

                    if (!updateResponse.ok) {
                        const errorData = await updateResponse.json();
                        console.error('Database update failed:', errorData);
                        throw new Error(errorData.message || 'Failed to update database');
                    }

                    const updateResult = await updateResponse.json();
                    console.log('Database update result:', updateResult);

                    // Update UI and current data
                    document.getElementById('profile-photo').src = cloudinaryResult.secure_url;
                    currentAdminData.photo_url = cloudinaryResult.secure_url;
                    currentAdminData.photo_public_id = cloudinaryResult.public_id;

                    // Delete old image from Cloudinary after successful update
                    if (oldPublicId) {
                        console.log('Deleting old photo from Cloudinary:', oldPublicId);
                        await deleteOldCloudinaryImage(oldPublicId, 'photo');
                    }

                    showToast('Profile photo updated successfully!', 'success', 2000);

                    // Refresh the entire page after 2 seconds to show the toast
                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                } catch (error) {
                    console.error('Error uploading photo:', error);
                    document.getElementById('profile-photo').src = originalSrc;
                    showToast('Failed to upload photo: ' + error.message, 'error', 4000);
                } finally {
                    // Clear the file input
                    e.target.value = '';
                }
            });

            // Handle wallpaper upload with Cloudinary
            document.querySelector('.profile-hero button').addEventListener('click', () => {
                document.getElementById('wallpaper-upload').click();
            });

            document.getElementById('wallpaper-upload').addEventListener('change', async (e) => {
                const file = e.target.files[0];
                if (!file) return;

                let oldPublicId = null;

                try {
                    // Show loading state
                    const originalBackground = document.getElementById('wallpaper-container').style.backgroundImage;
                    document.getElementById('wallpaper-container').style.backgroundImage = 'linear-gradient(45deg, #f8f9fa 25%, #e9ecef 25%, #e9ecef 50%, #f8f9fa 50%, #f8f9fa 75%, #e9ecef 75%, #e9ecef 100%)';
                    document.getElementById('wallpaper-container').style.backgroundSize = '20px 20px';

                    // Store old public_id for deletion after successful upload
                    if (currentAdminData && currentAdminData.wallpaper_public_id) {
                        oldPublicId = currentAdminData.wallpaper_public_id;
                    }

                    // Upload new wallpaper to Cloudinary
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', cloudinaryConfig.uploadPresetWallpaper);

                    console.log('Uploading wallpaper to Cloudinary...');

                    const uploadResponse = await fetch(`https://api.cloudinary.com/v1_1/${cloudinaryConfig.cloudName}/upload`, {
                        method: 'POST',
                        body: formData
                    });

                    if (!uploadResponse.ok) {
                        const errorText = await uploadResponse.text();
                        console.error('Cloudinary wallpaper upload failed:', errorText);
                        throw new Error('Failed to upload to Cloudinary');
                    }

                    const cloudinaryResult = await uploadResponse.json();
                    console.log('Cloudinary wallpaper upload result:', cloudinaryResult);

                    if (!cloudinaryResult.secure_url || !cloudinaryResult.public_id) {
                        throw new Error('Invalid response from Cloudinary');
                    }

                    // Update database records
                    console.log('Updating database records for wallpaper...');
                    const updateResponse = await fetch('http://127.0.0.1:8000/api/admin/update-photo-records', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            wallpaper_url: cloudinaryResult.secure_url,
                            wallpaper_public_id: cloudinaryResult.public_id,
                            type: 'wallpaper'
                        })
                    });

                    if (!updateResponse.ok) {
                        const errorData = await updateResponse.json();
                        console.error('Database update failed:', errorData);
                        throw new Error(errorData.message || 'Failed to update database');
                    }

                    const updateResult = await updateResponse.json();
                    console.log('Database update result:', updateResult);

                    // Update UI and current data
                    document.getElementById('wallpaper-container').style.backgroundImage = `url(${cloudinaryResult.secure_url})`;
                    document.getElementById('wallpaper-container').style.backgroundSize = 'cover';
                    currentAdminData.wallpaper_url = cloudinaryResult.secure_url;
                    currentAdminData.wallpaper_public_id = cloudinaryResult.public_id;

                    // Delete old image from Cloudinary after successful update
                    if (oldPublicId) {
                        console.log('Deleting old wallpaper from Cloudinary:', oldPublicId);
                        await deleteOldCloudinaryImage(oldPublicId, 'wallpaper');
                    }

                    showToast('Wallpaper updated successfully!', 'success', 2000);

                    // Refresh the entire page after 2 seconds to show the toast
                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                } catch (error) {
                    console.error('Error uploading wallpaper:', error);
                    document.getElementById('wallpaper-container').style.backgroundImage = originalBackground;
                    showToast('Failed to upload wallpaper: ' + error.message, 'error', 4000);
                } finally {
                    // Clear the file input
                    e.target.value = '';
                }
            });
        });
    </script>
@endsection
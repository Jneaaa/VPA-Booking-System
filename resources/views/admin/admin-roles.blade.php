@extends('layouts.admin')

@section('title', 'Manage Administrators')

@section('content')
<style>


#confirmDeleteBtn {
    min-width: 120px;
}


/* Spinner for delete button */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
    /* Shared card-like styling for both sections */
  .form-section,
  .table-section {
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  }

  .table-section .table-responsive {
    overflow-x: auto;
  }

  .table-section table {
    table-layout: fixed;
    width: 100%;
    font-size: 0.875rem; /* Slightly smaller text for better fit */
  }

  .table-section table th,
  .table-section table td {
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .table-section table th {
    background-color: #f8f9fa;
  }

  /* Column width adjustments */
  .table-section table th:nth-child(1), /* Admin ID */
  .table-section table td:nth-child(1) {
    width: 80px;
    min-width: 80px;
    max-width: 80px;
  }

  .table-section table th:nth-child(2), /* School ID */
  .table-section table td:nth-child(2) {
    width: 110px;
    min-width: 110px;
    max-width: 110px;
  }

  .table-section table th:nth-child(3), /* Full Name */
  .table-section table td:nth-child(3) {
    width: 110px;
    min-width: 110px;
    white-space: normal; /* Allow name wrapping */
    word-wrap: break-word;
  }

  .table-section table th:nth-child(4), /* Email */
  .table-section table td:nth-child(4) {
    width: 200px;
    min-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .table-section table th:nth-child(5), /* Phone Number */
  .table-section table td:nth-child(5) {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
  }

  .table-section table th:nth-child(6), /* Role */
  .table-section table td:nth-child(6) {
    width: 120px;
    min-width: 120px;
    white-space: normal; /* Allow role name wrapping */
  }

  /* Department column - ensure header doesn't break */
  .table-section table th:nth-child(7) {
    width: auto;
    min-width: 150px;
    white-space: nowrap; /* Prevent header from breaking */
    overflow: visible;
  }

  .table-section table td:nth-child(7) {
    width: auto;
    min-width: 150px;
    white-space: normal; /* Allow department badges to wrap */
    word-wrap: break-word;
  }

  /* Actions column - ensure buttons stay side by side */
  .table-section table th:nth-child(8) {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    white-space: nowrap;
    overflow: visible;
  }

  .table-section table td:nth-child(8) {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    white-space: nowrap;
    overflow: visible;
  }

  /* Ensure action buttons container doesn't wrap */
  .table-section table td:nth-child(8) .btn {
    white-space: nowrap;
    flex-shrink: 0;
  }

  /* Department button styles */
 .department-btn {
  background-color: #6c757d !important; /* solid gray */
  color: #fff !important;
  border-color: #6c757d !important;
  transition: background-color 0.2s ease;
}

/* Darker gray on hover if NOT selected */
.department-btn:hover:not(.selected) {
  background-color: #5a6268 !important; /* darker gray */
  border-color: #5a6268 !important;
}

.department-btn.selected {
  background-color: var(--btn-primary) !important; /* custom blue */
  color: #fff !important;
  border-color: var(--btn-primary) !important;
}

/* Optional: keep same blue on hover when selected */
.department-btn.selected:hover {
  background-color: var(--btn-primary-hover) !important; /* slightly darker blue */
  border-color: var(--btn-primary-hover) !important;
}

  /* Department buttons styling */
  #department-buttons-container button,
  #add-department-buttons-container button {
    display: inline-flex !important;
    width: auto !important;
  }

  /* Make badges in department column more compact */
  .table-section .badge {
    font-size: 0.75rem;
    margin-bottom: 2px;
    display: inline-block;
  }
</style>

<div id="layout">
  <main id="main">
    <div class="container my-4">

      <!-- Add New Admin card -->
      <section class="form-section card p-4 mb-4">
        <h3 class="mb-4 fw-bold">Add New Admin</h3>
        <form id="addAdminForm" novalidate>
          @csrf
          <div class="row g-3">
            <div class="col-md-4">
              <label for="first_name" class="form-label">First Name</label>
              <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
            </div>
            <div class="col-md-4">
              <label for="middle_name" class="form-label">Middle Name</label>
              <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name">
            </div>
            <div class="col-md-4">
              <label for="last_name" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
            </div>
            <div class="col-md-6">
              <label for="school_id" class="form-label d-flex align-items-center">
                School ID
                <small class="text-muted ms-2">(Format: 00-0000-00)</small>
              </label>
              <input type="text" class="form-control" id="school_id" name="school_id"
                     placeholder="00-0000-00"
                     pattern="\d{2}-\d{4}-\d{2}" maxlength="10" minlength="10">
            </div>
<div class="col-md-6">
  <label for="email" class="form-label">Email</label>
 <input
  type="email"
  class="form-control"
  id="email"
  name="email"
  placeholder="samplemail@gmail.com"
  required
  minlength="6"
  maxlength="150"
  autocomplete="off"
>
</div>

<div class="col-md-6">
  <label for="contact_number" class="form-label">Phone Number</label>
  <input
    type="tel"
    class="form-control"
    id="contact_number"
    name="contact_number"
    placeholder="e.g. 09123456789"
    pattern="\d{11,20}"
    minlength="11"
    maxlength="20"
  >
</div>

<div class="col-md-6">
  <label for="role_id" class="form-label">Role</label>
  <select class="form-select" id="role_id" name="role_id" required>
    <option value="">Select a role</option>
  </select>
</div>

<div class="col-12">
  <label for="password" class="form-label d-flex align-items-center">
    Temporary Password
    <small class="text-muted ms-2">(Admin will be prompted to change this upon first login.)</small>
  </label>
  <input
    type="password"
    class="form-control"
    id="password"
    name="password"
    placeholder="Temporary Password"
    required
    minlength="8"
    maxlength="12"
  >
</div>



<!-- Departments Section for Add Form -->
<div class="col-12" id="add-departments-section-container">
  <label class="form-label d-flex align-items-center">
    Departments
    <small class="text-muted ms-2">(Click to select/deselect departments.)</small>
  </label>

  <div id="add-department-buttons-container"
       class="d-flex flex-wrap gap-2"
       style="flex-direction: row !important; align-items: flex-start;">
    <!-- Bootstrap loading spinner -->
    <div class="d-flex align-items-center text-muted">
      <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
      Loading departments...
    </div>
  </div>

  <input type="hidden" id="add-selected-departments" name="department_ids">
</div>


            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-primary">Add Admin</button>
            </div>
          </div>
        </form>
      </section>

      <!-- Existing Admins card -->
      <section class="table-section card p-4">
        <h3 class="mb-4 fw-bold">Existing Admins</h3>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Admin ID</th>
                <th>School ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Role</th>
                <th class="allow-wrap">Departments</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="adminListBody">
              <!-- Dynamic content will be loaded here -->
            </tbody>
          </table>
        </div>
      </section>

    </div><!-- /.container -->
  </main>
</div>


  <!-- Edit Admin Modal -->
  <div class="modal fade" id="editAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editAdminForm">
            @csrf
            <input type="hidden" id="edit_admin_id" name="admin_id">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="edit_first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
              </div>
              <div class="col-md-4">
                <label for="edit_middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
              </div>
              <div class="col-md-4">
                <label for="edit_last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
              </div>
              <div class="col-md-6">
                <label for="edit_school_id" class="form-label d-flex align-items-center">
                  School ID
                  <small class="text-muted ms-2">(Format: 00-0000-00)</small>
                </label>
                <input type="text" class="form-control" id="edit_school_id" name="school_id" placeholder="00-0000-00"
                  pattern="\d{2}-\d{4}-\d{2}" maxlength="10" minlength="10">
              </div>
              <div class="col-md-6">
                <label for="edit_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
              </div>
              <div class="col-md-6">
                <label for="edit_contact_number" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="edit_contact_number" name="contact_number"
                  placeholder="e.g. 09123456789" pattern="\d{11,}" minlength="11">
              </div>
              <div class="col-md-6">
                <label for="edit_role_id" class="form-label">Role</label>
                <select class="form-select" id="edit_role_id" name="role_id" required>
                  <option value="">Loading roles...</option>
                </select>
              </div>
              <div class="col-12">
                <label for="edit_password" class="form-label d-flex align-items-center">
                  New Password
                  <small class="text-muted ms-2">(Leave blank to keep current password)</small>
                </label>
                <input type="password" class="form-control" id="edit_password" name="password" placeholder="New Password">
              </div>

              <!-- Departments Section for Edit Modal -->
              <div class="col-12" id="edit-departments-section-container">
                <label class="form-label">Departments</label>
                <div id="edit-department-buttons-container" class="d-flex flex-wrap gap-2"
                  style="flex-direction: row !important; align-items: flex-start;">
                  <div class="text-muted">Loading departments...</div>
                </div>
                <input type="hidden" id="edit-selected-departments" name="department_ids">
                <div class="form-text">
                  Click to select/deselect departments. First selected becomes primary.
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveAdminChanges">Save Changes</button>
        </div>
      </div>
    </div>
  </div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 2rem;"></i>
                <p class="mb-1 fw-bold">Are you sure you want to delete this admin?</p>
                <p class="mb-3 text-muted">This action cannot be undone. All associated data will be permanently removed.</p>
                
                <div id="deleteAdminDetails" class="bg-light p-3 rounded">
                    <!-- Admin details will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Admin</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin/toast.js') }}"></script>
<script>
    // School ID formatting function
    function formatSchoolId(input) {
      input.addEventListener('input', function (e) {
        let digits = e.target.value.replace(/\D/g, '');
        if (digits.length > 2 && digits.length <= 6) {
          digits = digits.slice(0, 2) + '-' + digits.slice(2);
        } else if (digits.length > 6) {
          digits = digits.slice(0, 2) + '-' + digits.slice(2, 6) + '-' + digits.slice(6, 8);
        }
        e.target.value = digits;
      });
    }

    // Function to create department buttons
    function createDepartmentButtons(containerId, selectedDeptIds = []) {
      const deptContainer = document.getElementById(containerId);
      if (!deptContainer) {
        console.error('Department buttons container not found:', containerId);
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
        button.className = 'btn btn-secondary btn-sm department-btn';
        button.textContent = `${dept.department_name} (${dept.department_code})`;
        button.dataset.deptId = dept.department_id;

        // Pre-select if in selectedDeptIds - add BOTH classes
        if (selectedDeptIds.includes(dept.department_id.toString())) {
          button.classList.add('selected');
          button.classList.add('active'); // Add active class too
        }

        // Toggle both classes when clicked
        button.addEventListener('click', function () {
          this.classList.toggle('selected');
          this.classList.toggle('active'); // Also toggle active class
          updateSelectedDepartments(containerId);
        });

        deptContainer.appendChild(button);
      });
    }

    // Function to update hidden input with selected departments - FIXED
    function updateSelectedDepartments(containerType) {
      let deptContainer, hiddenInput;

      if (containerType === 'add-department-buttons-container') {
        deptContainer = document.getElementById('add-department-buttons-container');
        hiddenInput = document.getElementById('add-selected-departments');
      } else {
        deptContainer = document.getElementById('edit-department-buttons-container');
        hiddenInput = document.getElementById('edit-selected-departments');
      }

      if (!deptContainer || !hiddenInput) return;

      // Look for buttons with EITHER selected OR active class
      const selectedButtons = deptContainer.querySelectorAll('.btn.selected, .btn.active');
      const selectedDeptIds = Array.from(selectedButtons).map(btn => btn.dataset.deptId);
      hiddenInput.value = JSON.stringify(selectedDeptIds);
      console.log('Updated selected departments:', selectedDeptIds);
    }

    // Function to auto-select all departments for specific roles - FIXED
    function setupRoleAutoSelect(roleSelectId, deptContainerId) {
      const roleSelect = document.getElementById(roleSelectId);
      if (!roleSelect) return;

      roleSelect.addEventListener('change', function () {
        const selectedRoleId = parseInt(this.value);
        console.log('Role changed to:', selectedRoleId);

        // Role IDs that should auto-select all departments
        const autoSelectRoleIds = [1, 2]; // Head Admin and Vice President

        const deptContainer = document.getElementById(deptContainerId);
        if (!deptContainer) return;

        const allButtons = deptContainer.querySelectorAll('.department-btn');
        
        if (autoSelectRoleIds.includes(selectedRoleId)) {
          console.log('Auto-selecting all departments for role:', selectedRoleId);
          allButtons.forEach(button => {
            button.classList.add('selected');
            button.classList.add('active');
          });
        } else {
          console.log('Clearing department selection for role:', selectedRoleId);
          allButtons.forEach(button => {
            button.classList.remove('selected');
            button.classList.remove('active');
          });
        }
        updateSelectedDepartments(deptContainerId);
      });
    }

    // Function to manually select all departments
    function selectAllDepartments(containerId) {
      const deptContainer = document.getElementById(containerId);
      if (!deptContainer) return;

      const allButtons = deptContainer.querySelectorAll('.department-btn');
      allButtons.forEach(button => {
        button.classList.add('selected');
        button.classList.add('active');
      });
      updateSelectedDepartments(containerId);
    }

    document.addEventListener('DOMContentLoaded', function () {
      const addAdminForm = document.getElementById('addAdminForm');
      const roleSelect = document.getElementById('role_id');
      const adminListBody = document.getElementById('adminListBody');
      const token = localStorage.getItem('adminToken') || localStorage.getItem('token');

      // Apply School ID formatting
      formatSchoolId(document.getElementById('school_id'));
      formatSchoolId(document.getElementById('edit_school_id'));

      // Function to load departments
      async function loadDepartments() {
        try {
          const response = await fetch('http://127.0.0.1:8000/api/departments', {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error('Failed to fetch departments');
          }

          window.departmentsData = await response.json();
          console.log('Departments loaded:', window.departmentsData);
        } catch (error) {
          console.error('Error loading departments:', error);
          window.departmentsData = [];
        }
      }

      // Function to render admin list with departments
      async function loadAdminList() {
        try {
          const response = await fetch('/api/admins', {
            headers: {
              'Accept': 'application/json',
              'Authorization': `Bearer ${token}`
            }
          });

          if (!response.ok) throw new Error('Failed to fetch admin list');

          const admins = await response.json();
          adminListBody.innerHTML = '';

          if (admins.length === 0) {
            adminListBody.innerHTML =
              '<tr><td colspan="8" class="text-center">No other admins found</td></tr>';
            return;
          }

          admins.forEach(admin => {
            const currentAdminId = localStorage.getItem('adminId');
            if (currentAdminId && admin.admin_id == currentAdminId) return;

            // Format departments - more compact display without (P) indicator
            let departmentsHtml = 'N/A';
            if (admin.departments?.length) {
              departmentsHtml = admin.departments.map(dept => {
                return `<span class="badge bg-light text-dark me-1 mb-1" title="${dept.department_name}">
                          ${dept.department_code}
                        </span>`;
              }).join('');
            }

            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${admin.admin_id}</td>
              <td>${admin.school_id || 'N/A'}</td>
              <td>${admin.first_name} ${admin.middle_name ? admin.middle_name + ' ' : ''}${admin.last_name}</td>
              <td title="${admin.email}">${admin.email}</td>
              <td>${admin.contact_number || 'N/A'}</td>
              <td>${admin.role ? admin.role.role_title : 'N/A'}</td>
              <td>${departmentsHtml}</td>
              <td>
                <button class="btn btn-sm btn-info me-1" onclick="openEditModal(${admin.admin_id})" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${admin.admin_id})" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            `;
            adminListBody.appendChild(row);
          });
        } catch (error) {
          console.error('Error loading admin list:', error);
          adminListBody.innerHTML =
            '<tr><td colspan="8" class="text-center">Error loading admin list</td></tr>';
        }
      }

      // Delete admin function - UPDATED to use modal
let adminToDelete = null;

window.deleteAdmin = function (adminId) {
    adminToDelete = adminId;
    
    // Fetch admin details to show in confirmation modal
    fetch(`/api/admins/${adminId}`, {
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to fetch admin details');
        return response.json();
    })
    .then(admin => {
        // Populate admin details in modal
        document.getElementById('deleteAdminDetails').innerHTML = `
            <div class="row">
                <div class="col-4 fw-bold">School ID:</div>
                <div class="col-8">${admin.school_id}</div>
                <div class="col-4 fw-bold">Name:</div>
                <div class="col-8">${admin.first_name} ${admin.middle_name ? admin.middle_name + ' ' : ''}${admin.last_name}</div>
                <div class="col-4 fw-bold">Email:</div>
                <div class="col-8">${admin.email}</div>
                <div class="col-4 fw-bold">Role:</div>
                <div class="col-8">${admin.role ? admin.role.role_title : 'N/A'}</div>
            </div>
        `;
        
        // Show the modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    })
    .catch(error => {
        console.error('Error fetching admin details:', error);
        // Fallback: show modal with basic info if details fetch fails
        document.getElementById('deleteAdminDetails').innerHTML = `
            <div class="text-center">
                <p class="mb-0">Admin ID: ${adminId}</p>
                <p class="text-muted">Unable to load full details</p>
            </div>
        `;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    });
};

// Confirm delete button handler
document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
    if (!adminToDelete) return;

    const deleteBtn = this;
    const originalText = deleteBtn.innerHTML;
    
    // Show loading state
    deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
    deleteBtn.disabled = true;

    try {
        const response = await fetch(`/api/admins/${adminToDelete}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to delete admin');
        }

        // Success - close modal and refresh list
        bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal')).hide();
        
        // Show success message
        showToast('Admin deleted successfully', 'success');
        
        await loadAdminList();
        
    } catch (error) {
        console.error('Error:', error);
        
        // Show error message
        showToast('Failed to delete admin: ' + error.message, 'error');
        
        // Reset button state
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
});

// Reset modal state when hidden
document.getElementById('deleteConfirmationModal').addEventListener('hidden.bs.modal', function () {
    adminToDelete = null;
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    deleteBtn.innerHTML = 'Delete Admin';
    deleteBtn.disabled = false;
});

      // Fetch roles and populate dropdown
      async function loadRoles() {
        try {
          const response = await fetch('http://127.0.0.1:8000/api/admin-role', {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const roles = await response.json();
          console.log('Fetched roles:', roles);

          // Populate add form role select
          roleSelect.innerHTML = '<option value="">Select a role</option>';
          if (roles && roles.length > 0) {
            roles.forEach(role => {
              const option = new Option(role.role_title, role.role_id);
              roleSelect.add(option);
            });
          }

          // Populate edit form role select
          const editRoleSelect = document.getElementById('edit_role_id');
          if (editRoleSelect) {
            editRoleSelect.innerHTML = '<option value="">Select a role</option>';
            roles.forEach(role => {
              const option = new Option(role.role_title, role.role_id);
              editRoleSelect.add(option);
            });
          }
        } catch (error) {
          console.error('Error loading roles:', error);
          roleSelect.innerHTML = '<option value="">Error loading roles</option>';
        }
      }

      // Add admin form submission - FIXED validation
      addAdminForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        // Validate School ID format
        const schoolId = document.getElementById('school_id').value.trim();
        const schoolIdPattern = /^\d{2}-\d{4}-\d{2}$/;
        if (schoolId && !schoolIdPattern.test(schoolId)) {
          showToast('School ID must follow the format ##-####-##');
          return;
        }

        // Validate email format
const email = document.getElementById('email').value.trim();
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
if (!emailPattern.test(email)) {
  showToast('Please enter a valid email address');
  return;
}

        // Get selected departments - FIXED: Check both classes
        const selectedDeptIds = JSON.parse(document.getElementById('add-selected-departments').value || '[]');
        console.log('Selected departments for add:', selectedDeptIds);

        // Validate department selection for certain roles
        const roleId = parseInt(document.getElementById('role_id').value);
        const noDeptRequiredRoleIds = [1, 2]; // Head Admin and Vice President
        
        // For roles 1 and 2, auto-select all departments if none are selected
        if (selectedDeptIds.length === 0 && noDeptRequiredRoleIds.includes(roleId)) {
          selectAllDepartments('add-department-buttons-container');
          // Re-get the selected departments after auto-selection
          const updatedDeptIds = JSON.parse(document.getElementById('add-selected-departments').value || '[]');
          console.log('Auto-selected departments:', updatedDeptIds);
        }
        
        // Final validation check
        const finalSelectedDeptIds = JSON.parse(document.getElementById('add-selected-departments').value || '[]');
        if (finalSelectedDeptIds.length === 0 && !noDeptRequiredRoleIds.includes(roleId)) {
          shortToast('Please select at least one department for this role');
          return;
        }

        const formData = {
          first_name: document.getElementById('first_name').value,
          middle_name: document.getElementById('middle_name').value,
          last_name: document.getElementById('last_name').value,
          email: document.getElementById('email').value,
          contact_number: document.getElementById('contact_number').value,
          role_id: roleId,
          school_id: schoolId,
          password: document.getElementById('password').value,
          department_ids: finalSelectedDeptIds, // Use the final selected departments
          photo_url: 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png',
          photo_public_id: 'ksdmh4mmpxdtjogdgjmm',
          wallpaper_url: null,
          wallpaper_public_id: null
        };

        console.log('Submitting form data:', formData);

        try {
          const response = await fetch('/api/admins', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
              'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
          });

          if (!response.ok) {
            const errorData = await response.json();
            console.error('Backend error response:', errorData);
            throw new Error(errorData.message || 'Failed to add admin');
          }

          const data = await response.json();
          showToast('Admin added successfully!');
          addAdminForm.reset();
          document.getElementById('add-selected-departments').value = '[]';
          createDepartmentButtons('add-department-buttons-container', []);
          await loadAdminList();
        } catch (error) {
          console.error('Error adding admin:', error);
          showToast(error.message || 'Failed to add admin');
        }
      });

      // Function to open edit modal
      window.openEditModal = async function (adminId) {
        try {
          const response = await fetch(`/api/admins/${adminId}`, {
            headers: {
              'Accept': 'application/json',
              'Authorization': `Bearer ${token}`
            }
          });

          if (!response.ok) {
            throw new Error('Failed to fetch admin details');
          }

          const admin = await response.json();
          console.log('Admin details for edit:', admin);

          // Populate form
          document.getElementById('edit_admin_id').value = admin.admin_id;
          document.getElementById('edit_first_name').value = admin.first_name;
          document.getElementById('edit_middle_name').value = admin.middle_name || '';
          document.getElementById('edit_last_name').value = admin.last_name;
          document.getElementById('edit_email').value = admin.email;
          document.getElementById('edit_contact_number').value = admin.contact_number || '';
          document.getElementById('edit_school_id').value = admin.school_id || '';

          // Set role
          const editRoleSelect = document.getElementById('edit_role_id');
          if (editRoleSelect && admin.role_id) {
            editRoleSelect.value = admin.role_id;
          }

          // Create department buttons and pre-select current departments
          const selectedDeptIds = admin.departments ? admin.departments.map(dept => dept.department_id.toString()) : [];
          createDepartmentButtons('edit-department-buttons-container', selectedDeptIds);
          document.getElementById('edit-selected-departments').value = JSON.stringify(selectedDeptIds);

          // Setup role auto-select for edit modal
          setupRoleAutoSelect('edit_role_id', 'edit-department-buttons-container');

          // Show modal
          const modal = new bootstrap.Modal(document.getElementById('editAdminModal'));
          modal.show();

        } catch (error) {
          console.error('Error opening edit modal:', error);
          showToast('Failed to load admin details: ' + error.message);
        }
      };

      // Save edited admin - FIXED validation
      document.getElementById('saveAdminChanges').addEventListener('click', async function () {
        const adminId = document.getElementById('edit_admin_id').value;

        // Validate School ID format
        const schoolId = document.getElementById('edit_school_id').value.trim();
        const schoolIdPattern = /^\d{2}-\d{4}-\d{2}$/;
        if (schoolId && !schoolIdPattern.test(schoolId)) {
          showToast('School ID must follow the format ##-####-##');
          return;
        }

        // Get selected departments - FIXED: Check both classes
        const selectedDeptIds = JSON.parse(document.getElementById('edit-selected-departments').value || '[]');
        console.log('Selected departments for edit:', selectedDeptIds);

        // Validate department selection for certain roles
        const roleId = parseInt(document.getElementById('edit_role_id').value);
        const noDeptRequiredRoleIds = [1, 2];
        
        // For roles 1 and 2, auto-select all departments if none are selected
        if (selectedDeptIds.length === 0 && noDeptRequiredRoleIds.includes(roleId)) {
          selectAllDepartments('edit-department-buttons-container');
          // Re-get the selected departments after auto-selection
          const updatedDeptIds = JSON.parse(document.getElementById('edit-selected-departments').value || '[]');
          console.log('Auto-selected departments:', updatedDeptIds);
        }
        
        // Final validation check
        const finalSelectedDeptIds = JSON.parse(document.getElementById('edit-selected-departments').value || '[]');
        if (finalSelectedDeptIds.length === 0 && !noDeptRequiredRoleIds.includes(roleId)) {
          showToast('Please select at least one department for this role');
          return;
        }

        const formData = {
          admin_id: adminId,
          first_name: document.getElementById('edit_first_name').value,
          middle_name: document.getElementById('edit_middle_name').value,
          last_name: document.getElementById('edit_last_name').value,
          email: document.getElementById('edit_email').value,
          contact_number: document.getElementById('edit_contact_number').value,
          role_id: roleId,
          school_id: schoolId,
          password: document.getElementById('edit_password').value || undefined,
          department_ids: finalSelectedDeptIds // Use the final selected departments
        };

        console.log('Submitting edit form data:', formData);

        try {
          const response = await fetch(`/api/admins/${adminId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
              'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
          });

          if (!response.ok) {
            const errorData = await response.json();
            console.error('Backend error response:', errorData);
            throw new Error(errorData.message || 'Failed to update admin');
          }

          showToast('Admin updated successfully!');
          await loadAdminList();
          bootstrap.Modal.getInstance(document.getElementById('editAdminModal')).hide();
        } catch (error) {
          console.error('Error updating admin:', error);
          showToast(error.message || 'Failed to update admin');
        }
      });

      // Initialize the page
      async function initializePage() {
        await loadDepartments();
        await loadRoles();
        await loadAdminList();

        // Create department buttons for add form
        createDepartmentButtons('add-department-buttons-container', []);
        setupRoleAutoSelect('role_id', 'add-department-buttons-container');
      }

      initializePage();
    });
  </script>
@endsection
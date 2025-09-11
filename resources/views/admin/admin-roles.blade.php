@extends('layouts.admin')

@section('title', 'Manage Administrators')

@section('content')
  <style>


    /* Custom styles for the Admin Roles page */
    .form-section {
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    }

    .table-section {
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .table-section table th,
    .table-section table td {
    vertical-align: middle;
    }

    .table-section table th {
    background-color: #f8f9fa;
    }
  </style>
  <div id="layout">
    <main id="main">

    <section class="form-section">
      <h3 class="mb-4 fw-bold">Add New Admin</h3>
      <form id="addAdminForm">
      @csrf
      <div class="row g-3">
        <div class="col-md-4">
        <label for="first_name" class="form-label">First Name</span></label>
        <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="col-md-4">
        <label for="middle_name" class="form-label">Middle Name</label>
        <input type="text" class="form-control" id="middle_name" name="middle_name">
        </div>
        <div class="col-md-4">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
        <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="col-md-6">
        <label for="contact_number" class="form-label">Phone Number</label>
        <input type="tel" class="form-control" id="contact_number" name="contact_number">
        </div>
        <div class="col-md-6">
        <label for="role_id" class="form-label">Role</label>
        <select class="form-select" id="role_id" name="role_id" required>
          <option value="">Loading roles...</option>
        </select>
        </div>
        <div class="col-md-6">
        <label for="school_id" class="form-label">School ID</label>
        <input type="text" class="form-control" id="school_id" name="school_id">
        </div>
        <div class="col-12">
        <label for="password" class="form-label">Temporary Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
        <div class="form-text">Admin will be prompted to change this upon first login.</div>
        </div>
        <div class="col-12 mt-4">
        <button type="submit" class="btn btn-primary">Add Admin</button>
        </div>
      </div>
      </form>
    </section>

    <section class="table-section">
      <h3 class="mb-4 fw-bold">Existing Admins</h3>
      <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
        <tr>
          <th>Admin ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone Number</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody id="adminListBody">
        <!-- Dynamic content will be loaded here -->
        </tbody>
      </table>
      </div>
    </section>
    </main>
  </div>
  <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Event Details</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
        <strong>Title:</strong> <span id="eventTitle"></span>
        </li>
        <li class="list-group-item">
        <strong>Date:</strong> <span id="eventDate"></span>
        </li>
        <li class="list-group-item">
        <strong>Time:</strong> <span id="eventTime">10:00 AM - 12:00 PM</span>
        </li>
        <li class="list-group-item">
        <strong>Description:</strong> <span id="eventDescription"></span>
        </li>
      </ul>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
    </div>
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
          <label for="edit_email" class="form-label">Email</label>
          <input type="email" class="form-control" id="edit_email" name="email" required>
        </div>
        <div class="col-md-6">
          <label for="edit_contact_number" class="form-label">Phone Number</label>
          <input type="tel" class="form-control" id="edit_contact_number" name="contact_number">
        </div>
        <div class="col-md-6">
          <label for="edit_role_id" class="form-label">Role</label>
          <select class="form-select" id="edit_role_id" name="role_id" required>
          <option value="">Loading roles...</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="edit_school_id" class="form-label">School ID</label>
          <input type="text" class="form-control" id="edit_school_id" name="school_id">
        </div>
        <div class="col-12">
          <label for="edit_password" class="form-label">New Password (leave blank to keep current)</label>
          <input type="password" class="form-control" id="edit_password" name="password">
          <div class="form-text">Only fill this if you want to change the password</div>
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
@endsection

@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const addAdminForm = document.getElementById('addAdminForm');
    const roleSelect = document.getElementById('role_id');
    const adminListBody = document.getElementById('adminListBody');

    // Get the token from localStorage - make sure it matches your auth system
    const token = localStorage.getItem('adminToken') || localStorage.getItem('token');

    // Function to render admin list
    async function loadAdminList() {
      try {
      const response = await fetch('/api/admins', {
        headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch admin list');
      }

      const admins = await response.json();
      adminListBody.innerHTML = ''; // Clear existing content

      if (admins.length === 0) {
        adminListBody.innerHTML = '<tr><td colspan="6" class="text-center">No other admins found</td></tr>';
        return;
      }

      admins.forEach(admin => {
        // Double-check that current admin isn't in the list (client-side fallback)
        const currentAdminId = localStorage.getItem('adminId');
        if (currentAdminId && admin.admin_id == currentAdminId) return;

        const row = document.createElement('tr');
        row.innerHTML = `
      <td>${admin.admin_id}</td>
      <td>${admin.first_name} ${admin.middle_name ? admin.middle_name + ' ' : ''}${admin.last_name}</td>
      <td>${admin.email}</td>
      <td>${admin.contact_number || 'N/A'}</td>
      <td>${admin.role ? admin.role.role_title : 'N/A'}</td>
      <td>
        <button class="btn btn-sm btn-info me-2"><i class="bi bi-pencil"></i> Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${admin.admin_id})">
        <i class="bi bi-trash"></i> Delete
        </button>
      </td>
      `;
        adminListBody.appendChild(row);
      });
      } catch (error) {
      console.error('Error loading admin list:', error);
      adminListBody.innerHTML = '<tr><td colspan="6" class="text-center">Error loading admin list</td></tr>';
      }
    }

    // Add delete function
    window.deleteAdmin = async function (adminId) {
      if (!confirm('Are you sure you want to delete this admin?')) {
      return;
      }

      try {
      const response = await fetch(`/api/admins/${adminId}`, {
        method: 'DELETE',
        headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
      });

      if (!response.ok) {
        throw new Error('Failed to delete admin');
      }

      alert('Admin deleted successfully');
      await loadAdminList(); // Refresh the list
      } catch (error) {
      console.error('Error:', error);
      alert('Failed to delete admin');
      }
    };

    // Load initial admin list
    loadAdminList();

    // Fetch roles and populate dropdown
    async function loadRoles() {
      try {
      const response = await fetch('/admin-roles', {
        headers: {
        'Accept': 'application/json'
        }
      });
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const roles = await response.json();
      console.log('Fetched roles:', roles); // Debug log

      // Clear loading option
      roleSelect.innerHTML = '<option value="">Select a role</option>';

      if (roles && roles.length > 0) {
        roles.forEach(role => {
        const option = new Option(role.role_title, role.role_id);
        roleSelect.add(option);
        });
      } else {
        console.warn('No roles returned from API');
        roleSelect.innerHTML = '<option value="">No roles available</option>';
      }
      } catch (error) {
      console.error('Error loading roles:', error);
      roleSelect.innerHTML = '<option value="">Error loading roles</option>';
      }
    }

    // Load roles immediately
    loadRoles();

    addAdminForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      const formData = {
      first_name: document.getElementById('first_name').value,
      middle_name: document.getElementById('middle_name').value,
      last_name: document.getElementById('last_name').value,
      email: document.getElementById('email').value,
      contact_number: document.getElementById('contact_number').value,
      role_id: document.getElementById('role_id').value,
      school_id: document.getElementById('school_id').value,
      password: document.getElementById('password').value,
      photo_url: 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png',
      photo_public_id: 'ksdmh4mmpxdtjogdgjmm',
      wallpaper_url: null,
      wallpaper_public_id: null
      };

      try {
      const response = await fetch('/api/admins', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify(formData)
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to add admin');
      }

      const data = await response.json();
      alert('Admin added successfully!');
      addAdminForm.reset();
      await loadAdminList(); // Reload admin list instead of page refresh
      } catch (error) {
      console.error('Error:', error);
      alert(error.message || 'Failed to add admin');
      }
    });

    // Function to open edit modal
    async function openEditModal(adminId) {
      try {
      // Fetch admin details
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

      // Populate form
      document.getElementById('edit_admin_id').value = admin.admin_id;
      document.getElementById('edit_first_name').value = admin.first_name;
      document.getElementById('edit_middle_name').value = admin.middle_name || '';
      document.getElementById('edit_last_name').value = admin.last_name;
      document.getElementById('edit_email').value = admin.email;
      document.getElementById('edit_contact_number').value = admin.contact_number || '';
      document.getElementById('edit_school_id').value = admin.school_id || '';

      // Load roles for the dropdown
      const editRoleSelect = document.getElementById('edit_role_id');
      editRoleSelect.innerHTML = '<option value="">Loading...</option>';

      const rolesResponse = await fetch('/admin-roles', {
        headers: { 'Accept': 'application/json' }
      });
      const roles = await rolesResponse.json();

      editRoleSelect.innerHTML = '<option value="">Select a role</option>';
      roles.forEach(role => {
        const option = new Option(role.role_title, role.role_id);
        option.selected = (role.role_id === admin.role_id);
        editRoleSelect.add(option);
      });

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('editAdminModal'));
      modal.show();

      } catch (error) {
      console.error('Error opening edit modal:', error);
      alert('Failed to load admin details');
      }
    }

    // Save edited admin
    document.getElementById('saveAdminChanges').addEventListener('click', async function () {
      const formData = {
      admin_id: document.getElementById('edit_admin_id').value,
      first_name: document.getElementById('edit_first_name').value,
      middle_name: document.getElementById('edit_middle_name').value,
      last_name: document.getElementById('edit_last_name').value,
      email: document.getElementById('edit_email').value,
      contact_number: document.getElementById('edit_contact_number').value,
      role_id: document.getElementById('edit_role_id').value,
      school_id: document.getElementById('edit_school_id').value,
      password: document.getElementById('edit_password').value || undefined // Only send if changed
      };

      try {
      const response = await fetch(`/api/admins/${formData.admin_id}`, {
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
        throw new Error(errorData.message || 'Failed to update admin');
      }

      alert('Admin updated successfully!');
      await loadAdminList(); // Refresh the list
      bootstrap.Modal.getInstance(document.getElementById('editAdminModal')).hide();
      } catch (error) {
      console.error('Error:', error);
      alert(error.message || 'Failed to update admin');
      }
    });

    // Update the admin list rendering to include edit button click handler
    async function loadAdminList() {
      try {
      const response = await fetch('/api/admins', {
        headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch admin list');
      }

      const admins = await response.json();
      adminListBody.innerHTML = ''; // Clear existing content

      if (admins.length === 0) {
        adminListBody.innerHTML = '<tr><td colspan="6" class="text-center">No other admins found</td></tr>';
        return;
      }

      admins.forEach(admin => {
        const currentAdminId = localStorage.getItem('adminId');
        if (currentAdminId && admin.admin_id == currentAdminId) return;

        const row = document.createElement('tr');
        row.innerHTML = `
              <td>${admin.admin_id}</td>
              <td>${admin.first_name} ${admin.middle_name ? admin.middle_name + ' ' : ''}${admin.last_name}</td>
              <td>${admin.email}</td>
              <td>${admin.contact_number || 'N/A'}</td>
              <td>${admin.role ? admin.role.role_title : 'N/A'}</td>
              <td>
                <button class="btn btn-sm btn-info me-2" onclick="openEditModal(${admin.admin_id})">
                  <i class="bi bi-pencil"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${admin.admin_id})">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </td>
            `;
        adminListBody.appendChild(row);
      });
      } catch (error) {
      console.error('Error loading admin list:', error);
      adminListBody.innerHTML = '<tr><td colspan="6" class="text-center">Error loading admin list</td></tr>';
      }
    }

    // Make openEditModal available globally
    window.openEditModal = openEditModal;

    });
  </script>
@endsection
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
  <style>
    /* Add sharp edges to all elements */
    * {
    border-radius: 0 !important;
    }

    /* Exclude admin photo container and status circle */
    .profile-img {
    border-radius: 50% !important;
    }

    .status-indicator {
    border-radius: 50% !important;
    }

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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Manage Admin Roles</h2>
    </div>

    <section class="form-section">
      <h3 class="mb-4">Add New Admin</h3>
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
      <h3 class="mb-4">Existing Admins</h3>
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
        <tr>
          <td>101</td>
          <td>Jane A. Doe</td>
          <td>jane.doe@cpu.edu</td>
          <td>+639123456789</td>
          <td>Admin</td>
          <td>
          <button class="btn btn-sm btn-info me-2"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
          </td>
        </tr>
        <tr>
          <td>102</td>
          <td>Robert B. Johnson</td>
          <td>robert.johnson@cpu.edu</td>
          <td>N/A</td>
          <td>Department Admin</td>
          <td>
          <button class="btn btn-sm btn-info me-2"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
          </td>
        </tr>
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
@endsection

@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const addAdminForm = document.getElementById('addAdminForm');
      const roleSelect = document.getElementById('role_id');

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
              const option = new Option(role.role_name, role.role_id);
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
        };

        try {
          const response = await fetch('/admins', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(formData)
          });

          const data = await response.json();

          if (response.ok) {
            alert('Admin added successfully!');
            addAdminForm.reset();
            // Optionally refresh the admin list
            location.reload();
          } else {
            alert(data.message || 'Failed to add admin');
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Failed to add admin');
        }
      });
    });
  </script>
@endsection
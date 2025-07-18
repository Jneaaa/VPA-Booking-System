<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/admin-styles.css">
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
</head>

<body>
  <header id="topbar" class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <img src="../cpu-logo.png" alt="CPU Logo" class="me-2" style="height: 40px;">
      <span class="fw-bold">CPU Facilities and Equipment Management</span>
    </div>
    <div class="d-flex align-items-center">
      <div class="position-relative me-3">
        <div class="dropdown">
          <i class="bi bi-bell fs-4 position-relative" id="notificationIcon" data-bs-toggle="dropdown"
            aria-expanded="false"></i>
          <span class="notification-badge">1</span>
          <ul class="dropdown-menu dropdown-menu-end p-0" id="notificationDropdown" aria-labelledby="notificationIcon">
            <li class="dropdown-header">Notifications</li>
            <li>
              <a href="#" class="notification-item unread d-block" data-notification-id="1">
                <div class="notification-title">New Facility Request</div>
                <div class="notification-text">John Smith requested the Main Auditorium for March 15, 2024</div>
                <div class="notification-time">2 minutes ago</div>
              </a>
            </li>
            <li>
              <a href="#" class="notification-item d-block">
                <div class="notification-title">Booking Approved</div>
                <div class="notification-text">Your equipment request for the sound system has been approved</div>
                <div class="notification-time">3 hours ago</div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider m-0">
            </li>
            <li><a href="#" class="dropdown-item view-all-item text-center">View all notifications</a></li>
          </ul>
        </div>
      </div>
      <div class="dropdown">
        <button class="btn btn-link p-0 text-white" id="dropdownMenuButton" data-bs-toggle="dropdown"
          aria-expanded="false">
          <i class="bi bi-three-dots fs-4"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="adminlogin.html"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </header>
  <div id="layout">
    <nav id="sidebar">
      <div class="text-center mb-4">
        <div class="position-relative d-inline-block">
          <img src="assets/admin-pic.jpg" alt="Admin Profile" class="profile-img rounded-circle">
        </div>
        <h5 class="mt-3 mb-1">John Doe</h5>
        <p class="text-muted mb-0">Head Admin</p>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.html"> <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="calendar.html">
            <i class="bi bi-calendar-event me-2"></i>
            Calendar
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="requisitions.html">
            <i class="bi bi-file-earmark-text me-2"></i>
            Requisitions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage-facilities.html">
            <i class="bi bi-building me-2"></i>
            Facilities
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="equipment.html">
            <i class="bi bi-tools me-2"></i>
            Equipment
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="admin-roles-page.html"> <i class="bi bi-people me-2"></i>
            Admin Roles
          </a>
        </li>
      </ul>
    </nav>
    <main id="main">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manage Admin Roles</h2>
      </div>

      <section class="form-section">
        <h3 class="mb-4">Add New Admin</h3>
        <form id="addAdminForm">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="col-md-4">
              <label for="middleName" class="form-label">Middle Name</label>
              <input type="text" class="form-control" id="middleName" name="middleName">
            </div>
            <div class="col-md-4">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6">
              <label for="contactNumber" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="contactNumber" name="contactNumber">
            </div>
            <div class="col-md-6">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role" required>
                <option value="">Select a role</option>
                <option value="Admin">Admin</option>
                <option value="Signatories">Signatories</option>
                <option value="Inventory Manager">Inventory Manager</option>
                <option value="President">President</option>
              </select>
            </div>
            <div class="col-12">
              <label for="hashedPassword" class="form-label">Temporary Password</label>
              <input type="password" class="form-control" id="hashedPassword" name="hashedPassword" required>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="js/global.js"></script>
  <script src="js/calendar.js"> </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const addAdminForm = document.getElementById('addAdminForm');
      const adminListBody = document.getElementById('adminListBody');

      addAdminForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        const firstName = document.getElementById('firstName').value;
        const middleName = document.getElementById('middleName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('email').value;
        const contactNumber = document.getElementById('contactNumber').value;
        const role = document.getElementById('role').value;
        const hashedPassword = document.getElementById('hashedPassword').value;


        const fullName = `${firstName} ${middleName ? middleName + ' ' : ''}${lastName}`;

        // In a real application, you would send this data to your backend
        // using fetch() or XMLHttpRequest. For this example, we'll just
        // add it to the table directly.

        // Example of how you would send data to the backend:
        /*
        fetch('/api/add-admin', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            firstName,
            middleName,
            lastName,
            email,
            contactNumber,
            role,
            hashedPassword // In a real app, this should be securely hashed on the server
          }),
        })
        .then(response => response.json())
        .then(data => {
          console.log('Success:', data);
          // If successful, you'd then add the new admin to the table
          // Or ideally, re-fetch the list of admins from the server
          addNewAdminRow(data); // Assuming data includes the new admin's details including adminID
          addAdminForm.reset(); // Clear the form
        })
        .catch((error) => {
          console.error('Error:', error);
          alert('Failed to add admin.');
        });
        */

        // For demonstration purposes, we'll add a dummy admin ID and add to table
        const dummyAdminID = Math.floor(Math.random() * 1000) + 200; // Generate a dummy ID
        addNewAdminRow({
          adminID: dummyAdminID,
          firstName,
          middleName,
          lastName,
          email,
          contactNumber,
          role
        });

        addAdminForm.reset();
        alert('Admin added successfully (demonstration only)!');
      });

      function addNewAdminRow(admin) {
        const newRow = adminListBody.insertRow();

        const adminIDCell = newRow.insertCell();
        adminIDCell.textContent = admin.adminID;

        const fullNameCell = newRow.insertCell();
        fullNameCell.textContent = `${admin.firstName} ${admin.middleName ? admin.middleName + ' ' : ''}${admin.lastName}`;

        const emailCell = newRow.insertCell();
        emailCell.textContent = admin.email;

        const contactNumberCell = newRow.insertCell();
        contactNumberCell.textContent = admin.contactNumber || 'N/A';
        const roleCell = newRow.insertCell();
        roleCell.textContent = admin.role;

        const actionsCell = newRow.insertCell();
        actionsCell.innerHTML = `
          <button class="btn btn-sm btn-info me-2"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
        `;
      }

      // You would typically fetch existing admins from your backend here
      // Example:
      /*
      fetch('/api/get-admins')
        .then(response => response.json())
        .then(admins => {
          admins.forEach(admin => addNewAdminRow(admin));
        })
        .catch(error => console.error('Error fetching admins:', error));
      */
    });
  </script>
</body>

</html>
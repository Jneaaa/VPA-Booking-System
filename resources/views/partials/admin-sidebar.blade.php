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
          <a class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::is('admin/calendar*') ? 'active' : '' }}" href="{{ url('/admin/calendar') }}">
            <i class="bi bi-calendar-event me-2"></i>
            Calendar
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::is('admin/manage-requests*') ? 'active' : '' }}" href="{{ url('/admin/manage-requests') }}">
            <i class="bi bi-file-earmark-text me-2"></i>
            Requisitions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::is('admin/manage-facilities*') ? 'active' : '' }}" href="{{ url('/admin/manage-facilities') }}">
            <i class="bi bi-building me-2"></i>
            Facilities
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::is('admin/manage-equipment*') ? 'active' : '' }}" href="{{ url('/admin/manage-equipment') }}">
            <i class="bi bi-tools me-2"></i>
            Equipment
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::is('admin/admin-roles*') ? 'active' : '' }}" href="{{ url('/admin/admin-roles') }}">
            <i class="bi bi-people me-2"></i>
            Admin Roles
          </a>
        </li>
      </ul>
    </nav>
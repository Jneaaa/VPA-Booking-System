@extends('layouts.app')

@section('title', 'Booking Catalog - Equipment')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public/catalog.css') }}" />
  <style>

    .quantity-input.is-invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.quantity-error {
  font-size: 0.75rem;
}
    
    /* Ensure modal and calendar have proper dimensions */
#availabilityModal .modal-body {
  min-height: 70vh;
  padding: 1rem;
}

#availabilityCalendar {
  height: 65vh;
  min-height: 400px;
}

/* Make sure FullCalendar container is visible */
.fc .fc-toolbar {
  opacity: 1 !important;
  visibility: visible !important;
  display: flex !important;
}

.fc .fc-header-toolbar {
  margin-bottom: 1em !important;
}
        /* Fix modal z-index for event details modal */
#eventDetailModal {
  z-index: 9999 !important;
}

#eventDetailModal .modal-dialog {
  z-index: 10000;
}
        /* Event Modal Styles */
    .event-details p {
      margin-bottom: 1rem;
    }

    .event-details strong {
      color: #495057;
      display: block;
      margin-bottom: 0.25rem;
    }

    #eventSchedule {
      white-space: pre-line;
      background: #f8f9fa;
      padding: 0.5rem;
      border-radius: 0.25rem;
      display: inline-block;
    }

    /* FullCalendar Styles */

    #calendar {
      background: #ffffff;
    }

    /* FullCalendar Event Cursor */
    .fc-event {
      cursor: pointer !important;
    }

    .fc-event:hover {
      opacity: 0.9 !important;
    }

    /* Also target specific event elements to be sure */
    .fc-timegrid-event,
    .fc-daygrid-event,
    .fc-event-main {
      cursor: pointer !important;
    }

    .fc-daygrid-body {
      background: #f8f9fa;
    }

    .fc-col-header-cell-cushion {
      text-decoration: none;
      color: var(--cpu-text-dark);
    }

    .fc-col-header-cell {
      background: #e6e8ebff;
    }

    .fc-daygrid-day-number {
      text-decoration: none;
      color: var(--cpu-text-dark);
    }

    .fc-day-today {
      background: #f3f4f7ff !important;
    }

    .btn-custom {
      background-color: #f5bc40ff;
      color: #1d1300ff;
      border-color: transparent !important;
    }

    .btn-custom:hover {
      background-color: #daa32cff;
      color: #1d1300ff;
      border-color: transparent !important;
    }

    .btn-custom:active {
      background-color: #c08e22ff !important;
      color: #1d1300ff !important;
      border-color: transparent !important;
      box-shadow: none !important;
    }

/* Additional fix for the card container */
.list-layout .catalog-card {
  flex-direction: row;
  align-items: stretch;
  gap: 1rem;
  height: 200px;
  overflow: hidden; /* Ensure card doesn't overflow */
  padding: 0.25rem !important;
  box-sizing: border-box;
  position: relative;
}
.list-layout .catalog-card-img {
  width: 200px;
  height: 190px !important; /* Force exact height */
  object-fit: cover;
  flex-shrink: 0;
  box-sizing: border-box;
}

.list-layout .catalog-card-details {
  flex: 1;
  min-width: 0; /* Prevent flex item from overflowing */
  padding-left: 1rem;

}

.list-layout .catalog-card-actions {
  flex-direction: column;
  width: 200px;
  flex-shrink: 0;
  border-top: none;
  border-left: 1px solid #eee;
  padding: 0.75rem;
  margin-top: 0;
  gap: 0.5rem;
  justify-content: flex-start;
  height: 100%;
  box-sizing: border-box;
  align-items: stretch;
  overflow: hidden; /* Add this to prevent bleeding */
}

.list-layout .catalog-card-actions .form-action-btn {
  flex-shrink: 0;
  height: auto !important;
  min-height: 38px;
  white-space: nowrap;
  width: 60% !important;
  box-sizing: border-box;
  margin: 0 auto;
  font-size: 0.95rem !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  text-align: center;
}


.list-layout .catalog-card-fee {
  margin-top: 0;
  padding: 0.5rem 0;
}

.catalog-card-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding-top: 0.75rem;
}

.status-banner {
  align-self: flex-start; /* Prevents stretching */
  width: auto !important; /* Prevents flex stretching */
  white-space: nowrap; /* Prevents text wrapping */
  padding: 0.25rem 0.75rem; /* Consistent padding */
  margin-bottom: 0.5rem; /* Space below banner */
}

.facility-description {
  flex-grow: 1; /* takes remaining space so the fee + buttons stay at bottom */
}

/* Fee and actions section */
.catalog-card-fee,
.catalog-card-actions {
  margin-top: auto; /* push to bottom */
}

.catalog-card-actions {
  margin-top: auto; /* pushes the buttons to the bottom */
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
  padding: 0.75rem;
}

.grid-layout .catalog-card {
  padding: 0.25rem !important;
}

    #chooseCatalogDropdown {
      color: #0c3779ff;
      transition: color 0.2s ease;
    }
    #chooseCatalogDropdown:hover,
    #chooseCatalogDropdown:focus,
    #chooseCatalogDropdown.show {
      color: #0066ffff;
    }
    /* Catalog Hero Section */
    .catalog-hero-section {
      background-image: url("{{ asset('assets/homepage.jpg') }}");
      background-size: cover;
      background-position: center;
      min-height: 170px;
      display: flex;
      align-items: flex-end;
      padding-bottom: 20px;
      position: relative;
      z-index: 0;
    }
  </style>

  <section class="catalog-hero-section">
    <div class="catalog-hero-content">
      <h2 id="catalogHeroTitle">Equipment Catalog</h2>
    </div>
  </section>

   <main class="main-catalog-section">
    <div class="container">
      <!-- Sidebar -->
      <div class="row">
        <div class="col-lg-3 col-md-4">
          <div class="quick-links-card mb-4">
            <p class="mb-2">
              Not sure when to book?<br />View available timeslots here.
            </p>

            <div class="d-grid gap-2"> <!-- ensures uniform full-width buttons -->
              <button type="button" class="btn btn-light btn-custom d-flex justify-content-center align-items-center"
                id="eventsCalendarBtn" data-bs-toggle="modal" data-bs-target="#userCalendarModal">
                <i class="fa-solid fa-calendar me-2"></i> Events Calendar
              </button>

              <div style="position:relative;">
                <span id="requisitionBadge" class="badge bg-danger rounded-pill position-absolute"
                  style="top:-0.7rem; right:-0.7rem; font-size:0.8em; z-index:2; display:none;">
                  0
                </span>
                <a id="requisitionFormButton" href="reservation-form"
                  class="btn btn-primary d-flex justify-content-center align-items-center position-relative">
                  <i class="fa-solid fa-file-invoice me-2"></i> Your Requisition Form
                </a>
              </div>
            </div>
          </div>

          <div class="sidebar-card">
            <h5>Browse by Category</h5>
            <div class="filter-list" id="categoryFilterList"></div>
          </div>
        </div>


        <div class="col-lg-9 col-md-8">
          <div class="right-content-header">
            <div class="dropdown">
              <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" id="chooseCatalogDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                All Equipment
              </button>
              <ul class="dropdown-menu" aria-labelledby="chooseCatalogDropdown">
                <li>
                  <a class="dropdown-item" href="{{ asset('facility-catalog') }}" data-catalog-type="facilities">
                    Facilities
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ asset('equipment-catalog') }}" data-catalog-type="equipment">
                    Equipment
                  </a>
                </li>
              </ul>
            </div>

            <div class="d-flex gap-2 filter-sort-dropdowns">
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                  id="statusDropdown">
                  Status: Available
                </button>
                <ul class="dropdown-menu" id="statusFilterMenu">
                  <li><a class="dropdown-item status-option" href="#" data-status="All">All</a></li>
                  <li><a class="dropdown-item status-option" href="#" data-status="Available">Available</a></li>
                  <li><a class="dropdown-item status-option" href="#" data-status="Unavailable">Unavailable</a></li>
                </ul>
              </div>

              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                  id="layoutDropdown">
                  Grid Layout
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item layout-option active" href="#" data-layout="grid">Grid</a></li>
                  <li><a class="dropdown-item layout-option" href="#" data-layout="list">List</a></li>
                </ul>
              </div>
            </div>
          </div>

 <!-- Calendar Modal -->
          <div class="modal fade" id="userCalendarModal" tabindex="-1" aria-labelledby="userCalendarModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 95%;">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="userCalendarModalLabel">Available Schedules</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" style="min-height: 70vh;">
                  <div id="userFullCalendar" style="height: 65vh;"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>


          <!-- Equipment Detail Modal -->
          <div class="modal fade" id="equipmentDetailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="equipmentDetailModalLabel">Equipment Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="equipmentDetailContent">
                  <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading Indicator -->
          <div id="loadingIndicator" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading catalog items...</p>
          </div>

          <!-- Catalog Items Container -->
          <div id="catalogItemsContainer" class="grid-layout d-none"></div>

          <div class="text-center mt-4">
            <nav>
              <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </main>
    <!-- Event Details Modal -->
  <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventDetailModalLabel">Booking Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-borderless mb-0">
              <tbody>
                <tr>
                  <td class="border-0 p-1"><strong>Event Title:</strong></td>
                  <td class="border-0 p-1" id="eventTitle"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Requester:</strong></td>
                  <td class="border-0 p-1" id="eventRequester"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Purpose:</strong></td>
                  <td class="border-0 p-1" id="eventPurpose"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Participants:</strong></td>
                  <td class="border-0 p-1" id="eventParticipants"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Status:</strong></td>
                  <td class="border-0 p-1">
                    <span id="eventStatus" class="badge"></span>
                  </td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Start:</strong></td>
                  <td class="border-0 p-1" id="eventStart"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>End:</strong></td>
                  <td class="border-0 p-1" id="eventEnd"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Facilities:</strong></td>
                  <td class="border-0 p-1" id="eventFacilities"></td>
                </tr>
                <tr>
                  <td class="border-0 p-1"><strong>Equipment:</strong></td>
                  <td class="border-0 p-1" id="eventEquipment"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
    <!-- Availability Modal -->
<div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="availabilityModalLabel">Availability Schedule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3" style="min-height: 70vh;">
        <div id="availabilityCalendar" style="height: 65vh;"></div>
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

       // Show event details in modal
    function showEventModal(event) {
      const modalElement = document.getElementById('eventDetailModal');
      if (!modalElement) {
        console.error('Event detail modal not found in DOM');
        return;
      }

      const extendedProps = event.extendedProps;
      const modal = new bootstrap.Modal(modalElement);

      // Format dates and times
      const startDate = event.start.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      const endDate = event.end.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      const startTime = event.start.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
      const endTime = event.end.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });

      // Set modal content with safety checks
      const elements = {
        eventDetailModalLabel: document.getElementById('eventDetailModalLabel'),
        eventTitle: document.getElementById('eventTitle'),
        eventRequester: document.getElementById('eventRequester'),
        eventPurpose: document.getElementById('eventPurpose'),
        eventParticipants: document.getElementById('eventParticipants'),
        eventStatus: document.getElementById('eventStatus'),
        eventStart: document.getElementById('eventStart'),
        eventEnd: document.getElementById('eventEnd'),
        eventFacilities: document.getElementById('eventFacilities'),
        eventEquipment: document.getElementById('eventEquipment')
      };

      // Check if all elements exist before setting content
      Object.keys(elements).forEach(key => {
        if (!elements[key]) {
          console.error(`Element with id '${key}' not found`);
          return;
        }
      });

      // Now safely set the content
      elements.eventDetailModalLabel.textContent = 'Booking Details';
      elements.eventTitle.textContent = event.title || 'N/A';
      elements.eventRequester.textContent = extendedProps.requester || 'N/A';
      elements.eventPurpose.textContent = extendedProps.purpose || 'N/A';
      elements.eventParticipants.textContent = extendedProps.num_participants || 'N/A';

      // Status badge
      elements.eventStatus.textContent = extendedProps.status || 'N/A';
      elements.eventStatus.style.backgroundColor = event.backgroundColor;
      elements.eventStatus.style.color = '#fff';
      elements.eventStatus.style.padding = '0.25rem 0.5rem';
      elements.eventStatus.style.borderRadius = '0.25rem';

      // Start and end times
      elements.eventStart.textContent = `${startDate} at ${startTime}`;
      elements.eventEnd.textContent = `${endDate} at ${endTime}`;

      // Facilities
      const facilities = extendedProps.facilities || [];
      elements.eventFacilities.textContent = facilities.length > 0
        ? facilities.join(', ')
        : 'None';

      // Equipment
      const equipment = extendedProps.equipment || [];
      elements.eventEquipment.textContent = equipment.length > 0
        ? equipment.join(', ')
        : 'None';

      modal.show();
    }


    // Global variables
    let currentPage = 1;
    const itemsPerPage = 6;
    let allEquipment = [];
    let equipmentCategories = [];
    let filteredItems = [];
    let currentLayout = "grid";
    let selectedItems = []; // Will store items in session
    let allowedStatusIds = [1, 2];
    let statusFilter = "All"; // "All", "Available", "Unavailable"

    // DOM elements
    const loadingIndicator = document.getElementById("loadingIndicator");
    const catalogItemsContainer = document.getElementById("catalogItemsContainer");
    const categoryFilterList = document.getElementById("categoryFilterList");
    const pagination = document.getElementById("pagination");
    const requisitionBadge = document.getElementById("requisitionBadge");

    // Utility Functions
    async function fetchData(url, options = {}) {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const response = await fetch(url, {
        ...options,
        headers: {
          "X-CSRF-TOKEN": csrfToken,
          "Content-Type": "application/json",
          "Accept": "application/json",
          ...(options.headers || {}),
        },
        credentials: "same-origin"
      });
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return await response.json();
    }

    function showToast(message, type = "success", duration = 3000) {
      const toast = document.createElement("div");

      // Toast base styles (bottom right)
      toast.className = `toast align-items-center border-0 position-fixed end-0 mb-2`;
      toast.style.zIndex = '1100';
      toast.style.bottom = '0';
      toast.style.right = '0';
      toast.style.margin = '1rem';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
      toast.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
      toast.setAttribute('role', 'alert');
      toast.setAttribute('aria-live', 'assertive');
      toast.setAttribute('aria-atomic', 'true');

      // Custom colors
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
        }, 400); // matches animation time
      }, duration);
    }


    // Render category filters (overhauled)
    function renderCategoryFilters() {
      categoryFilterList.innerHTML = "";

      // "All Categories" option
      const allCategoriesItem = document.createElement("div");
      allCategoriesItem.className = "category-item";
      allCategoriesItem.innerHTML = `
        <div class="form-check">
        <input class="form-check-input category-filter" type="checkbox" id="allCategories" value="All" checked disabled>
        <label class="form-check-label" for="allCategories">All Categories</label>
        </div>
        `;
      categoryFilterList.appendChild(allCategoriesItem);

      // Render equipment categories
      equipmentCategories.forEach((category) => {
        const categoryItem = document.createElement("div");
        categoryItem.className = "category-item";
        categoryItem.innerHTML = `
        <div class="form-check">
        <input class="form-check-input category-filter" type="checkbox" id="category${category.category_id}" value="${category.category_id}">
        <label class="form-check-label" for="category${category.category_id}">${category.category_name}</label>
        </div>
        `;
        categoryFilterList.appendChild(categoryItem);
      });

      // Event listeners for category filters
      const allCategoriesCheckbox = document.getElementById("allCategories");
      const categoryCheckboxes = Array.from(document.querySelectorAll('.category-filter')).filter(cb => cb.id !== "allCategories");

      // When any category is checked/unchecked
      categoryCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
          const anyChecked = categoryCheckboxes.some(c => c.checked);
          if (anyChecked) {
            allCategoriesCheckbox.checked = false;
            allCategoriesCheckbox.disabled = false;
          } else {
            allCategoriesCheckbox.checked = true;
            allCategoriesCheckbox.disabled = true;
          }
          filterAndRenderItems();
        });
      });

      // When "All Categories" is checked
      allCategoriesCheckbox.addEventListener('change', function () {
        if (this.checked) {
          categoryCheckboxes.forEach(cb => {
            cb.checked = false;
          });
          allCategoriesCheckbox.disabled = true;
          filterAndRenderItems();
        }
      });

      // Initial state: only "All Categories" checked and disabled
      allCategoriesCheckbox.checked = true;
      allCategoriesCheckbox.disabled = true;
      categoryCheckboxes.forEach(cb => cb.checked = false);
    }

    // Filter items based on selected categories (overhauled)
    function filterItems() {
      const allCategoriesCheckbox = document.getElementById('allCategories');
      const categoryCheckboxes = Array.from(document.querySelectorAll('.category-filter')).filter(cb => cb.id !== "allCategories");

      // Only keep allowed status
      filteredItems = [...allEquipment].filter(e => allowedStatusIds.includes(e.status.status_id));

      // Filter by status dropdown
      if (statusFilter === "Available") {
        filteredItems = filteredItems.filter(e => e.status.status_id === 1);
      } else if (statusFilter === "Unavailable") {
        filteredItems = filteredItems.filter(e => e.status.status_id === 2);
      }

      // Category filtering
      if (allCategoriesCheckbox.checked) {
        // All categories selected, show all
        return;
      }

      // Otherwise, filter by selected categories
      const selectedCategories = categoryCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
      if (selectedCategories.length === 0) {
        filteredItems = [];
        return;
      }
      filteredItems = filteredItems.filter(equipment =>
        selectedCategories.includes(equipment.category.category_id.toString())
      );
    }

    // Render items based on current layout
function renderItems(items) {
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedItems = items.slice(startIndex, startIndex + itemsPerPage);

  catalogItemsContainer.innerHTML = "";

  // handle empty state
  if (paginatedItems.length === 0) {
    catalogItemsContainer.classList.remove("grid-layout", "list-layout");
    catalogItemsContainer.innerHTML = `
      <div
        style="
          grid-column: 1 / -1;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          min-height: 220px;
          width: 100%;
        "
      >
        <i class="bi bi-box-seam fs-1 text-muted"></i>
        <h4 class="mt-2">No equipment found</h4>
      </div>
    `;
    return;
  }

  // normal render path
  catalogItemsContainer.classList.remove("grid-layout", "list-layout");
  catalogItemsContainer.classList.add(`${currentLayout}-layout`);

  if (currentLayout === "grid") {
    renderEquipmentGrid(paginatedItems);
  } else {
    renderEquipmentList(paginatedItems);
  }
}


  // Grid layout for equipment (with Check Availability button)
function renderEquipmentGrid(equipmentList) {
  catalogItemsContainer.innerHTML = equipmentList.map(item => {
    const primaryImage = item.images?.find(i => i.image_type === 'Primary')?.image_url || 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png';
    
    // Truncate equipment name (max 18 characters)
    const equipmentName = item.equipment_name.length > 18
      ? item.equipment_name.substring(0, 18) + "..."
      : item.equipment_name;

    // Truncate description (max 100 characters)
    const description = item.description
      ? (item.description.length > 100
          ? item.description.substring(0, 100) + "..."
          : item.description)
      : "No description available";

    return `
    <div class="catalog-card">
      <img src="${primaryImage}" 
           alt="${item.equipment_name}" 
           class="catalog-card-img"
           onerror="this.src='https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png'">
      <div class="catalog-card-details">
        <h5 title="${item.equipment_name}">${equipmentName}</h5>
        <span class="status-banner" style="background-color: ${item.status.color_code}">
          ${item.status.status_name}
        </span>
        <div class="catalog-card-meta">
          <span><i class="bi bi-tags-fill"></i> ${item.category.category_name}</span>
          <span><i class="bi bi-box-seam"></i> ${item.available_quantity}/${item.total_quantity} available</span>
        </div>
        <p class="facility-description" title="${item.description || ''}">${description}</p>
        <div class="catalog-card-fee">
          <i class="bi bi-cash-stack"></i> ₱${parseFloat(item.external_fee).toLocaleString()} (${item.rate_type})
        </div>
      </div>
      <div class="catalog-card-actions">
        ${getEquipmentButtonHtml(item)}
        <button class="btn btn-light btn-custom">Check Availability</button>
      </div>
    </div>
    `;
  }).join('');
}

// List layout for equipment (with Check Availability button)
function renderEquipmentList(equipmentList) {
  catalogItemsContainer.innerHTML = equipmentList.map(item => {
    const primaryImage = item.images?.find(i => i.image_type === 'Primary')?.image_url || 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png';
    
    // Truncate equipment name (max 30 characters)
    const equipmentName = item.equipment_name.length > 30
      ? item.equipment_name.substring(0, 30) + "..."
      : item.equipment_name;

    // Truncate description (max 150 characters)
    const description = item.description
      ? (item.description.length > 150
          ? item.description.substring(0, 150) + "..."
          : item.description)
      : "No description available";

    return `
    <div class="catalog-card">
      <img src="${primaryImage}" 
           alt="${item.equipment_name}" 
           class="catalog-card-img"
           onerror="this.src='https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png'">
      <div class="catalog-card-details">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 title="${item.equipment_name}">${equipmentName}</h5>
          <span class="status-banner" style="background-color: ${item.status.color_code}">
            ${item.status.status_name}
          </span>
        </div>
        <div class="catalog-card-meta">
          <span><i class="bi bi-tags-fill"></i> ${item.category.category_name}</span>
          <span><i class="bi bi-box-seam"></i> ${item.available_quantity}/${item.total_quantity} available</span>
        </div>
        <p class="facility-description" title="${item.description || ''}">${description}</p>
      </div>
      <div class="catalog-card-actions">
        <div class="catalog-card-fee mb-2 text-center">
          <i class="bi bi-cash-stack"></i> ₱${parseFloat(item.external_fee).toLocaleString()} (${item.rate_type})
        </div>
        ${getEquipmentButtonHtml(item)}
        <button class="btn btn-outline-secondary">Check Availability</button>
      </div>
    </div>
    `;
  }).join('');
}
    // Pagination copied from facility catalog
    function renderPagination(totalItems) {
      const totalPages = Math.ceil(totalItems / itemsPerPage);
      pagination.innerHTML = "";

      if (totalPages <= 1) return;

      for (let i = 1; i <= totalPages; i++) {
        const pageItem = document.createElement("li");
        pageItem.className = `page-item ${i === currentPage ? "active" : ""}`;
        pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        pageItem.addEventListener("click", (e) => {
          e.preventDefault();
          currentPage = i;
          filterAndRenderItems();
          window.scrollTo({
            top: catalogItemsContainer.offsetTop - 100,
            behavior: "smooth",
          });
        });
        pagination.appendChild(pageItem);
      }
    }

    // Main function to filter and render items
    function filterAndRenderItems() {
      filterItems();
      renderItems(filteredItems);
      renderPagination(filteredItems.length);
    }


    // Get selected items from session
    async function getSelectedItems() {
      try {
        const response = await fetchData("/api/requisition/get-items");
        return response.data || [];
      } catch (e) {
        console.error("Error getting selected items:", e);
        return [];
      }
    }

    // Update cart badge
    function updateCartBadge() {
      const badge = document.getElementById("requisitionBadge");
      if (!badge) return;
      if (selectedItems.length > 0) {
        badge.textContent = selectedItems.length;
        badge.style.display = "";
        badge.classList.remove("d-none");
      } else {
        badge.style.display = "none";
        badge.classList.add("d-none");
      }
    }

    // Main function to refresh UI

    async function updateAllUI() {
      try {
        const response = await fetchData("/api/requisition/get-items");
        selectedItems = response.data?.selected_items || [];
        filterAndRenderItems();
        updateCartBadge();
      } catch (error) {
        console.error("Error updating UI:", error);
      }
    }

// Add item to form with quantity validation
async function addToForm(id, type, quantity = 1) {
  try {
    // Find the equipment item to check available quantity
    const equipmentItem = allEquipment.find(item => item.equipment_id === parseInt(id));
    
    if (!equipmentItem) {
      throw new Error("Equipment item not found");
    }
    
    // Validate quantity against available quantity
    const availableQty = equipmentItem.available_quantity || 0;
    if (quantity > availableQty) {
      throw new Error(`Cannot add ${quantity} items. Only ${availableQty} available.`);
    }
    
    // Validate minimum quantity
    if (quantity < 1) {
      throw new Error("Quantity must be at least 1");
    }

    const requestBody = {
      type: type,
      equipment_id: type === 'equipment' ? parseInt(id) : undefined,
      facility_id: type === 'facility' ? parseInt(id) : undefined,
      quantity: parseInt(quantity)
    };

    const response = await fetchData("/api/requisition/add-item", {
      method: "POST",
      body: JSON.stringify(requestBody)
    });

    if (!response.success) {
      throw new Error(response.message || "Failed to add item");
    }

    selectedItems = response.data.selected_items || [];
    showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} added to form`);
    await updateAllUI();

    // Trigger storage event for cross-page sync
    localStorage.setItem('formUpdated', Date.now().toString());
  } catch (error) {
    console.error("Error adding item:", error);
    showToast(error.message || "Error adding item to form", "error");
  }
}

    // Remove item from form
    async function removeFromForm(id, type) {
      try {
        const requestBody = {
          type: type,
          equipment_id: type === 'equipment' ? parseInt(id) : undefined,
          facility_id: type === 'facility' ? parseInt(id) : undefined
        };

        const response = await fetchData("/api/requisition/remove-item", {
          method: "POST",
          body: JSON.stringify(requestBody)
        });

        if (!response.success) {
          throw new Error(response.message || "Failed to remove item");
        }

        selectedItems = response.data.selected_items || [];
        showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} removed from form`);
        await updateAllUI();

        // Trigger storage event for cross-page sync
        localStorage.setItem('formUpdated', Date.now().toString());
      } catch (error) {
        console.error("Error removing item:", error);
        showToast(error.message || "Error removing item from form", "error");
      }
    }

    // Generate button HTML based on selection state
function getEquipmentButtonHtml(equipment) {
  const isSelected = selectedItems.some(
    item => item.type === 'equipment' && parseInt(item.equipment_id) === equipment.equipment_id
  );

  const selectedItem = isSelected ? selectedItems.find(
    item => item.type === 'equipment' && parseInt(item.equipment_id) === equipment.equipment_id
  ) : null;

  const currentQty = selectedItem ? selectedItem.quantity : 1;
  const maxQty = equipment.available_quantity || 0;
  const isUnavailable = equipment.status.status_id !== 1 || maxQty === 0;

  if (isUnavailable) {
    return `
    <div class="d-flex gap-2 align-items-center">
      <input type="number" 
       class="form-control quantity-input" 
       value="${currentQty}" 
       min="1" 
       max="${maxQty}"
       style="width: 70px;"
       disabled>
      <button class="btn btn-secondary form-action-btn" 
      disabled
      style="cursor: not-allowed; opacity: 0.65;">
      Unavailable
      </button>
    </div>
    `;
  }

  if (isSelected) {
    return `
    <div class="d-flex gap-2 align-items-center">
      <input type="number" 
       class="form-control quantity-input" 
       value="${currentQty}" 
       min="1" 
       max="${maxQty}"
       style="width: 70px;">
      <button class="btn btn-danger add-remove-btn form-action-btn" 
      data-id="${equipment.equipment_id}" 
      data-type="equipment" 
      data-action="remove">
      Remove
      </button>
    </div>
    `;
  } else {
    return `
    <div class="d-flex gap-2 align-items-center">
      <input type="number" 
       class="form-control quantity-input" 
       value="1" 
       min="1" 
       max="${maxQty}"
       style="width: 70px;">
      <button class="btn btn-primary add-remove-btn form-action-btn" 
      data-id="${equipment.equipment_id}" 
      data-type="equipment" 
      data-action="add">
      Add
      </button>
    </div>
    `;
  }
}

    // Event delegation for Add/Remove buttons

    function setupEventListeners() {
      catalogItemsContainer.addEventListener("click", async (e) => {
        const button = e.target.closest(".add-remove-btn");
        if (!button || button.disabled) return;

        const id = button.dataset.id;
        const type = button.dataset.type;
        const action = button.dataset.action;
        const card = button.closest(".catalog-card");
        let quantity = 1;

        if (type === "equipment") {
          const quantityInput = card.querySelector(".quantity-input");
          quantity = parseInt(quantityInput.value) || 1;
        }

        try {
          if (action === "add") {
            await addToForm(id, type, quantity);
          } else if (action === "remove") {
            await removeFromForm(id, type);
          }
          // Force complete refresh
          await updateAllUI();
        } catch (error) {
          console.error("Error handling form action:", error);
        }
      });


// Handle quantity changes with validation
catalogItemsContainer.addEventListener('change', async (e) => {
  if (e.target.classList.contains('quantity-input')) {
    const card = e.target.closest('.catalog-card');
    const button = card.querySelector('.add-remove-btn');
    const id = button.dataset.id;
    const type = button.dataset.type;
    const action = button.dataset.action;
    const quantity = parseInt(e.target.value) || 1;
    
    // Find the equipment item
    const equipmentItem = allEquipment.find(item => item.equipment_id === parseInt(id));
    
    if (equipmentItem) {
      const availableQty = equipmentItem.available_quantity || 0;
      
      // Validate quantity
      if (quantity > availableQty) {
        showToast(`Cannot select ${quantity} items. Only ${availableQty} available.`, "error");
        // Reset to previous valid quantity or available max
        const selectedItem = selectedItems.find(
          item => item.type === 'equipment' && parseInt(item.equipment_id) === parseInt(id)
        );
        e.target.value = selectedItem ? selectedItem.quantity : Math.min(1, availableQty);
        return;
      }
      
      if (quantity < 1) {
        showToast("Quantity must be at least 1", "error");
        e.target.value = 1;
        return;
      }
    }

    if (action === 'remove') {
      await removeFromForm(id, type);
      await addToForm(id, type, quantity);
      await updateAllUI();
    }
  }
});

catalogItemsContainer.addEventListener('input', (e) => {
  if (e.target.classList.contains('quantity-input')) {
    const card = e.target.closest('.catalog-card');
    const button = card.querySelector('.add-remove-btn');
    const id = button.dataset.id;
    const quantity = parseInt(e.target.value) || 0;
    
    // Find the equipment item
    const equipmentItem = allEquipment.find(item => item.equipment_id === parseInt(id));
    
    if (equipmentItem) {
      const availableQty = equipmentItem.available_quantity || 0;
      
      // Validate in real-time
      if (quantity > availableQty) {
        e.target.classList.add('is-invalid');
        // Show inline error message
        let errorMsg = card.querySelector('.quantity-error');
        if (!errorMsg) {
          errorMsg = document.createElement('div');
          errorMsg.className = 'quantity-error text-danger small mt-1';
          e.target.parentNode.appendChild(errorMsg);
        }
        errorMsg.textContent = `Max: ${availableQty}`;
      } else {
        e.target.classList.remove('is-invalid');
        const errorMsg = card.querySelector('.quantity-error');
        if (errorMsg) errorMsg.remove();
      }
      
      if (quantity < 1) {
        e.target.classList.add('is-invalid');
      }
    }
  }
});
    }

   
// Initialize availability calendar for specific item
function initializeAvailabilityCalendar(itemId, itemType, itemName) {
  const calendarEl = document.getElementById('availabilityCalendar');
  if (!calendarEl) return;

  // Clear any existing content first and ensure proper structure
  calendarEl.innerHTML = '<div class="calendar-inner-container" style="height: 100%; position: relative;"></div>';
  const calendarInner = calendarEl.querySelector('.calendar-inner-container');

  // Create and show loading overlay - append to inner container
  const loadingOverlay = document.createElement('div');
  loadingOverlay.className = 'calendar-loading-overlay';
  loadingOverlay.innerHTML = `
    <div class="calendar-loading-content">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading availability data...</p>
    </div>
  `;

  // Add loading overlay to INNER container (not outer)
  calendarInner.appendChild(loadingOverlay);

  // Hide the calendar initially
  calendarInner.classList.add('calendar-hidden');

  const calendar = new FullCalendar.Calendar(calendarInner, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    titleFormat: {
      year: 'numeric',
      month: 'long'
    },
    buttonText: {
      today: 'Today',
      month: 'Month',
      week: 'Week',
      day: 'Day'
    },
    height: '100%',
    handleWindowResize: true,
    windowResizeDelay: 100,
    aspectRatio: 1.5,
    expandRows: true,
    events: function (fetchInfo, successCallback, failureCallback) {
      // Ensure loading overlay is visible and calendar is hidden
      loadingOverlay.style.display = 'flex';
      calendarInner.classList.add('calendar-hidden');
      
      // Fetch events filtered by specific item
      fetch(`/api/requisition-forms/calendar-events?${itemType}_id=${itemId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            successCallback(data.data);
          } else {
            failureCallback(data.message);
          }
        })
        .catch(error => {
          failureCallback('Failed to load availability data');
          console.error('Availability calendar error:', error);
        })
        .finally(() => {
          // Hide loading overlay and show calendar when everything is ready
          setTimeout(() => {
            loadingOverlay.style.display = 'none';
            calendarInner.classList.remove('calendar-hidden');
            calendarInner.classList.add('calendar-visible');
            calendar.updateSize();
          }, 500);
        });
    },
    loading: function(isLoading) {
      if (isLoading) {
        loadingOverlay.style.display = 'flex';
        calendarInner.classList.add('calendar-hidden');
      } else {
        setTimeout(() => {
          loadingOverlay.style.display = 'none';
          calendarInner.classList.remove('calendar-hidden');
          calendarInner.classList.add('calendar-visible');
        }, 500);
      }
    },
    eventClick: function (info) {
      showEventModal(info.event);
    },
    eventDidMount: function (info) {
      info.el.style.backgroundColor = info.event.backgroundColor;
      info.el.style.borderColor = info.event.borderColor;
      info.el.style.color = '#fff';
      info.el.style.fontWeight = 'bold';
      info.el.style.borderRadius = '4px';
      info.el.style.padding = '2px 4px';
      info.el.style.fontSize = '12px';
    },
    datesSet: function (info) {
      setTimeout(() => {
        calendar.updateSize();
      }, 50);
    },
    viewDidMount: function (info) {
      setTimeout(() => {
        calendar.updateSize();
      }, 100);
    },
    eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    },
    slotMinTime: '06:00:00',
    slotMaxTime: '22:00:00',
    allDaySlot: false,
    nowIndicator: true,
    navLinks: true,
    dayHeaderFormat: {
      weekday: 'long',
      month: 'short',
      day: 'numeric'
    },
    slotLabelFormat: {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    },
    views: {
      dayGridMonth: {
        dayHeaderFormat: { weekday: 'short' },
        fixedWeekCount: false
      },
      timeGridWeek: {
        dayHeaderFormat: {
          weekday: 'short',
          month: 'short',
          day: 'numeric'
        },
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00'
      },
      timeGridDay: {
        dayHeaderFormat: {
          weekday: 'long',
          month: 'short',
          day: 'numeric'
        },
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00'
      }
    },
    eventDisplay: 'block',
    dayMaxEvents: 3,
    moreLinkClick: 'popover',
    slotDuration: '01:00:00',
    slotLabelInterval: '01:00:00'
  });

  calendar.render();
  
  // Update modal title with item name
  document.getElementById('availabilityModalLabel').textContent = `Availability - ${itemName}`;

  return calendar;
}

function setupAvailabilityButtons() {
  catalogItemsContainer.addEventListener("click", (e) => {
    if ((e.target.classList.contains("btn-custom") || e.target.classList.contains("btn-outline-secondary")) && 
        e.target.textContent === "Check Availability") {
      const card = e.target.closest(".catalog-card");
      const itemId = card.querySelector(".add-remove-btn")?.dataset.id;
      const itemType = card.querySelector(".add-remove-btn")?.dataset.type;
      const itemName = card.querySelector("h5")?.textContent.trim();
      
      if (itemId && itemType) {
        showAvailabilityCalendar(itemId, itemType, itemName);
      }
    }
  });
}

// Function to show availability modal
function showAvailabilityCalendar(itemId, itemType, itemName) {
  const modal = new bootstrap.Modal(document.getElementById('availabilityModal'));
  let availabilityCalendar = null;
  
  document.getElementById('availabilityModal').addEventListener('shown.bs.modal', function () {
    if (!availabilityCalendar) {
      availabilityCalendar = initializeAvailabilityCalendar(itemId, itemType, itemName);
    } else {
      availabilityCalendar.refetchEvents();
      availabilityCalendar.updateSize();
    }
  });
  
  modal.show();
}


    // Main Initialization

async function init() {
  try {
    const [equipmentData, categoriesData, selectedItemsResponse] = await Promise.all([
      fetchData('/api/equipment'),
      fetchData('/api/equipment-categories'),
      fetchData('/api/requisition/get-items')
    ]);

    // Store the complete equipment data for validation
    allEquipment = equipmentData.data || [];

    equipmentCategories = categoriesData || [];
    selectedItems = selectedItemsResponse.data?.selected_items || [];

    renderCategoryFilters();
    filterAndRenderItems();
    setupEventListeners();
    updateCartBadge();
    setupAvailabilityButtons();

    catalogItemsContainer.classList.remove("d-none");
  } catch (error) {
    console.error("Error initializing page:", error);
    showToast("Failed to initialize the page. Please try again.", "error");
  } finally {
    loadingIndicator.style.display = "none";
  }
}

    // Initialize when DOM is loaded
    document.addEventListener("DOMContentLoaded", init);

    // Layout toggle
    document.querySelectorAll(".layout-option").forEach((option) => {
      option.addEventListener("click", (e) => {
        e.preventDefault();
        currentLayout = option.dataset.layout;
        // Set active class and update layoutDropdown button text
        document.querySelectorAll(".layout-option").forEach((opt) => opt.classList.remove("active"));
        option.classList.add("active");
        const layoutDropdownBtn = document.getElementById("layoutDropdown");
        if (currentLayout === "grid") {
          layoutDropdownBtn.textContent = "Grid Layout";
        } else {
          layoutDropdownBtn.textContent = "List Layout";
        }
        filterAndRenderItems();
      });
    });

    // Set initial layoutDropdown button text and active class on DOMContentLoaded
    document.addEventListener("DOMContentLoaded", function () {
      // ...existing code...
      // Set initial layoutDropdown button text and active class
      const layoutDropdownBtn = document.getElementById("layoutDropdown");
      if (currentLayout === "grid") {
        layoutDropdownBtn.textContent = "Grid Layout";
        document.querySelectorAll(".layout-option").forEach(opt => {
          if (opt.dataset.layout === "grid") opt.classList.add("active");
          else opt.classList.remove("active");
        });
      } else {
        layoutDropdownBtn.textContent = "List Layout";
        document.querySelectorAll(".layout-option").forEach(opt => {
          if (opt.dataset.layout === "list") opt.classList.add("active");
          else opt.classList.remove("active");
        });
      }
    });

    // Status dropdown filter
    document.querySelectorAll("#statusFilterMenu .status-option").forEach((option) => {
      option.addEventListener("click", (e) => {
        e.preventDefault();
        statusFilter = option.dataset.status;
        // Remove active from all, add to selected
        document.querySelectorAll("#statusFilterMenu .status-option").forEach(opt => opt.classList.remove("active"));
        option.classList.add("active");
        // Update dropdown display
        document.getElementById("statusDropdown").textContent =
          "Status: " + (statusFilter === "All" ? "All" : statusFilter);
        filterAndRenderItems();
      });
    });

    // Set initial active status option and dropdown display
    document.addEventListener("DOMContentLoaded", function () {
      // Set initial active status option
      document.querySelectorAll("#statusFilterMenu .status-option").forEach(opt => {
        if (opt.dataset.status === statusFilter) {
          opt.classList.add("active");
          document.getElementById("statusDropdown").textContent =
            "Status: " + (statusFilter === "All" ? "All" : statusFilter);
        } else {
          opt.classList.remove("active");
        }
      });
    });

    
    // FullCalendar initialization for user view
    function initializeUserCalendar() {
      const calendarEl = document.getElementById('userFullCalendar');
      if (!calendarEl) return;

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        titleFormat: {
          year: 'numeric',
          month: 'short'
        },
        height: 'auto', // Changed from '100%' to 'auto' for modal
        handleWindowResize: true,
        windowResizeDelay: 100, // Reduced delay for better modal responsiveness
        aspectRatio: 1.5, // Set explicit aspect ratio instead of null
        expandRows: false, // Changed to false for modal
        events: function (fetchInfo, successCallback, failureCallback) {
          fetch('/api/requisition-forms/calendar-events')
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                successCallback(data.data);
              } else {
                failureCallback(data.message);
              }
            })
            .catch(error => {
              failureCallback('Failed to load events');
              console.error('Calendar events error:', error);
            });
        },
        eventClick: function (info) {
          showEventModal(info.event);
        },
        eventDidMount: function (info) {
          // Add custom styling based on status
          info.el.style.backgroundColor = info.event.backgroundColor;
          info.el.style.borderColor = info.event.borderColor;
          info.el.style.color = '#fff';
          info.el.style.fontWeight = 'bold';
          info.el.style.borderRadius = '4px';
          info.el.style.padding = '2px 4px';
          info.el.style.fontSize = '12px';
        },
        datesSet: function (info) {
          // Force update size after render
          setTimeout(() => {
            calendar.updateSize();
          }, 50);
        },
        viewDidMount: function (info) {
          // Ensure proper sizing in modal
          setTimeout(() => {
            calendar.updateSize();
          }, 100);
        },
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        },
        slotMinTime: '06:00:00', // Adjusted for better visibility
        slotMaxTime: '22:00:00', // Adjusted for better visibility
        allDaySlot: false,
        nowIndicator: true,
        navLinks: true,
        dayHeaderFormat: {
          weekday: 'long',
          month: 'short',
          day: 'numeric'
        },
        slotLabelFormat: {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        },
        views: {
          dayGridMonth: {
            dayHeaderFormat: {
              weekday: 'short'
            },
            fixedWeekCount: false // Don't always show 6 weeks
          },
          timeGridWeek: {
            dayHeaderFormat: {
              weekday: 'short',
              month: 'short',
              day: 'numeric'
            },
            slotMinTime: '00:00:00',
            slotMaxTime: '24:00:00'
          },
          timeGridDay: {
            dayHeaderFormat: {
              weekday: 'long',
              month: 'short',
              day: 'numeric'
            },
            slotMinTime: '06:00:00',
            slotMaxTime: '22:00:00'
          }
        },
        eventDisplay: 'block',
        dayMaxEvents: 3, // Reduced for modal
        moreLinkClick: 'popover',
        slotDuration: '01:00:00', // Explicit slot duration
        slotLabelInterval: '01:00:00' // Explicit label interval
      });

      calendar.render();

      // Force initial size update
      setTimeout(() => {
        calendar.updateSize();
      }, 200);

      return calendar;
    }

    // Event listener for calendar modal
    document.addEventListener('DOMContentLoaded', function () {
      const calendarModal = document.getElementById('userCalendarModal');
      let userCalendar = null;

      if (calendarModal) {
        calendarModal.addEventListener('shown.bs.modal', function () {
          if (!userCalendar) {
            userCalendar = initializeUserCalendar();
          } else {
            userCalendar.updateSize();
          }
        });
      }
    });
  </script>
   <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
@endsection
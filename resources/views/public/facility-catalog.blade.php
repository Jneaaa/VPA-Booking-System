@extends('layouts.app')

@section('title', 'Booking Catalog - Facilities')

@section('content')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public/catalog.css') }}" />
  <style>

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


    .list-layout .catalog-card {
      flex-direction: row;
      align-items: stretch;
      gap: 1rem;
      height: 200px;
      overflow: hidden;
      padding: 0.25rem !important;
      box-sizing: border-box;
      position: relative;
    }

    .list-layout .catalog-card-img {
      width: 200px;
      height: 190px !important;
      /* Force exact height */
      object-fit: cover;
      flex-shrink: 0;
      box-sizing: border-box;
    }

    .list-layout .catalog-card-details {
      flex: 1;
      min-width: 0;
      /* Prevent flex item from overflowing */
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
      justify-content: center;
      /* Center vertically */
      height: 100%;
      /* Ensure actions take full card height */
      box-sizing: border-box;
      /* Include padding in height calculation */
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
      align-self: flex-start;
      /* Prevents stretching */
      width: auto !important;
      /* Prevents flex stretching */
      white-space: nowrap;
      /* Prevents text wrapping */
      padding: 0.25rem 0.75rem;
      /* Consistent padding */
      margin-bottom: 0.5rem;
      /* Space below banner */
    }

    .facility-description {
      flex-grow: 1;
      /* takes remaining space so the fee + buttons stay at bottom */
    }

    /* Fee and actions section */
    .catalog-card-fee,
    .catalog-card-actions {
      margin-top: auto;
      /* push to bottom */
    }

    .catalog-card-actions {
      margin-top: auto;
      /* pushes the buttons to the bottom */
      display: flex;
      justify-content: space-between;
      gap: 0.5rem;
      padding: 0.75rem;
      border-top: 1px solid #eee;
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
      <h2 id="catalogHeroTitle">Facility Catalog</h2>
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


        <!-- Main Content Area (Top Bar) -->
        <div class="col-lg-9 col-md-8">
          <div class="right-content-header">
            <div class="dropdown">
              <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" id="chooseCatalogDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                All Facilities
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
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="All">All</a>
                  </li>
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="Available">Available</a>
                  </li>
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="Unavailable">Unavailable</a>
                  </li>
                </ul>
              </div>
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                  id="layoutDropdown">
                  Grid Layout
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item layout-option active" href="#" data-layout="grid">Grid</a>
                  </li>
                  <li>
                    <a class="dropdown-item layout-option" href="#" data-layout="list">List</a>
                  </li>
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

          <!-- Facility Detail Modal -->
          <div class="modal fade" id="facilityDetailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="facilityDetailModalLabel">Facility Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="facilityDetailContent">
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

          <!-- Pagination -->
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
  <div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel"
    aria-hidden="true">
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
    let allFacilities = [];
    let facilityCategories = [];
    let filteredItems = [];
    let currentLayout = "grid";
    let selectedItems = []; // Declared globally
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
    // toast system
    function showToast(message, type = "success", duration = 3000) {
      const toast = document.createElement("div");

      // Toast base styles (bottom right)
      toast.className = `toast align-items-center border-0 position-fixed end-0 mb-2`;
      toast.style.zIndex = "1100";
      toast.style.bottom = "0";
      toast.style.right = "0";
      toast.style.margin = "1rem";
      toast.style.opacity = "0";
      toast.style.transform = "translateY(20px)";
      toast.style.transition = "transform 0.4s ease, opacity 0.4s ease";
      toast.setAttribute("role", "alert");
      toast.setAttribute("aria-live", "assertive");
      toast.setAttribute("aria-atomic", "true");

      // Custom colors
      const bgColor = type === "success" ? "#004183ff" : "#dc3545";
      toast.style.backgroundColor = bgColor;
      toast.style.color = "#fff";
      toast.style.minWidth = "250px";
      toast.style.borderRadius = "0.3rem";

      toast.innerHTML = `
                <div class="d-flex align-items-center px-3 py-1"> 
                  <i class="bi ${type === "success" ? "bi-check-circle-fill" : "bi-exclamation-circle-fill"} me-2"></i>
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
        toast.style.opacity = "1";
        toast.style.transform = "translateY(0)";
      });

      // Start loading bar animation
      const loadingBar = toast.querySelector(".loading-bar");
      requestAnimationFrame(() => {
        loadingBar.style.width = "0%";
      });

      // Remove after duration
      setTimeout(() => {
        // Float down disappear animation
        toast.style.opacity = "0";
        toast.style.transform = "translateY(20px)";

        setTimeout(() => {
          bsToast.hide();
          toast.remove();
        }, 400); // matches animation time
      }, duration);
    }

    function showError(message) {
      showToast(message, "error");
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

    // Add item to form
    async function addToForm(id, type, quantity = 1) {
      try {
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

    function getFacilityButtonHtml(facility) {
      const isUnavailable = facility.status.status_id === 2; // Status ID 2 = Unavailable
      const isSelected = selectedItems.some(
        item => item.type === 'facility' && parseInt(item.facility_id) === facility.facility_id
      );

      if (isUnavailable) {
        return `
                <button class="btn btn-secondary add-remove-btn" 
                data-id="${facility.facility_id}" 
                data-type="facility" 
                disabled
                style="cursor: not-allowed; opacity: 0.65;">
                Unavailable
                </button>
                `;
      }

      if (isSelected) {
        return `
                <button class="btn btn-danger add-remove-btn" 
                data-id="${facility.facility_id}" 
                data-type="facility" 
                data-action="remove">
                Remove from form
                </button>
                `;
      } else {
        return `
                <button class="btn btn-primary add-remove-btn" 
                data-id="${facility.facility_id}" 
                data-type="facility" 
                data-action="add">
                Add to form
                </button>
                `;
      }
    }

    // Event delegation for Add/Remove buttons
    function setupEventListeners() {
      // Handle Add/Remove buttons
      document.addEventListener("click", async (e) => {
        const button = e.target.closest(".add-remove-btn");
        if (!button || button.disabled) return;

        const id = button.dataset.id;
        const type = button.dataset.type;
        const action = button.dataset.action;

        try {
          if (action === "add") {
            await addToForm(id, type);
          } else if (action === "remove") {
            await removeFromForm(id, type);
          }
          // Force a complete refresh after modification
          await updateAllUI();
        } catch (error) {
          console.error("Error handling form action:", error);
        }
      });
    }

    // Render Functions
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

      // Render categories and subcategories
      facilityCategories.forEach((category) => {
        const categoryItem = document.createElement("div");
        categoryItem.className = "category-item";
        categoryItem.innerHTML = `
                <div class="form-check d-flex justify-content-between align-items-center">
                <div>
                <input class="form-check-input category-filter" type="checkbox" id="category${category.category_id}" value="${category.category_id}">
                <label class="form-check-label" for="category${category.category_id}">${category.category_name}</label>
                </div>
                <i class="bi bi-chevron-up toggle-arrow" style="cursor:pointer"></i>
                </div>
                <div class="subcategory-list ms-3" style="overflow: hidden; max-height: 0; transition: max-height 0.3s ease;">
                ${category.subcategories.map(sub => `
                <div class="form-check">
                <input class="form-check-input subcategory-filter" type="checkbox" id="subcategory${sub.subcategory_id}" value="${sub.subcategory_id}" data-category="${category.category_id}">
                <label class="form-check-label" for="subcategory${sub.subcategory_id}">${sub.subcategory_name}</label>
                </div>
                `).join("")}
                </div>
              `;
        categoryFilterList.appendChild(categoryItem);

        // Toggle subcategory list
        const toggleArrow = categoryItem.querySelector(".toggle-arrow");
        const subcategoryList = categoryItem.querySelector(".subcategory-list");
        // --- Make subcategory list expanded by default ---
        subcategoryList.style.maxHeight = `${subcategoryList.scrollHeight}px`;
        // Also ensure the arrow is "up" (expanded)
        toggleArrow.classList.remove("bi-chevron-up");
        toggleArrow.classList.add("bi-chevron-down");

        toggleArrow.addEventListener("click", function () {
          const isExpanded = subcategoryList.style.maxHeight !== "0px";
          if (isExpanded) {
            subcategoryList.style.maxHeight = "0";
          } else {
            subcategoryList.style.maxHeight = `${subcategoryList.scrollHeight}px`;
          }
          toggleArrow.classList.toggle("bi-chevron-down");
          toggleArrow.classList.toggle("bi-chevron-up");
        });
      });

      // --- Filtering Logic ---
      const allCategoriesCheckbox = document.getElementById("allCategories");
      const categoryCheckboxes = Array.from(document.querySelectorAll('.category-filter')).filter(cb => cb.id !== "allCategories");
      const subcategoryCheckboxes = Array.from(document.querySelectorAll('.subcategory-filter'));

      // Helper: update All Categories checkbox state
      function updateAllCategoriesCheckbox() {
        const anyChecked = categoryCheckboxes.some(c => c.checked) || subcategoryCheckboxes.some(s => s.checked);
        if (anyChecked) {
          allCategoriesCheckbox.checked = false;
          allCategoriesCheckbox.disabled = false;
        } else {
          allCategoriesCheckbox.checked = true;
          allCategoriesCheckbox.disabled = true;
        }
      }

      // Helper: update category checkbox state based on its subcategories
      function updateCategoryCheckboxState(catId) {
        const catCheckbox = document.getElementById("category" + catId);
        const relatedSubs = subcategoryCheckboxes.filter(sub => sub.dataset.category === catId);
        const anySubChecked = relatedSubs.some(sub => sub.checked);
        catCheckbox.checked = anySubChecked;
        catCheckbox.disabled = relatedSubs.every(sub => sub.disabled);
        // Remove bold if disabled
        const label = catCheckbox.nextElementSibling;
        if (catCheckbox.disabled) {
          label.style.fontWeight = "";
        }
      }

      // When a category is checked/unchecked, enable/disable its subcategories
      categoryCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
          const catId = cb.value;
          const relatedSubs = subcategoryCheckboxes.filter(sub => sub.dataset.category === catId);
          if (!cb.checked) {
            relatedSubs.forEach(sub => {
              sub.checked = false;
              sub.disabled = true;
              // Remove bold if disabled
              sub.nextElementSibling.style.fontWeight = "";
            });
          } else {
            relatedSubs.forEach(sub => {
              sub.disabled = false;
            });
          }
          updateAllCategoriesCheckbox();
          filterAndRenderItems();
        });
      });

      // When a subcategory is checked/unchecked, update parent category and All Categories
      subcategoryCheckboxes.forEach(sub => {
        sub.addEventListener('change', function () {
          const catId = sub.dataset.category;
          updateCategoryCheckboxState(catId);
          updateAllCategoriesCheckbox();
          filterAndRenderItems();
        });
      });

      // When "All Categories" is checked
      allCategoriesCheckbox.addEventListener('change', function () {
        if (this.checked) {
          categoryCheckboxes.forEach(cb => {
            cb.checked = false;
            cb.disabled = false;
            cb.nextElementSibling.style.fontWeight = "";
          });
          subcategoryCheckboxes.forEach(sub => {
            sub.checked = false;
            sub.disabled = false;
            sub.nextElementSibling.style.fontWeight = "";
          });
          allCategoriesCheckbox.disabled = true;
          filterAndRenderItems();
        }
      });

      // Initial state: only "All Categories" checked and disabled, all others unchecked/enabled
      allCategoriesCheckbox.checked = true;
      allCategoriesCheckbox.disabled = true;
      categoryCheckboxes.forEach(cb => { cb.checked = false; cb.disabled = false; cb.nextElementSibling.style.fontWeight = ""; });
      subcategoryCheckboxes.forEach(sub => { sub.checked = false; sub.disabled = false; sub.nextElementSibling.style.fontWeight = ""; });
    }

    function filterItems() {
      const allCategoriesCheckbox = document.getElementById("allCategories");
      const categoryCheckboxes = Array.from(document.querySelectorAll('.category-filter')).filter(cb => cb.id !== "allCategories");
      const subcategoryCheckboxes = Array.from(document.querySelectorAll('.subcategory-filter'));

      // Only keep allowed status
      filteredItems = [...allFacilities].filter(f => allowedStatusIds.includes(f.status.status_id));

      // Filter by status dropdown
      if (statusFilter === "Available") {
        filteredItems = filteredItems.filter(f => f.status.status_id === 1);
      } else if (statusFilter === "Unavailable") {
        filteredItems = filteredItems.filter(f => f.status.status_id === 2);
      }

      // Category/subcategory filtering
      if (allCategoriesCheckbox.checked) {
        return filteredItems;
      }

      const selectedCategories = categoryCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
      const selectedSubcategories = subcategoryCheckboxes.filter(cb => cb.checked).map(cb => cb.value);

      if (selectedCategories.length === 0 && selectedSubcategories.length === 0) {
        filteredItems = [];
        return filteredItems;
      }

      filteredItems = filteredItems.filter(facility => {
        // If subcategories are selected, match subcategory
        if (selectedSubcategories.length > 0 && facility.subcategory) {
          if (selectedSubcategories.includes(facility.subcategory.subcategory_id.toString())) {
            return true;
          }
        }
        // If categories are selected, match category
        if (selectedCategories.length > 0) {
          if (selectedCategories.includes(facility.category.category_id.toString())) {
            return true;
          }
        }
        return false;
      });

      return filteredItems;
    }

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
        <i class="bi bi-building fs-1 text-muted"></i>
        <h4 class="mt-2">No facilities found</h4>
      </div>
    `;
    return;
  }

  // normal render path
  catalogItemsContainer.classList.remove("grid-layout", "list-layout");
  catalogItemsContainer.classList.add(`${currentLayout}-layout`);

  if (currentLayout === "grid") {
    renderFacilitiesGrid(paginatedItems);
  } else {
    renderFacilitiesList(paginatedItems);
  }

  // add click events
  document.querySelectorAll(".catalog-card-details h5").forEach((title) => {
    title.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      showFacilityDetails(id);
    });
  });
}

    function renderFacilitiesGrid(facilities) {
      catalogItemsContainer.innerHTML = facilities
        .map((facility) => {
          const primaryImage =
            facility.images?.find((img) => img.image_type === "Primary")
              ?.image_url ||
            "https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png";

          // Truncate facility name (max 18 characters)
          const facilityName = facility.facility_name.length > 18
            ? facility.facility_name.substring(0, 18) + "..."
            : facility.facility_name;

          // Truncate description (max 100 characters)
          const description = facility.description
            ? (facility.description.length > 100
              ? facility.description.substring(0, 100) + "..."
              : facility.description)
            : "No description available.";

          return `
            <div class="catalog-card">
              <img src="${primaryImage}" 
                   alt="${facility.facility_name}" 
                   class="catalog-card-img"
                   onerror="this.src='https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png'">
              <div class="catalog-card-details">
                <h5 data-id="${facility.facility_id}" title="${facility.facility_name}">${facilityName}</h5>
                <span class="status-banner" style="background-color: ${facility.status.color_code}">
                  ${facility.status.status_name}
                </span>
                <div class="catalog-card-meta">
                  <span><i class="bi bi-people-fill"></i> ${facility.capacity || "N/A"}</span>
                  <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || facility.category.category_name}</span>
                </div>
                <p class="facility-description" title="${facility.description || ''}">${description}</p>
                <div class="catalog-card-fee">
                  <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
                </div>
              </div>
              <div class="catalog-card-actions">
                ${getFacilityButtonHtml(facility)}
                <button class="btn btn-light btn-custom">Check Availability</button>
              </div>
            </div>
            `;
        })
        .join("");
    }

    function renderFacilitiesList(facilities) {
      catalogItemsContainer.innerHTML = facilities
        .map((facility) => {
          const primaryImage =
            facility.images?.find((img) => img.image_type === "Primary")
              ?.image_url ||
            "https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png";

          // Truncate facility name (max 30 characters)
          const facilityName = facility.facility_name.length > 30
            ? facility.facility_name.substring(0, 30) + "..."
            : facility.facility_name;

          // Truncate description (max 150 characters)
          const description = facility.description
            ? (facility.description.length > 150
              ? facility.description.substring(0, 150) + "..."
              : facility.description)
            : "No description available.";

          return `
            <div class="catalog-card">
              <img src="${primaryImage}" 
                   alt="${facility.facility_name}" 
                   class="catalog-card-img"
                   onerror="this.src='https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png'">
              <div class="catalog-card-details">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 data-id="${facility.facility_id}" title="${facility.facility_name}">${facilityName}</h5>
                  <span class="status-banner" style="background-color: ${facility.status.color_code}">
                    ${facility.status.status_name}
                  </span>
                </div>
                <div class="catalog-card-meta">
                  <span><i class="bi bi-people-fill"></i> ${facility.capacity || "N/A"}</span>
                  <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || facility.category.category_name}</span>
                </div>
                <p class="facility-description" title="${facility.description || ''}">${description}</p>
              </div>
              <div class="catalog-card-actions">
                <div class="catalog-card-fee mb-2 text-center">
                  <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
                </div>
                ${getFacilityButtonHtml(facility)}
                <button class="btn btn-outline-secondary">Check Availability</button>
              </div>
            </div>
            `;
        })
        .join("");
    }

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

    // Main function to filter, render, and update pagination
    function filterAndRenderItems() {
      filterItems();
      renderItems(filteredItems);
      renderPagination(filteredItems.length);
    }

    // Event Handlers
    function setupEventListeners() {
      // Event delegation for Add/Remove buttons
      catalogItemsContainer.addEventListener("click", async (e) => {
        const button = e.target.closest(".add-remove-btn");
        if (!button) return;

        const id = button.dataset.id;
        const type = button.dataset.type;
        const action = button.dataset.action;

        try {
          if (action === "add") {
            await addToForm(id, type);
          } else if (action === "remove") {
            await removeFromForm(id, type);
          }
        } catch (error) {
          console.error("Error handling form action:", error);
        }
      });
    }

    // Category and subcategory filters
    document.addEventListener("change", function (e) {
      if (
        e.target.classList.contains("category-filter") ||
        e.target.classList.contains("subcategory-filter")
      ) {
        const label = e.target.nextElementSibling;
        if (e.target.checked) {
          label.style.fontWeight = "bold";
        } else {
          label.style.fontWeight = "";
        }
        currentPage = 1;
        filterAndRenderItems();
      }
    });

    // Layout toggle
    document.querySelectorAll(".layout-option").forEach((option) => {
      option.addEventListener("click", (e) => {
        e.preventDefault();
        currentLayout = option.dataset.layout;
        // Set active class and update layoutDropdown button text
        document.querySelectorAll(".layout-option").forEach(opt => opt.classList.remove("active"));
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
      // Initialize modal after Bootstrap is loaded
      facilityDetailModal = new bootstrap.Modal(
        document.getElementById("facilityDetailModal"),
        {
          keyboard: true,
          backdrop: true,
        }
      );

      init();

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

    async function showFacilityDetails(facilityId) {
      try {
        const facility = allFacilities.find((f) => f.facility_id == facilityId);
        if (!facility) return;

        const primaryImage = facility.images?.find((img) => img.image_type === "Primary")?.image_url || "https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png";
        const isUnavailable = facility.status.status_id === 2;
        const isSelected = selectedItems.some(
          (selectedItem) =>
            parseInt(selectedItem.id) === facility.facility_id &&
            selectedItem.type === "facility"
        );

        document.getElementById("facilityDetailModalLabel").textContent = facility.facility_name;
        document.getElementById("facilityDetailContent").innerHTML = `
                <div class="row">
                <div class="col-md-6">
                <img src="${primaryImage}" alt="${facility.facility_name}" class="facility-image img-fluid">
                </div>
                <div class="col-md-6">
                <div class="facility-details">
                <p><strong>Status:</strong> <span class="badge" style="background-color: ${facility.status.color_code}">${facility.status.status_name}</span></p>
                <p><strong>Category:</strong> ${facility.category.category_name}</p>
                <p><strong>Subcategory:</strong> ${facility.subcategory?.subcategory_name || "N/A"}</p>
                <p><strong>Capacity:</strong> ${facility.capacity}</p>
                <p><strong>Rate:</strong> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})</p>
                <p><strong>Description:</strong></p>
                <p>${facility.description || "No description available."}</p>
                </div>
                <div class="mt-3">
                ${isUnavailable
            ? `<button class="btn btn-secondary" disabled style="cursor: not-allowed; opacity: 0.65;">Unavailable</button>`
            : `<button class="btn ${isSelected ? "btn-danger" : "btn-primary"} add-remove-btn" 
                data-id="${facility.facility_id}" 
                data-type="facility" 
                data-action="${isSelected ? "remove" : "add"}">
                ${isSelected ? "Remove from Form" : "Add to Form"}
                </button>`}
                </div>
                </div>
                </div>
              `;
        facilityDetailModal.show();
      } catch (error) {
        console.error("Error showing facility details:", error);
        showError("Failed to load facility details.");
      }
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
  const modalElement = document.getElementById('availabilityModal');
  const modal = new bootstrap.Modal(modalElement);
  let availabilityCalendar = null;

  // Clear previous calendar on hide
  modalElement.addEventListener('hidden.bs.modal', function () {
    if (availabilityCalendar) {
      availabilityCalendar.destroy();
      availabilityCalendar = null;
      document.getElementById('availabilityCalendar').innerHTML = '';
    }
  });

  // Initialize calendar when modal is shown
  modalElement.addEventListener('shown.bs.modal', function () {
    // Small delay to ensure modal is fully visible
    setTimeout(() => {
      availabilityCalendar = initializeAvailabilityCalendar(itemId, itemType, itemName);
    }, 50);
  });

  modal.show();
}


    // Main Initialization
    async function init() {
      try {
        const [facilitiesData, facilityCategoriesData, selectedItemsResponse] = await Promise.all([
          fetchData('/api/facilities'),
          fetchData('/api/facility-categories/index'),
          fetchData('/api/requisition/get-items')
        ]);

        // Only keep facilities with status_id 1 or 2
        allFacilities = (facilitiesData.data || []).filter(f => allowedStatusIds.includes(f.status.status_id));
        facilityCategories = facilityCategoriesData || [];
        selectedItems = selectedItemsResponse.data?.selected_items || []; // Updated to match new response structure

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

    function filterItems() {
      const allCategoriesCheckbox = document.getElementById("allCategories");
      const categoryCheckboxes = Array.from(document.querySelectorAll('.category-filter')).filter(cb => cb.id !== "allCategories");
      const subcategoryCheckboxes = Array.from(document.querySelectorAll('.subcategory-filter'));

      // Only keep allowed status
      filteredItems = [...allFacilities].filter(f => allowedStatusIds.includes(f.status.status_id));

      // Filter by status dropdown
      if (statusFilter === "Available") {
        filteredItems = filteredItems.filter(f => f.status.status_id === 1);
      } else if (statusFilter === "Unavailable") {
        filteredItems = filteredItems.filter(f => f.status.status_id === 2);
      }

      // Category/subcategory filtering
      if (allCategoriesCheckbox.checked) {
        return filteredItems;
      }

      const selectedCategories = categoryCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
      const selectedSubcategories = subcategoryCheckboxes.filter(cb => cb.checked).map(cb => cb.value);

      if (selectedCategories.length === 0 && selectedSubcategories.length === 0) {
        filteredItems = [];
        return filteredItems;
      }

      filteredItems = filteredItems.filter(facility => {
        // If subcategories are selected, match subcategory
        if (selectedSubcategories.length > 0 && facility.subcategory) {
          if (selectedSubcategories.includes(facility.subcategory.subcategory_id.toString())) {
            return true;
          }
        }
        // If categories are selected, match category
        if (selectedCategories.length > 0) {
          if (selectedCategories.includes(facility.category.category_id.toString())) {
            return true;
          }
        }
        return false;
      });

      return filteredItems;
    }

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
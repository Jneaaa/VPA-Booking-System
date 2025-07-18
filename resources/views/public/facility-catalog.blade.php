<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Catalog - Facilities</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public/catalog.css') }}" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .main-content {
      padding-top: 20px;
    }

    .form-section-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    * {
      border-radius: 0 !important;
    }

    .profile-img,
    .status-indicator {
      border-radius: 50% !important;
    }

    #userCalendarModal .modal-dialog {
      max-width: 90vw;
      width: 100%;
    }

    #userCalendarModal .modal-body {
      height: 75vh;
      overflow-y: auto;
      padding: 0;
      margin: 0;
    }

    #userFullCalendar {
      width: 100%;
      height: 100%;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #e0e0e0;
    }

    .fc .fc-button-primary {
      background-color: #007bff;
      border-color: #007bff;
      color: #fff;
    }

    .fc .fc-button-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .fc-event {
      border-radius: 4px;
      padding: 2px 4px;
      cursor: pointer;
      font-size: 0.85em;
      line-height: 1.2;
    }

    .fc-event-main,
    .fc-event-title-container {
      color: white;
    }

    .quick-links-card,
    .sidebar-card,
    .catalog-card,
    .dropdown-menu {
      border-radius: 0 !important;
      border: 1px solid #d3d3d3 !important;
    }

    .container {
      border: none !important;
      box-shadow: none !important;
    }

    /* Ensure the spinner is circular */
    .spinner-border {
      border-radius: 50% !important;
    }

    .toast {
      border-radius: 0 !important;
      margin-bottom: 10px;
    }

    .catalog-card {
      display: flex;
      position: relative;
      padding: 15px;
      margin: 0;
      box-sizing: border-box;
      background-color: #fff;
      border: 1px solid #d3d3d3;
      gap: 15px;
      /* Add spacing between the image and content */
    }

    .catalog-card .catalog-card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 4px;
      background-color: #f8f9fa;
    }

    .catalog-card .row.g-0 {
      margin: 0;
      /* Remove row-level spacing */
    }

    .catalog-card .col-md-3 {
      padding: 0 5px;
      /* Reduce left padding to match the right padding of the container */
    }

    .catalog-card .col-md-7 {
      padding-left: 15px;
      /* Maintain padding between the image and content */
    }

    .catalog-card .col-md-2 {
      position: absolute;
      top: 0;
      right: 0;
      height: 100%;
      padding: 15px;
      border-left: 1px solid #d3d3d3;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background-color: #fff;
    }

    /* Adjusted grid card styling */
    .catalog-card.grid-layout {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 15px;
      background-color: #fff;
      border: 1px solid #d3d3d3;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      height: auto;
      /* Adjust height dynamically to fit content */
      min-height: 200px;
      /* Ensure minimum height */
      padding-bottom: 20px;
      /* Add padding to prevent overflow */
    }

    .catalog-card.grid-layout:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .catalog-card.grid-layout img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 4px;
      margin-bottom: 10px;
    }

    .catalog-card.grid-layout .card-body {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .catalog-card.grid-layout .card-title {
      font-size: 1.1rem;
      font-weight: bold;
      color: #333;
    }

    .catalog-card.grid-layout .badge {
      font-size: 0.8rem;
      padding: 5px 10px;
      border-radius: 12px;
    }

    .catalog-card.grid-layout .card-text {
      font-size: 0.9rem;
      color: #666;
      text-align: left;
    }

    .catalog-card.grid-layout .rental-fee {
      font-size: 1.2rem;
      font-weight: bold;
      text-align: left;
      color: var(--cpu-primary);
    }

    .catalog-card.grid-layout .btn {
      font-size: 0.85rem;
      padding: 0.5rem 0.75rem;
    }

    .catalog-card.grid-layout .btn-outline-secondary {
      color: #6c757d;
      border-color: #6c757d;
    }

    .catalog-card.grid-layout .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: #fff;
    }

    .catalog-card.grid-layout .btn-primary {
      background-color: var(--cpu-primary);
      border-color: var(--cpu-primary);
    }

    .catalog-card.grid-layout .btn-primary:hover {
      background-color: var(--cpu-primary-hover);
      border-color: var(--cpu-primary-hover);
    }

    .catalog-card.grid-layout .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .catalog-card.grid-layout .btn-danger:hover {
      background-color: #a71d2a;
      border-color: #a71d2a;
    }

    .catalog-card.grid-layout .d-flex.flex-wrap.gap-2 span {
      font-size: 0.9rem;
      /* Match text size with location note */
      color: #666;
      /* Ensure consistent color */
      gap: 5px;
      /* Reduced gap between tags */
    }
  </style>
</head>

<body>

  @extends('layouts.app')

  @section('title', 'Facility Catalog')


  @section('content')

    <section class="catalog-hero-section">
    <div class="catalog-hero-content">
      <h2 id="catalogHeroTitle">Facilities Catalog</h2>
    </div>
    </section>

    <main class="main-catalog-section">
    <div class="container">
      <div class="row">
      <div class="col-lg-3 col-md-4">
        <div class="quick-links-card mb-4">
        <p class="mb-2">
          Not sure when to book?<br />View available timeslots here.
        </p>
        <a id="requisitionFormButton" href="bookingpage.html"
          class="btn btn-primary d-flex justify-content-center align-items-center position-relative mb-2"
          style="border-radius: 0; font-size: 0.9rem; padding: 0.4rem 0.7rem; text-align: center;">
          <i class="bi bi-receipt me-2"></i> Your Requisition Form
          <span id="requisitionBadge" class="badge bg-danger rounded-pill position-absolute d-none"
          style="top: 0; right: 0; transform: translate(50%, -50%); font-size: 0.8rem;">
          0
          </span>
        </a>

        <button type="button" class="btn btn-outline-primary d-flex align-items-center"
          style="border-radius: 0; font-size: 1rem; padding: 0.4rem 0.7rem; width: 100%;" id="eventsCalendarBtn"
          data-bs-toggle="modal" data-bs-target="#userCalendarModal">Events Calendar</button>

        </div>

        <div class="sidebar-card">
        <h5>Browse by Category</h5>
        <div class="filter-list" id="categoryFilterList"></div>
        </div>
      </div>

      <div class="col-lg-9 col-md-8">
        <div class="right-content-header">
        <h4 id="currentCategoryTitle" class="d-flex align-items-center">
          <div class="dropdown">
          <button class="btn btn-link dropdown-toggle text-decoration-none" type="button"
            id="chooseCatalogDropdown" data-bs-toggle="dropdown" aria-expanded="false"
            style="font-size: 1.2rem; color: inherit;">
            All Facilities
          </button>
          <ul class="dropdown-menu" aria-labelledby="chooseCatalogDropdown">
            <li>
            <a class="dropdown-item" href="bookingcatalog.html" data-catalog-type="facilities">
              Facilities
            </a>
            </li>
            <li>
            <a class="dropdown-item" href="equipmentcatalog.html" data-catalog-type="equipment">
              Equipment
            </a>
            </li>
          </ul>
          </div>
        </h4>
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
            <li>
            <a class="dropdown-item status-option" href="#" data-status="Under Maintenance">Under
              Maintenance</a>
            </li>
            <li>
            <a class="dropdown-item status-option" href="#" data-status="Closed">Closed</a>
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
            <a class="dropdown-item layout-option" href="#" data-layout="list">List</a>
            </li>
            <li>
            <a class="dropdown-item layout-option" href="#" data-layout="grid">Grid</a>
            </li>
          </ul>
          </div>
          <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Filter By
          </button>
          <ul class="dropdown-menu">
            <li>
            <a class="dropdown-item" href="#">Price (Low to High)</a>
            </li>
            <li>
            <a class="dropdown-item" href="#">Price (High to Low)</a>
            </li>
            <li>
            <a class="dropdown-item" href="#">Alphabetical (A-Z)</a>
            </li>
          </ul>
          </div>
        </div>
        </div>

        <div class="modal fade" id="userCalendarModal" tabindex="-1" aria-labelledby="userCalendarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
          <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="userCalendarModalLabel">View Booking Events Calendar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="userFullCalendar"></div>
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
        <p class="mt-2">Loading facilities...</p>
        </div>


        <!-- Catalog Items Container -->
        <div id="catalogItemsContainer" class="list-layout d-none"></div>

        <div class="text-center mt-4">
        <nav>
          <ul id="pagination-top" class="pagination justify-content-center"></ul>
          <ul id="pagination" class="pagination justify-content-center"></ul>
        </nav>
        </div>
      </div>
      </div>
    </div>
    </main>
  @endsection

  <script>

    // Global state management
    const AppState = {
      initialized: false,
      facilities: [],
      calendar: null,
      departments: [], 
      requisitionItems: (() => {
        try {
          const items = JSON.parse(localStorage.getItem("selectedItems"));
          return Array.isArray(items) ? items : [];
        } catch (e) {
          return [];
        }
      })(),
      requisitionPurposes: [] // Added for storing purposes
    };


    // Toast notification system (unchanged)
    const Toast = {
      container: null,
      init() {
        this.container = document.getElementById('toastContainer') || this.createContainer();
      },
      createContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container.style.bottom = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
      },
      show(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type}`;
        toast.role = 'alert';
        toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
        this.container.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
      }
    };

    // API Service (unchanged)
    const ApiService = {
      baseUrl: 'http://127.0.0.1:8000/api',
      async request(url, method = 'GET', body = null) {
        const headers = {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        };
        const config = {
          method,
          headers,
          credentials: 'same-origin'
        };
        if (body) {
          config.body = JSON.stringify(body);
        }
        const response = await fetch(`${this.baseUrl}${url}`, config);
        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
        }
        return response.json();
      },
      get(url) {
        return this.request(url);
      },
      post(url, data) {
        return this.request(url, 'POST', data);
      }
    };

    // Enhanced Requisition Module
    const RequisitionModule = {
      init() {
        this.bindEvents();
        this.updateRequisitionButton();
        this.fetchPurposes().then(() => {
          // Auto-load items if we're on the booking page
          if (window.location.pathname.includes('bookingpage.html')) {
            this.initializeBookingPage();
          }
        });
      },

      bindEvents() {
        document.addEventListener('click', (e) => {
          const btn = e.target.closest('.add-to-form-btn');
          if (btn) {
            e.preventDefault();
            this.handleItemAction(btn);
          }
        });

        window.addEventListener('storage', () => this.updateRequisitionButton());
      },

      async fetchPurposes() {
        try {
          AppState.requisitionPurposes = await ApiService.get('/requisition-purposes');
          this.populatePurposeDropdown();
        } catch (error) {
          console.error('Failed to load requisition purposes:', error);
          Toast.show('Failed to load requisition purposes', 'error');
        }
      },

      populatePurposeDropdown() {
        const purposeSelect = document.getElementById('requisitionPurpose');
        if (!purposeSelect) return; // Only on booking page

        // Clear existing options
        purposeSelect.innerHTML = '<option value="" selected disabled>Select a purpose</option>';

        // Add new options
        AppState.requisitionPurposes.forEach(purpose => {
          const option = document.createElement('option');
          option.value = purpose.id;
          option.textContent = purpose.name;
          purposeSelect.appendChild(option);
        });
      },

      initializeBookingPage() {
        this.populatePurposeDropdown();
        this.displaySelectedItems();

        // Load any previously selected purpose
        const savedPurpose = localStorage.getItem('selectedPurpose');
        if (savedPurpose && document.getElementById('requisitionPurpose')) {
          document.getElementById('requisitionPurpose').value = savedPurpose;
        }
      },

      displaySelectedItems() {
        const itemsContainer = document.getElementById('selectedItemsContainer');
        if (!itemsContainer) return;

        itemsContainer.innerHTML = '';

        if (AppState.requisitionItems.length === 0) {
          itemsContainer.innerHTML = '<p class="text-muted">No items selected</p>';
          return;
        }

        const list = document.createElement('ul');
        list.className = 'list-group';

        AppState.requisitionItems.forEach(item => {
          const listItem = document.createElement('li');
          listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
          listItem.innerHTML = `
        ${item.name}
        <button class="btn btn-sm btn-danger remove-item-btn" data-item-id="${item.id}">
          <i class="bi bi-trash"></i>
        </button>
      `;
          list.appendChild(listItem);
        });

        itemsContainer.appendChild(list);

        // Add event listeners for remove buttons
        document.querySelectorAll('.remove-item-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const itemId = btn.dataset.itemId;
            this.removeItem(itemId);
          });
        });
      },

      async handleItemAction(button) {
        const facilityId = button.dataset.facilityId;
        const facility = AppState.facilities.find(f => f.id === parseInt(facilityId));

        if (!facility) {
          Toast.show('Facility not found', 'error');
          return;
        }

        const isAddAction = !button.classList.contains('btn-danger');
        const originalText = button.innerHTML;

        button.innerHTML = `<span class="spinner-border spinner-border-sm"></span> ${isAddAction ? 'Adding...' : 'Removing...'}`;
        button.disabled = true;

        try {
          if (isAddAction) {
            await this.addItem(facilityId, facility);
          } else {
            await this.removeItem(facilityId);
          }
        } finally {
          button.disabled = false;
        }
      },

      async addItem(facilityId, facility) {
        await ApiService.post('/requisition/add-item', { facility_id: facilityId });

        if (!AppState.requisitionItems.some(item => item.id === parseInt(facilityId))) {
          AppState.requisitionItems.push({
            id: facility.id,
            name: facility.facility_name,
            type: 'facility'
          });
          this.saveItems();
        }

        Toast.show(`${facility.facility_name} added to form!`, 'success');
        this.updateRequisitionButton();
      },

      async removeItem(facilityId) {
        await ApiService.post('/requisition/remove-item', { facility_id: facilityId });

        AppState.requisitionItems = AppState.requisitionItems.filter(item => item.id !== parseInt(facilityId));
        this.saveItems();


        Toast.show('Item removed from form!', 'success');
        this.updateRequisitionButton();
      },

      saveItems() {
        localStorage.setItem("selectedItems", JSON.stringify(AppState.requisitionItems));
      },

      updateRequisitionButton() {
        const requisitionFormButton = document.getElementById("requisitionFormButton");
        if (!requisitionFormButton) return;

        const requisitionBadge = document.getElementById("requisitionBadge");
        const count = AppState.requisitionItems.length;

        if (count > 0) {
          requisitionBadge.textContent = count;
          requisitionBadge.classList.remove("d-none");
          requisitionFormButton.classList.replace("btn-outline-primary", "btn-primary");

          const tooltipContent = AppState.requisitionItems.map(item => `• ${item.name}`).join('<br>');
          $(requisitionFormButton)
            .attr('data-bs-toggle', 'tooltip')
            .attr('data-bs-html', 'true')
            .attr('title', `<strong>Selected Items (${count}):</strong><br>${tooltipContent}`);

          new bootstrap.Tooltip(requisitionFormButton);
        } else {
          requisitionBadge.classList.add("d-none");
          requisitionFormButton.classList.replace("btn-primary", "btn-outline-primary");
          $(requisitionFormButton).tooltip('dispose');
        }
      }
    };

    // Catalog Module
    const CatalogModule = {
      currentCategory: "All",
      currentLayout: "grid", // Changed default layout to "grid"
      currentPage: 1,
      itemsPerPage: 4,
      currentStatus: "Available", // Default status set to "Available"

      async init() {
        await this.loadDepartments(); // Fetch departments before loading facilities
        this.loadCategories();
        this.loadFacilities();
        this.bindEvents();
      },

      async loadDepartments() {
        try {
          AppState.departments = await ApiService.get('/departments');
        } catch (error) {
          console.error('Failed to load departments:', error);
        }
      },

      async loadCategories() {
        const categoryFilterList = document.getElementById("categoryFilterList");
        categoryFilterList.innerHTML = '<p>Loading categories...</p>';

        try {
          const response = await fetch('http://127.0.0.1:8000/api/facility-categories/index');
          const categories = await response.json();

          categoryFilterList.innerHTML = ''; // Clear loading message

          categories.forEach(category => {
            const categoryItem = document.createElement("div");
            categoryItem.classList.add("category-item");

            const categoryHeader = document.createElement("a");
            categoryHeader.href = "#";
            categoryHeader.classList.add("filter-item", "d-flex", "align-items-center", "justify-content-between");
            categoryHeader.textContent = `All ${category.category_name}`;
            categoryHeader.dataset.category = category.category_name;

            const toggleIcon = document.createElement("i");
            toggleIcon.classList.add("bi", "bi-chevron-down", "ms-2", "toggle-icon");
            toggleIcon.dataset.bsToggle = "collapse";
            toggleIcon.dataset.bsTarget = `#subcategory-${category.category_id}`;

            categoryHeader.addEventListener("click", (e) => {
              if (e.target === categoryHeader || e.target === toggleIcon) {
                e.preventDefault();
                this.updateCategoryFilter(category.category_name);
              }
            });

            categoryHeader.appendChild(toggleIcon);
            categoryItem.appendChild(categoryHeader);

            if (category.subcategories?.length > 0) {
              const subcategoryList = document.createElement("div");
              subcategoryList.classList.add("subcategory-list", "collapse");
              subcategoryList.id = `subcategory-${category.category_id}`;

              category.subcategories.forEach(subcategory => {
                const subcategoryItem = document.createElement("a");
                subcategoryItem.href = "#";
                subcategoryItem.classList.add("filter-item", "ms-3");
                subcategoryItem.dataset.category = subcategory.subcategory_name;
                subcategoryItem.textContent = subcategory.subcategory_name;

                subcategoryItem.addEventListener("click", (e) => {
                  e.preventDefault();
                  this.updateCategoryFilter(subcategory.subcategory_name);
                });

                subcategoryList.appendChild(subcategoryItem);
              });

              categoryItem.appendChild(subcategoryList);
            }

            categoryFilterList.appendChild(categoryItem);
          });

          const allCategoriesLink = document.createElement("a");
          allCategoriesLink.href = "#";
          allCategoriesLink.classList.add("filter-item", "active");
          allCategoriesLink.dataset.category = "All";
          allCategoriesLink.textContent = "All Categories";
          allCategoriesLink.addEventListener("click", (e) => {
            e.preventDefault();
            this.updateCategoryFilter("All");
          });

          categoryFilterList.prepend(allCategoriesLink);
        } catch (error) {
          categoryFilterList.innerHTML = '<p class="text-danger">Failed to load categories.</p>';
          console.error("Error loading categories:", error);
        }
      },

      async loadFacilities() {
        const loadingIndicator = document.getElementById("loadingIndicator");
        const catalogContainer = document.getElementById("catalogItemsContainer");

        try {
          loadingIndicator.classList.add("d-none");
          catalogContainer.classList.remove("d-none");

          const data = await ApiService.get('/facilities');
          AppState.facilities = data.data.map(f => ({
            ...f,
            rental_fee: parseFloat(f.rental_fee) || 0
          }));

          this.renderCatalogItems();
        } catch (error) {
          loadingIndicator.innerHTML = '<div class="alert alert-danger">Failed to load facilities.</div>';
        } finally {
          loadingIndicator.classList.add("d-none");
          catalogContainer.classList.remove("d-none");
        }
      },

      bindEvents() {
        // Layout toggle
        document.querySelectorAll(".layout-option").forEach(option => {
          option.addEventListener("click", (e) => {
            e.preventDefault();
            this.currentLayout = option.dataset.layout;
            document.getElementById("layoutDropdown").textContent = `${option.textContent} Layout`;
            this.currentPage = 1;
            this.renderCatalogItems();
          });
        });

        // Sorting
        document.querySelectorAll(".sort-option").forEach(option => {
          option.addEventListener("click", (e) => {
            e.preventDefault();
            this.sortFacilities(option.dataset.sort);
            this.currentPage = 1;
            this.renderCatalogItems();
          });
        });

        // Status filter
        document.querySelectorAll(".status-option").forEach(option => {
          option.addEventListener("click", (e) => {
            e.preventDefault();
            this.currentStatus = option.dataset.status;
            document.getElementById("statusDropdown").textContent = `Status: ${this.currentStatus}`;
            this.currentPage = 1;
            this.renderCatalogItems();
          });
        });
      },

      sortFacilities(sortType) {
        switch (sortType) {
          case 'price-asc':
            AppState.facilities.sort((a, b) => (a.rental_fee || 0) - (b.rental_fee || 0));
            break;
          case 'price-desc':
            AppState.facilities.sort((a, b) => (b.rental_fee || 0) - (a.rental_fee || 0));
            break;
          case 'name-asc':
            AppState.facilities.sort((a, b) => (a.facility_name || '').localeCompare(b.facility_name || ''));
            break;
        }
      },

      updateCategoryFilter(category) {
        document.querySelectorAll("#categoryFilterList .filter-item").forEach(item => {
          item.classList.remove("active");
        });

        const activeItem = document.querySelector(`#categoryFilterList .filter-item[data-category="${category}"]`);
        if (activeItem) {
          activeItem.classList.add("active");
        }

        this.currentCategory = category;
        this.currentPage = 1;
        document.getElementById("currentCategoryTitle").querySelector(".dropdown-toggle").textContent =
          `${this.currentCategory === "All" ? "All" : this.currentCategory} Facilities`;

        this.renderCatalogItems();
      },

      renderCatalogItems() {
        const container = document.getElementById("catalogItemsContainer");
        container.innerHTML = "";
        container.className = `${this.currentLayout}-layout`;

        const filtered = this.getFilteredFacilities();
        const paginated = this.paginateFacilities(filtered);

        if (paginated.length === 0) {
          container.innerHTML = '<p class="text-center text-muted mt-5">No facilities found.</p>';
          return;
        }

        const wrapper = document.createElement("div");
        wrapper.className = this.currentLayout === "grid" ? "row g-3" : "";

        paginated.forEach(facility => {
          const element = this.createFacilityElement(facility);
          wrapper.appendChild(element);
        });

        container.appendChild(wrapper);
        this.renderPagination(filtered.length);
      },

      getFilteredFacilities() {
        let facilities = AppState.facilities;

        if (this.currentCategory !== "All") {
          facilities = facilities.filter(f =>
            f.category?.category_name?.toLowerCase() === this.currentCategory.toLowerCase() ||
            f.subcategory?.subcategory_name?.toLowerCase() === this.currentCategory.toLowerCase()
          );
        }

        if (this.currentStatus !== "All") {
          facilities = facilities.filter(f => f.status?.status_name === this.currentStatus);
        }

        return facilities;
      },

      paginateFacilities(facilities) {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        return facilities.slice(start, start + this.itemsPerPage);
      },

      createFacilityElement(facility) {
        const isSelected = AppState.requisitionItems.some(item => item.id === facility.facility_id);
        const rentalFee = parseFloat(facility.internal_fee).toFixed(2);
        const statusColor = facility.status?.color_code || "#6c757d";
        const statusName = facility.status?.status_name || "Unknown";

        // Find department code
        const department = AppState.departments.find(d => d.department_name === facility.department?.department_name);
        const departmentCode = department?.department_code || "N/A";

        if (this.currentLayout === "list") {
          const card = document.createElement("div");
          card.classList.add("catalog-card");
          card.innerHTML = `
          <div class="row g-0">
              <div class="col-md-3">
                  <img src="{{ asset('assets/facilities-pic.jpg') }}" alt="Facilities">
              </div>
              <div class="col-md-7">
                  <div class="card-body">
                      <h5 class="card-title d-flex align-items-center">
                          ${facility.facility_name}
                          <span class="badge ms-2" style="background-color: ${statusColor};">${statusName}</span>
                      </h5>
                      <div class="d-flex flex-wrap gap-2 mb-2">
                          <span class="text-muted">${facility.category?.category_name || "Uncategorized"}</span> |
                          <span class="text-muted">${facility.subcategory?.subcategory_name || "No Subcategory"}</span> |
                          <span class="text-muted">${departmentCode}</span> |
                          <span class="text-muted">Capacity: ${facility.capacity || "N/A"}</span>
                      </div>
                      <p class="card-text text-muted mb-1">${facility.description || "No description available."}</p>
                      <p class="card-text text-muted"><i class="bi bi-geo-alt"></i> ${facility.location_note || "No location note available."}</p>
                  </div>
              </div>
              <div class="col-md-2 d-flex flex-column justify-content-between p-3 border-start">
                  <div>
                      <div class="rental-fee fw-bold">₱${rentalFee}</div>
                      <div class="text-muted">${facility.rate_type || "N/A"}</div>
                  </div>
                  <div class="d-grid gap-2 mt-auto">
                      <button class="btn btn-sm ${isSelected ? 'btn-danger' : 'btn-primary'} add-to-form-btn" 
                          data-facility-id="${facility.facility_id}" 
                          data-type="facility">
                          ${isSelected ? 'Remove from Form' : 'Add to Form'}
                      </button>
                      <button class="btn btn-sm btn-outline-secondary view-calendar-btn" 
                          data-bs-toggle="modal" data-bs-target="#userCalendarModal" 
                          data-facility-id="${facility.facility_id}">
                          View Calendar
                      </button>
                  </div>
              </div>
          </div>
          `;
          return card;
        } else {
          const card = document.createElement("div");
          card.className = "catalog-card grid-layout";
          card.innerHTML = `
            <img src="{{ asset('assets/public/facilities-pic.jpg') }}" alt="Facilities">
            <div class="card-body">
              <h5 class="card-title d-flex align-items-center">
                ${facility.facility_name}
                <span class="badge ms-2" style="background-color: ${statusColor};">${statusName}</span>
              </h5>
              <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="text-muted">${facility.category?.category_name || "Uncategorized"}</span> |
                <span class="text-muted">${facility.subcategory?.subcategory_name || "No Subcategory"}</span> |
                <span class="text-muted">${departmentCode}</span> |
                <span class="text-muted">Capacity: ${facility.capacity || "N/A"}</span>
              </div>
              <p class="card-text text-muted">${facility.description || "No description available."}</p>
              <div class="rental-fee">₱${rentalFee}</div>
              <div class="d-grid gap-2 mt-auto">
                <button class="btn btn-sm ${isSelected ? 'btn-danger' : 'btn-primary'} add-to-form-btn" 
                  data-facility-id="${facility.facility_id}" 
                  data-type="facility">
                  ${isSelected ? 'Remove from Form' : 'Add to Form'}
                </button>
                <button class="btn btn-sm btn-outline-secondary view-calendar-btn" 
                  data-bs-toggle="modal" data-bs-target="#userCalendarModal" 
                  data-facility-id="${facility.facility_id}">
                  View Calendar
                </button>
              </div>
            </div>
          `;
          return card;
        }
      },

      renderPagination(totalItems) {
        const container = document.getElementById("pagination");
        container.innerHTML = "";

        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
        if (totalPages <= 1) return;

        const createPageItem = (content, disabled, onClick) => {
          const li = document.createElement("li");
          li.className = `page-item ${disabled ? "disabled" : ""}`;
          li.innerHTML = `<a class="page-link" href="#">${content}</a>`;
          li.addEventListener("click", (e) => {
            e.preventDefault();
            if (!disabled) onClick();
          });
          return li;
        };

        // Previous
        container.appendChild(createPageItem(
          '&laquo;',
          this.currentPage === 1,
          () => {
            this.currentPage--;
            this.renderCatalogItems();
            window.scrollTo(0, 0);
          }
        ));

        // Pages
        for (let i = 1; i <= totalPages; i++) {
          container.appendChild(createPageItem(
            i,
            i === this.currentPage,
            () => {
              this.currentPage = i;
              this.renderCatalogItems();
              window.scrollTo(0, 0);
            }
          ));
        }

        // Next
        container.appendChild(createPageItem(
          '&raquo;',
          this.currentPage === totalPages,
          () => {
            this.currentPage++;
            this.renderCatalogItems();
            window.scrollTo(0, 0);
          }
        ));
      }
    };

    // Calendar Module
    const CalendarModule = {
      init() {
        const modal = document.getElementById('userCalendarModal');
        modal.addEventListener('shown.bs.modal', () => this.initializeCalendar());
        modal.addEventListener('hidden.bs.modal', () => this.destroyCalendar());

        document.addEventListener('click', (e) => {
          if (e.target.closest('.view-calendar-btn')) {
            // Could load facility-specific calendar data here
          }
        });
      },

      initializeCalendar() {
        if (AppState.calendar) return;

        const calendarEl = document.getElementById('userFullCalendar');
        AppState.calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
          },
          height: 'auto',
          nowIndicator: true,
          events: this.getCalendarEvents(),
          eventClick: (info) => {
            alert('Event: ' + info.event.title);
          }
        });

        AppState.calendar.render();
      },

      destroyCalendar() {
        if (AppState.calendar) {
          AppState.calendar.destroy();
          AppState.calendar = null;
        }
      },

      getCalendarEvents() {
        return [
          {
            title: 'Booked: AVR Room',
            start: '2025-06-02T10:00:00',
            end: '2025-06-02T12:00:00',
            backgroundColor: '#198754'
          },
          {
            title: 'PE Gym Reservation',
            start: '2025-06-05',
            backgroundColor: '#0d6efd'
          },
          {
            title: 'Library Conference',
            start: '2025-06-10T14:00:00',
            end: '2025-06-10T16:00:00',
            backgroundColor: '#dc3545'
          }
        ];
      }
    };

    // Main Application Initialization
    document.addEventListener("DOMContentLoaded", () => {
      if (AppState.initialized) return;
      AppState.initialized = true;

      Toast.init();
      RequisitionModule.init();

      // Only initialize these if we're on the catalog page
      if (window.location.pathname.includes('bookingcatalog.html') ||
        window.location.pathname.includes('facilities.html')) {
        CatalogModule.init();
        CalendarModule.init();
      }
    });

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.add-to-form-btn');
      if (btn) {
        e.preventDefault();
        const facilityId = btn.getAttribute('data-facility-id');
        const type = btn.getAttribute('data-type');
        const isAddAction = !btn.classList.contains('btn-danger');

        try {
          if (isAddAction) {
            await fetch('http://127.0.0.1:8000/api/requisition/add-item', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({ facility_id: facilityId, type }),
            });
            btn.classList.replace('btn-primary', 'btn-danger');
            btn.textContent = 'Remove from Form';
          } else {
            // Handle removal logic if needed
          }
        } catch (error) {
          console.error('Error:', error);
        }
      }
    });

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.add-to-form-btn');
      if (btn) {
        e.preventDefault();
        const facilityId = btn.getAttribute('data-facility-id');
        const isAddAction = !btn.classList.contains('btn-danger');

        try {
          if (isAddAction) {
            // Add facility ID to localStorage
            const selectedFacilities = JSON.parse(localStorage.getItem('selectedFacilities')) || [];
            if (!selectedFacilities.includes(facilityId)) {
              selectedFacilities.push(facilityId);
              localStorage.setItem('selectedFacilities', JSON.stringify(selectedFacilities));
            }

            // Update button state
            btn.classList.replace('btn-primary', 'btn-danger');
            btn.textContent = 'Remove from Form';

            // Trigger storage event for other pages
            window.dispatchEvent(new Event('storage'));
          } else {
            // Remove facility ID from localStorage
            const selectedFacilities = JSON.parse(localStorage.getItem('selectedFacilities')) || [];
            const updatedFacilities = selectedFacilities.filter(id => id !== facilityId);
            localStorage.setItem('selectedFacilities', JSON.stringify(updatedFacilities));

            // Update button state
            btn.classList.replace('btn-danger', 'btn-primary');
            btn.textContent = 'Add to Form';

            // Trigger storage event for other pages
            window.dispatchEvent(new Event('storage'));
          }
        } catch (error) {
          console.error('Error handling facility action:', error);
        }
      }
    });

    // Ensure data persists when switching pages
    document.addEventListener('DOMContentLoaded', () => {
      const selectedFacilities = JSON.parse(localStorage.getItem('selectedFacilities')) || [];
      document.querySelectorAll('.add-to-form-btn').forEach(btn => {
        const facilityId = btn.getAttribute('data-facility-id');
        if (selectedFacilities.includes(facilityId)) {
          btn.classList.replace('btn-primary', 'btn-danger');
          btn.textContent = 'Remove from Form';
        }
      });
    });
  </script>

  <style>
    /* Your existing CSS remains the same */
    .quick-links-card,
    .sidebar-card,
    .catalog-card,
    .dropdown-menu {
      border-radius: 0 !important;
      border: 1px solid #d3d3d3 !important;
    }

    /* Remove border and shadows from the main container */
    .container {
      border: none !important;
      box-shadow: none !important;
    }

    /* Style pagination to use CPU primary colors */
    .pagination .page-item.active .page-link {
      background-color: var(--cpu-primary) !important;
      border-color: var(--cpu-primary) !important;
      color: #fff !important;
    }

    .pagination .page-link {
      color: var(--cpu-primary) !important;
      border-color: lightgrey !important;
    }

    .pagination .page-item.disabled .page-link {
      color: #6c757d !important;
      border-color: #d3d3d3 !important;
    }
  </style>
</body>

</html>
@extends('layouts.app')

@section('title', 'Booking Catalog - Equipment')

@section('content')
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public/catalog.css') }}" />
  <style>
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
    <div class="row">
      <div class="col-lg-3 col-md-4">
      <div class="quick-links-card mb-4">
        <p class="mb-2">
        Not sure when to book?<br />View available timeslots here.
        </p>
        <a id="requisitionFormButton" href="reservation-form"
        class="btn btn-primary d-flex justify-content-center align-items-center position-relative mb-2">
        <i class="bi bi-receipt me-2"></i> Your Requisition Form
        <span id="requisitionBadge" class="badge bg-danger rounded-pill position-absolute d-none">0</span>
        </a>

        <button type="button" class="btn btn-outline-primary d-flex align-items-center" id="eventsCalendarBtn"
        data-bs-toggle="modal" data-bs-target="#userCalendarModal">Events Calendar</button>
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

        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          Filter By
          </button>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#">Price (Low to High)</a></li>
          <li><a class="dropdown-item" href="#">Price (High to Low)</a></li>
          <li><a class="dropdown-item" href="#">Alphabetical (A-Z)</a></li>
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
@endsection

@section('scripts')
  <script>
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

    function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${type === "success" ? "success" : "danger"} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.style.zIndex = "1100";
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
      <div class="d-flex">
      <div class="toast-body">
      <i class="bi ${type === "success" ? "bi-check-circle-fill" : "bi-exclamation-circle-fill"} me-2"></i>
      ${message}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener("hidden.bs.toast", () => {
      toast.remove();
    });
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

      catalogItemsContainer.classList.remove("grid-layout", "list-layout");
      catalogItemsContainer.classList.add(`${currentLayout}-layout`);

      if (paginatedItems.length === 0) {
        catalogItemsContainer.innerHTML = `
          <div class="col-12 text-center py-5">
            <i class="bi bi-box-seam fs-1 text-muted"></i>
            <h4>No equipment found</h4>
          </div>
        `;
        return;
      }

      if (currentLayout === "grid") {
        renderEquipmentGrid(paginatedItems);
      } else {
        renderEquipmentList(paginatedItems);
      }
    }

    // Grid layout for equipment (same as before)
    function renderEquipmentGrid(equipmentList) {
      catalogItemsContainer.innerHTML = equipmentList.map(item => `
        <div class="catalog-card">
          <img src="${item.images?.find(i => i.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/300x200'}" 
           alt="${item.equipment_name}" class="catalog-card-img">
          <div class="catalog-card-details">
            <h5>${item.equipment_name}</h5>
            <span class="status-banner" style="background-color: ${item.status.color_code}">
              ${item.status.status_name}
            </span>
            <div class="catalog-card-meta">
              <span><i class="bi bi-tags-fill"></i> ${item.category.category_name}</span>
              <span><i class="bi bi-box-seam"></i> ${item.available_quantity}/${item.total_quantity} available</span>
            </div>
            <p class="facility-description">${item.description?.substring(0, 100) || 'No description available'}${item.description?.length > 100 ? '...' : ''}</p>
            <div class="catalog-card-fee">
              <i class="bi bi-cash-stack"></i> ₱${parseFloat(item.external_fee).toLocaleString()} (${item.rate_type})
            </div>
          </div>
          <div class="catalog-card-actions">
            ${getEquipmentButtonHtml(item)}
          </div>
        </div>
      `).join('');
    }

    // List layout for equipment (similar to facility list layout)
    function renderEquipmentList(equipmentList) {
      catalogItemsContainer.innerHTML = equipmentList.map(item => {
        const primaryImage = item.images?.find(i => i.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/300x200';
        return `
          <div class="catalog-card">
            <img src="${primaryImage}" alt="${item.equipment_name}" class="catalog-card-img">
            <div class="catalog-card-details">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>${item.equipment_name}</h5>
                <span class="status-banner" style="background-color: ${item.status.color_code}">
                  ${item.status.status_name}
                </span>
              </div>
              <div class="catalog-card-meta">
                <span><i class="bi bi-tags-fill"></i> ${item.category.category_name}</span>
                <span><i class="bi bi-box-seam"></i> ${item.available_quantity}/${item.total_quantity} available</span>
              </div>
              <p class="facility-description">${item.description || 'No description available'}</p>
              <div class="catalog-card-fee">
                <i class="bi bi-cash-stack"></i> ₱${parseFloat(item.external_fee).toLocaleString()} (${item.rate_type})
              </div>
            </div>
            <div class="catalog-card-actions">
              ${getEquipmentButtonHtml(item)}
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
    if (selectedItems.length > 0) {
      requisitionBadge.textContent = selectedItems.length;
      requisitionBadge.classList.remove("d-none");
    } else {
      requisitionBadge.classList.add("d-none");
    }
    }

    // Main function to refresh UI
    async function updateAllUI() {
    try {
      selectedItems = await getSelectedItems();
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

    // Generate button HTML based on selection state
    function getEquipmentButtonHtml(equipment) {
    const isSelected = selectedItems.some(
      item => parseInt(item.id) === equipment.equipment_id && item.type === "equipment"
    );

    const selectedItem = isSelected ? selectedItems.find(
      item => parseInt(item.id) === equipment.equipment_id
    ) : null;

    const currentQty = selectedItem ? selectedItem.quantity : 1;
    const maxQty = equipment.total_quantity || 1;
    const isUnavailable = equipment.status.status_id === 2; // Status ID 2 = Unavailable

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
        <button class="btn btn-secondary add-remove-btn" 
          data-id="${equipment.equipment_id}" 
          data-type="equipment" 
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
        <button class="btn btn-danger add-remove-btn" 
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
        <button class="btn btn-primary add-remove-btn" 
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
      } catch (error) {
      console.error("Error handling form action:", error);
      }
    });

    // Handle quantity changes
    catalogItemsContainer.addEventListener('change', async (e) => {
      if (e.target.classList.contains('quantity-input')) {
      const card = e.target.closest('.catalog-card');
      const button = card.querySelector('.add-remove-btn');
      const id = button.dataset.id;
      const type = button.dataset.type;
      const action = button.dataset.action;
      const quantity = parseInt(e.target.value) || 1;

      if (action === 'remove') {
        // If already in cart, update quantity
        await removeFromForm(id, type);
        await addToForm(id, type, quantity);
      }
      }
    });
    }

    // Main Initialization
    async function init() {
    try {
      // Fetch initial data
      const [equipmentData, categoriesData, selectedItemsData] = await Promise.all([
      fetchData('/api/equipment'),
      fetchData('/api/equipment-categories'),
      fetchData('/api/requisition/get-items')
      ]);

      // Only keep equipment with status_id 1 or 2
      allEquipment = (equipmentData.data || []).filter(e => allowedStatusIds.includes(e.status.status_id));
      equipmentCategories = categoriesData || [];
      selectedItems = selectedItemsData.data || [];

      renderCategoryFilters();
      filterAndRenderItems();
      setupEventListeners();
      updateCartBadge();

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
      // ...existing code...
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
  </script>
@endsection
@extends('layouts.app')

@section('title', 'Booking Catalog - Facilities')

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
    <h2 id="catalogHeroTitle">Facility Catalog</h2>
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
        <div style="position:relative;">
        <span id="requisitionBadge" class="badge bg-danger rounded-pill position-absolute"
          style="top:-0.7rem; right:-0.7rem; font-size:0.8em; z-index:2; display:none;">
          0
        </span>
        <a id="requisitionFormButton" href="reservation-form"
          class="btn btn-primary d-flex justify-content-center align-items-center position-relative mb-2">
          <i class="bi bi-receipt me-2"></i> Your Requisition Form
        </a>
        </div>
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
    const bgColor = type === "success" ? "#003366" : "#dc3545";
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

    if (paginatedItems.length === 0) {
      catalogItemsContainer.innerHTML = `
      <div class="col-12 text-center py-5">
      <i class="bi bi-building fs-1 text-muted"></i>
      <h4>No facilities found</h4>
      </div>
      `;
      return;
    }

    catalogItemsContainer.classList.remove("grid-layout", "list-layout");
    catalogItemsContainer.classList.add(`${currentLayout}-layout`);

    currentLayout === "grid"
      ? renderFacilitiesGrid(paginatedItems)
      : renderFacilitiesList(paginatedItems);

    // Add event listeners to item name links
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
        ?.image_url || "https://via.placeholder.com/300x200";

      return `
      <div class="catalog-card">
      <img src="${primaryImage}" alt="${facility.facility_name}" class="catalog-card-img">
      <div class="catalog-card-details">
      <h5 data-id="${facility.facility_id}">${facility.facility_name}</h5>
      <span class="status-banner" style="background-color: ${facility.status.color_code}">
      ${facility.status.status_name}
      </span>
      <div class="catalog-card-meta">
      <span><i class="bi bi-people-fill"></i> ${facility.capacity || "N/A"}</span>
      <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || facility.category.category_name}</span>
      </div>
      <p class="facility-description">${facility.description?.substring(0, 100) || "No description available."}${facility.description?.length > 100 ? "..." : ""}</p>
      <div class="catalog-card-fee">
      <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
      </div>
      </div>
      <div class="catalog-card-actions">
      ${getFacilityButtonHtml(facility)}
      <button class="btn btn-outline-secondary">View Calendar</button>
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
        ?.image_url || "https://via.placeholder.com/300x200";

      return `
      <div class="catalog-card">
      <img src="${primaryImage}" alt="${facility.facility_name}" class="catalog-card-img">
      <div class="catalog-card-details">
      <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 data-id="${facility.facility_id}">${facility.facility_name}</h5>
      <span class="status-banner" style="background-color: ${facility.status.color_code}">
      ${facility.status.status_name}
      </span>
      </div>
      <div class="catalog-card-meta">
      <span><i class="bi bi-people-fill"></i> ${facility.capacity || "N/A"}</span>
      <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || facility.category.category_name}</span>
      </div>
      <p class="facility-description">${facility.description || "No description available."}</p>
      <div class="catalog-card-fee">
      <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
      </div>
      </div>
      <div class="catalog-card-actions">
      ${getFacilityButtonHtml(facility)}
      <button class="btn btn-outline-secondary">View Calendar</button>
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

      const primaryImage = facility.images?.find((img) => img.image_type === "Primary")?.image_url || "https://via.placeholder.com/800x400";
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
  </script>
@endsection
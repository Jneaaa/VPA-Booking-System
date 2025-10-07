@extends('layouts.admin')

@section('title', 'Manage Equipment')

@section('content')

<style>

  /* Toast notification styles */
.toast {
    z-index: 1100;
    bottom: 0;
    left: 0;
    margin: 1rem;
    opacity: 0;
    transform: translateY(20px);
    transition: transform 0.4s ease, opacity 0.4s ease;
    min-width: 250px;
    border-radius: 0.3rem;
}

.toast .loading-bar {
    height: 3px;
    background: rgba(255, 255, 255, 0.7);
    width: 100%;
    transition: width 3000ms linear;
}

/* Custom pagination colors using CPU theme */
.pagination .page-link {
  color: var(--cpu-primary); /* dark blue text */
}

.pagination .page-link:hover {
  color: var(--cpu-primary-hover); /* hover text color */
}

/* Active page */
.pagination .page-item.active .page-link {
  background-color: var(--cpu-primary);
  border-color: var(--cpu-primary);
  color: #fff; /* white text for contrast */
}

/* Disabled state */
.pagination .page-item.disabled .page-link {
  color: #6c757d; /* gray */
  pointer-events: none;
  background-color: var(--light-gray);
  border-color: #dee2e6;
}


  html,
  body {
    height: 100%;
    margin: 0;
    overflow: hidden;
    /* prevent the whole page from scrolling */
  }

  #equipmentContainer {
    flex: 1;
    /* take up remaining space between header and pagination */
    overflow-y: auto;
    /* allow inner scrolling */
    min-height: 0;
    /* IMPORTANT for flexbox scrolling */
    padding-right: 8px;
    /* Add right padding */
  }

  /* Custom thin scrollbar */
  #equipmentContainer::-webkit-scrollbar {
    width: 6px;
  }

  #equipmentContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  #equipmentContainer::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
  }

  #equipmentContainer::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  /* Firefox */
  #equipmentContainer {
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
  }
</style>


<div id="layout">
  <!-- Main Content -->
  <main id="main">
    <div class="container-fluid bg-light rounded p-4 d-flex flex-column h-100">
      <div class="container-fluid d-flex flex-column h-100">

        <!-- Header & Controls -->
        <div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title m-0 fw-bold">Manage Equipment</h2>
            <div>
              <a href="{{ url('/admin/add-equipment') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Add New
              </a>

               <a href="{{ url('/admin/scan-equipment') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Equipment Scanner
            </a>
            </div>
          </div>

          <!-- Filters & Search Bar -->
          <div class="row mb-3 g-2">
            <div class="col-sm-6 col-md-2 col-lg-2">
              <select id="layoutSelect" class="form-select">
                <option value="grid">Grid Layout</option>
                <option value="list">List Layout</option>
              </select>
            </div>
            <div class="col-sm-6 col-md-2 col-lg-2">
              <select id="statusFilter" class="form-select">
                <option value="all">All Statuses</option>
                <!-- Statuses will be populated dynamically -->
              </select>
            </div>
            <div class="col-sm-6 col-md-2 col-lg-2">
              <select id="categoryFilter" class="form-select">
                <option value="all">All Categories</option>
                <!-- Categories will be populated dynamically -->
              </select>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 ms-auto">
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search Equipment...">
              </div>
            </div>
          </div>

          <!-- Equipment List (scrollable) -->
          <div id="equipmentContainer" class="flex-grow-1 overflow-auto" style="height: calc(100vh - 300px);">
            <div class="row g-2" id="equipmentCardsContainer">
              <div class="col-12 text-center py-5" id="loadingIndicator">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Loading equipment...</p>
              </div>
              <div class="col-12 text-center py-5 d-none" id="noResultsMessage">
                <i class="bi bi-exclamation-circle fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No equipment found matching your criteria</p>
              </div>
            </div>
          </div>

          <!-- Pagination Controls (fixed at bottom) -->
          <div class="d-flex justify-content-center mt-auto pt-3">
            <nav aria-label="Equipment pagination">
              <ul class="pagination" id="paginationContainer">
                <li class="page-item disabled" id="prevPage">
                  <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="visually-hidden">Previous</span>
                  </a>
                </li>
                <li class="page-item active">
                  <a class="page-link" href="#" data-page="1">1</a>
                </li>
                <li class="page-item" id="nextPage">
                  <a class="page-link" href="#" data-page="2">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="visually-hidden">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
  </main>
</div>
@endsection

@section('scripts')
  <!-- Combined JS resources -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {

      // Authentication check
      const token = localStorage.getItem("adminToken");
      if (!token) {
        window.location.href = "/admin/admin-login";
        return;
      }

      // DOM elements
      const equipmentContainer = document.getElementById("equipmentContainer");
      const searchInput = document.getElementById("searchInput");
      const layoutSelect = document.getElementById("layoutSelect");
      const statusFilter = document.getElementById("statusFilter");
      const categoryFilter = document.getElementById("categoryFilter");
      const loadingIndicator = document.getElementById("loadingIndicator");
      const noResultsMessage = document.getElementById("noResultsMessage");
      const paginationContainer = document.getElementById("paginationContainer");
      const addEquipmentBtn = document.getElementById("addEquipmentBtn");

// State variables
let allEquipment = [];
let filteredEquipment = [];
let categories = [];
let itemsPerPage = 12;
let currentPage = 1;
let totalPages = 1;

      // Update the init function to fetch statuses
     // Update the init function
async function init() {
  try {
    // Fetch equipment data
    await fetchEquipment();

    // Fetch and populate dropdowns
    await fetchStatuses();
    await fetchCategories();

    // Set up event listeners
    setupEventListeners();

    // Initialize pagination
    initializePagination();
  } catch (error) {
    console.error("Initialization error:", error);
    alert("Failed to initialize page. Please try again.");
  }
}

      // Fetch and populate availability statuses
      async function fetchStatuses() {
        try {
          const response = await fetch("http://127.0.0.1:8000/api/availability-statuses", {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          });

          if (response.status === 401) {
            console.error("Unauthorized: Invalid or expired token.");
            localStorage.removeItem("adminToken");
            alert("Your session has expired. Please log in again.");
            setTimeout(() => {
              window.location.href = "/admin/admin-login";
            }, 2000);
            return;
          }

          if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
          }

          const data = await response.json();

          // Populate dropdown with status data
          populateStatusFilter(data);

        } catch (error) {
          console.error("Error fetching statuses:", error);
        }
      }

      // Populate status filter dropdown
      function populateStatusFilter(statuses) {
        statusFilter.innerHTML = '<option value="all">All Statuses</option>';

        statuses.forEach((status) => {
          const option = document.createElement("option");
          option.value = status.status_id;
          option.textContent = status.status_name;
          statusFilter.appendChild(option);
        });
      }

      // Fetch equipment data from API
      async function fetchEquipment() {
        try {
          loadingIndicator.classList.remove("d-none");
          noResultsMessage.classList.add("d-none");

          const response = await fetch(
            "http://127.0.0.1:8000/api/equipment",
            {
              headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
              },
            }
          );

          if (!response.ok) {
            throw new Error(
              `Error ${response.status}: ${response.statusText}`
            );
          }

          const data = await response.json();
          allEquipment = data.data || [];
          filteredEquipment = [...allEquipment];

          // Render equipment dynamically
          renderEquipment(allEquipment);
        } catch (error) {
          console.error("Error fetching equipment:", error);
          loadingIndicator.classList.add("d-none");
          noResultsMessage.classList.remove("d-none");
          noResultsMessage.innerHTML = `<p class="text-danger">Failed to load equipment. Please try again later.</p>`;
        }
      }

      // Fetch and populate equipment categories
      async function fetchCategories() {
        try {
          const response = await fetch("http://127.0.0.1:8000/api/equipment-categories", {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          });

          if (response.status === 401) {
            console.error("Unauthorized: Invalid or expired token.");
            localStorage.removeItem("adminToken");
            alert("Your session has expired. Please log in again.");
            setTimeout(() => {
              window.location.href = "/admin/admin-login";
            }, 2000);
            return;
          }

          if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
          }

          const data = await response.json();

          // Log to inspect
          console.log("Equipment categories:", data);

          // Populate dropdown directly with API data
          populateCategoryFilter(data);

        } catch (error) {
          console.error("Error fetching categories:", error);
        }
      }

      // Populate category filter dropdown
      function populateCategoryFilter(categories) {
        categoryFilter.innerHTML = '<option value="all">All Categories</option>';

        categories.forEach((category) => {
          const option = document.createElement("option");
          option.value = category.category_id;      // Use category_id for value
          option.textContent = category.category_name;
          categoryFilter.appendChild(option);
        });
      }

// Render equipment cards
function renderEquipment(equipmentList) {
  loadingIndicator.classList.add("d-none");

  // Clear existing content
  const container = document.getElementById('equipmentCardsContainer');
  container.innerHTML = "";

  if (equipmentList.length === 0) {
    // Show no equipment found message with icon
    container.innerHTML = `
      <div class="col-12 text-center py-5">
        <i class="bi bi-tools fs-1 text-muted" style="font-size: 4rem !important;"></i>
        <p class="mt-2 text-muted">No equipment found.</p>
      </div>
    `;
    // Clear pagination when no results
    paginationContainer.innerHTML = '';
    return;
  }

  noResultsMessage.classList.add("d-none");

  // Calculate pagination
  totalPages = Math.ceil(equipmentList.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = Math.min(startIndex + itemsPerPage, equipmentList.length);

  // Get layout selection
  const layout = layoutSelect.value;

  // Set container class based on layout
  container.className = layout === "list" ? "row g-3" : "row g-2";

  // Render only the current page's items
  for (let i = startIndex; i < endIndex; i++) {
    const equipment = equipmentList[i];
    const statusClass = getStatusClass(equipment.status.status_name);
    
   // Find primary image - check if images array exists and has valid images
let primaryImage = "https://res.cloudinary.com/dn98ntlkd/image/upload/v1759850278/t4fyv56wog6pglhwvwtn.png";

if (equipment.images && equipment.images.length > 0) {
  const validImages = equipment.images.filter(img => img.image_url && img.image_url.trim() !== '');
  
  if (validImages.length > 0) {
    const sortOrder1Image = validImages.find(img => img.sort_order === 1);
    const primaryTypeImage = validImages.find(img => img.image_type === "Primary");
    
    primaryImage = sortOrder1Image?.image_url || 
                  primaryTypeImage?.image_url || 
                  validImages[0]?.image_url || 
                  primaryImage;
  }
}

    const card = document.createElement("div");
    card.dataset.status = equipment.status.status_id.toString();
    card.dataset.category = equipment.category.category_id.toString();
    card.dataset.title = equipment.equipment_name.toLowerCase();

  if (layout === "list") {
  // List layout
  card.className = "col-12 equipment-card mb-0";
  card.innerHTML = `
    <div class="card h-100 shadow-sm rounded-3">
      <div class="row g-0">
        <div class="col-md-2" style="max-width: 120px; flex: 0 0 120px;">
          <img src="${primaryImage}" 
               class="img-fluid rounded-start" 
               style="width: 120px; height: 120px; object-fit: cover;" 
               alt="${equipment.equipment_name}">
        </div>
        <div class="col-md-8">
          <div class="card-body py-3">
            <h5 class="card-title fw-bold mb-2">${equipment.equipment_name}</h5>
            <p class="card-text mb-2">
              <span class="badge ${statusClass} me-2">${equipment.status.status_name}</span>
<small class="text-muted">
  <i class="bi bi-tag-fill text-primary me-1"></i>${equipment.category.category_name}
  <i class="bi bi-box-fill text-primary ms-2 me-1"></i>${equipment.available_quantity}/${equipment.total_quantity} available
</small>
            </p>
            <p class="card-text text-muted mb-0">
              ${equipment.description || "No description available"}
            </p>
          </div>
        </div>
        <div class="col-md-2 d-flex align-items-center justify-content-center">
          <div class="d-grid gap-2 w-100 px-2">
            <a href="/admin/edit-equipment?id=${equipment.equipment_id}" 
               class="btn btn-sm btn-primary">
               Manage
            </a>
            <button class="btn btn-sm btn-outline-danger btn-delete" 
                    data-id="${equipment.equipment_id}">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
    } else {
  // Grid layout
  card.className = "col-md-4 col-lg-3 equipment-card mb-3";
  card.innerHTML = `
    <div class="card h-100">
      <img src="${primaryImage}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="${equipment.equipment_name}">
      <div class="card-body d-flex flex-column p-2">
        <div>
          <h6 class="card-title mb-1 fw-bold">${equipment.equipment_name}</h6>
<p class="card-text text-muted mb-1 small">
  <i class="bi bi-tag-fill text-primary me-1"></i>${equipment.category.category_name}
  <i class="bi bi-box-fill text-primary ms-2 me-1"></i>${equipment.available_quantity}/${equipment.total_quantity}
</p>
          <span class="badge ${statusClass} mb-2">${equipment.status.status_name}</span>
          <p class="card-text mb-2 small text-truncate">${equipment.description || "No description available"}</p>
        </div>
        <div class="equipment-actions mt-auto d-grid gap-1">
          <a href="/admin/edit-equipment?id=${equipment.equipment_id}" class="btn btn-sm btn-primary btn-manage">Manage</a>
          <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${equipment.equipment_id}">Delete</button>
        </div>
      </div>
    </div>
  `;
}

    container.appendChild(card);
  }

  // Add event listeners to new buttons
  addButtonEventListeners();

  // Update pagination controls
  updatePagination();
}

      // Get appropriate status class
      function getStatusClass(status) {
        switch (status.toLowerCase()) {
          case "available":
            return "bg-success";
          case "reserved":
            return "bg-warning text-dark";
          case "unavailable":
            return "bg-danger";
          case "under maintenance":
            return "bg-info text-dark";
          default:
            return "bg-secondary";
        }
      }

      // Set up event listeners
// Set up event listeners
function setupEventListeners() {
  // Filter controls
  searchInput.addEventListener("input", filterEquipment);
  statusFilter.addEventListener("change", filterEquipment);
  categoryFilter.addEventListener("change", filterEquipment);
  
  // Simple layout switch
  layoutSelect.addEventListener("change", function() {
    filterEquipment();
  });
}

      // Add event listeners to manage and delete buttons
function addButtonEventListeners() {
    // Delete buttons
    document.querySelectorAll(".btn-delete").forEach((button) => {
        button.addEventListener("click", function () {
            const equipmentId = this.dataset.id;
            const equipmentName = this.closest('.card').querySelector('.card-title').textContent.trim();
            
            // Show confirmation modal instead of confirm()
            showDeleteConfirmationModal(equipmentId, equipmentName);
        });
    });
}

function showDeleteConfirmationModal(equipmentId, equipmentName) {
    // Create modal HTML
const modalHtml = `
    <div class="modal fade" id="deleteEquipmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2.5rem;"></i>
                    <p class="mt-3 mb-1">Are you sure you want to delete <strong>"${equipmentName}"</strong>?</p>
                    <p class="text-danger mt-1">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteEquipmentBtn">Delete Equipment</button>
                </div>
            </div>
        </div>
    </div>
`;

    
    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Initialize and show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteEquipmentModal'));
    modal.show();
    
    // Handle confirm button click
    document.getElementById('confirmDeleteEquipmentBtn').addEventListener('click', async function() {
        try {
            const success = await deleteEquipment(equipmentId);
            if (success) {
                showToast('Equipment deleted successfully!', 'success');
                await fetchEquipment(); // Refresh the list
            }
        } catch (error) {
            console.error("Error deleting equipment:", error);
            showToast('Failed to delete equipment: ' + error.message, 'error');
        } finally {
            modal.hide();
            // Remove modal from DOM after hiding
            setTimeout(() => {
                document.getElementById('deleteEquipmentModal')?.remove();
            }, 300);
        }
    });
    
    // Remove modal from DOM when hidden
    document.getElementById('deleteEquipmentModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Toast notification function (copied from edit-equipment)
window.showToast = function (message, type = 'success', duration = 3000) {
    const toast = document.createElement('div');

    // Toast base styles
    toast.className = `toast align-items-center border-0 position-fixed start-0 mb-2`;
    toast.style.zIndex = '1100';
    toast.style.bottom = '0';
    toast.style.left = '0';
    toast.style.margin = '1rem';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
    toast.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    // Colors
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
        }, 400);
    }, duration);
};

      // Delete equipment
     async function deleteEquipment(id) {
    try {
        const response = await fetch(
            `http://127.0.0.1:8000/api/admin/equipment/${id}`,
            {
                method: "DELETE",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            }
        );

        if (response.status === 401) {
            console.error("Unauthorized: Invalid or expired token.");
            localStorage.removeItem("adminToken");
            alert("Your session has expired. Please log in again.");
            setTimeout(() => {
                window.location.href = "/admin/admin-login";
            }, 2000);
            return false;
        }

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || "Failed to delete equipment");
        }

        const result = await response.json();
        return true;

    } catch (error) {
        console.error("Error deleting equipment:", error);
        alert(error.message || "Failed to delete equipment");
        throw error;
    }
}

     // Filter Equipment based on all criteria
// Filter Equipment based on all criteria
function filterEquipment() {
  const searchTerm = searchInput.value.toLowerCase();
  const status = statusFilter.value;
  const category = categoryFilter.value;
  const layout = layoutSelect.value;

  // Reset to first page when filters change
  currentPage = 1;

  // Filter the equipment array
  filteredEquipment = allEquipment.filter(equipment => {
    const equipmentStatus = equipment.status.status_id.toString();
    const equipmentCategory = equipment.category.category_id.toString();
    const equipmentTitle = equipment.equipment_name.toLowerCase();

    const matchesSearch = equipmentTitle.includes(searchTerm);
    const matchesStatus = status === "all" || equipmentStatus === status;
    const matchesCategory = category === "all" || equipmentCategory === category;

    return matchesSearch && matchesStatus && matchesCategory;
  });
  
  // Re-render with filtered results
  renderEquipment(filteredEquipment);
}


      // Initialize pagination
      function initializePagination() {
        updatePagination();
        showPage(1);

        // Event delegation for pagination (handles dynamically created elements)
        paginationContainer.addEventListener("click", function (e) {
          if (e.target.classList.contains("page-link")) {
            e.preventDefault();

            const page = parseInt(e.target.getAttribute("data-page"));
            if (!isNaN(page)) {
              showPage(page);
            } else if (e.target.closest("#prevPage")) {
              // Previous page button
              if (currentPage > 1) {
                showPage(currentPage - 1);
              }
            } else if (e.target.closest("#nextPage")) {
              // Next page button
              if (currentPage < totalPages) {
                showPage(currentPage + 1);
              }
            }
          }
        });
      }

      // Show a specific page
      function showPage(page) {
        currentPage = page;

        // Re-render the equipment with the new page
        renderEquipment(filteredEquipment);
      }

      // Update pagination controls
      function updatePagination() {
        const totalPages = Math.ceil(filteredEquipment.length / itemsPerPage);

        // Clear existing pagination
        paginationContainer.innerHTML = "";


        // Don't show pagination if there's only 1 page or no items
        if (totalPages <= 1) {
          return;
        }

        // Previous button
        const prevLi = document.createElement("li");
        prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
        prevLi.id = "prevPage";
        prevLi.innerHTML = `
            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
              <span aria-hidden="true">&laquo;</span>
              <span class="visually-hidden">Previous</span>
            </a>
          `;
        paginationContainer.appendChild(prevLi);

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        // Adjust start page if we're near the end
        if (endPage - startPage + 1 < maxVisiblePages) {
          startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
          const pageLi = document.createElement("li");
          pageLi.className = `page-item ${i === currentPage ? "active" : ""}`;
          pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
          paginationContainer.appendChild(pageLi);
        }
        // Next button
        const nextLi = document.createElement("li");
        nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`;
        nextLi.id = "nextPage";
        nextLi.innerHTML = `
                    <a class="page-link" href="#" data-page="${currentPage + 1}">
                      <span aria-hidden="true">&raquo;</span>
                      <span class="visually-hidden">Next</span>
                    </a>
                  `;
        paginationContainer.appendChild(nextLi);

        // Add event listeners
        paginationContainer.querySelectorAll(".page-link[data-page]").forEach((link) => {
          link.addEventListener("click", function (e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute("data-page"));
            if (page >= 1 && page <= totalPages) {
              showPage(page);
            }
          });
        });

        // Previous page event
        prevLi.querySelector(".page-link").addEventListener("click", function (e) {
          e.preventDefault();
          if (currentPage > 1) {
            showPage(currentPage - 1);
          }
        });

        // Next page event
        nextLi.querySelector(".page-link").addEventListener("click", function (e) {
          e.preventDefault();
          if (currentPage < totalPages) {
            showPage(currentPage + 1);
          }
        });
      }

      // Start the application
      init();
    });
  </script>
@endsection
@extends('layouts.admin')

@section('title', 'Facilities Management')

@section('content')

<style>

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

  #facilitiesContainer {
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
  #facilitiesContainer::-webkit-scrollbar {
    width: 6px;
  }

  #facilitiesContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  #facilitiesContainer::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
  }

  #facilitiesContainer::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  /* Firefox */
  #facilitiesContainer {
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
  }
</style>

<link rel="stylesheet" href="{{ asset('css/admin/facilities.css') }}">
<div id="layout">
  <!-- Main Content -->
  <main id="main">
    <div class="container-fluid bg-light rounded p-4 d-flex flex-column h-100">
      <div class="container-fluid d-flex flex-column h-100">

        <!-- Header & Controls -->
        <div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title m-0 fw-bold">Manage Facilities</h2>
            <div>
              <a href="{{ url('/admin/add-facility') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Add New
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
                <input type="text" id="searchInput" class="form-control" placeholder="Search Facilities...">
              </div>
            </div>
          </div>

          <!-- Facilities List (scrollable) -->
          <div id="facilitiesContainer" class="flex-grow-1 overflow-auto" style="height: calc(100vh - 300px);">
            <div class="row g-2" id="facilitiesCardsContainer">
              <div class="col-12 text-center py-5" id="loadingIndicator">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Loading facilities...</p>
              </div>
              <div class="col-12 text-center py-5 d-none" id="noResultsMessage">
                <i class="bi bi-exclamation-circle fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No facilities found matching your criteria</p>
              </div>
            </div>
          </div>

          <!-- Pagination Controls (fixed at bottom) -->
          <div class="d-flex justify-content-center mt-auto pt-3">
            <nav aria-label="Facilities pagination">
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
      const facilitiesContainer = document.getElementById("facilitiesContainer");
      const searchInput = document.getElementById("searchInput");
      const layoutSelect = document.getElementById("layoutSelect");
      const statusFilter = document.getElementById("statusFilter");
      const categoryFilter = document.getElementById("categoryFilter");
      const loadingIndicator = document.getElementById("loadingIndicator");
      const noResultsMessage = document.getElementById("noResultsMessage");
      const paginationContainer = document.getElementById("paginationContainer");
      const addFacilitiesBtn = document.getElementById("addFacilitiesBtn");

      // State variables
      let allFacilities = [];
      let filteredFacilities = [];
      let categories = [];
      let itemsPerPage = 12;
      let currentPage = 1;
      let totalPages = 1;

      // Update the init function to fetch statuses
      async function init() {
        try {
          // Fetch facilities data
          await fetchFacilities();

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

      // Fetch facilities data from API
      async function fetchFacilities() {
        try {
          loadingIndicator.classList.remove("d-none");
          noResultsMessage.classList.add("d-none");

          const response = await fetch(
            "http://127.0.0.1:8000/api/facilities",
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
          allFacilities = data.data || [];
          filteredFacilities = [...allFacilities];

          // Render facilities dynamically
          renderFacilities(allFacilities);
        } catch (error) {
          console.error("Error fetching facilities:", error);
          loadingIndicator.classList.add("d-none");
          noResultsMessage.classList.remove("d-none");
          noResultsMessage.innerHTML = `<p class="text-danger">Failed to load facilities. Please try again later.</p>`;
        }
      }

      // Fetch and populate facilities categories
      async function fetchCategories() {
        try {
          const response = await fetch("http://127.0.0.1:8000/api/facility-categories", {
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
          console.log("Facilities categories:", data);

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

      // Render facilities cards
      function renderFacilities(facilitiesList) {
        loadingIndicator.classList.add("d-none");

        // Clear existing content
        const container = document.getElementById('facilitiesCardsContainer');
        container.innerHTML = "";

        if (facilitiesList.length === 0) {
          // Show no facilities found message with icon
          container.innerHTML = `
                                        <div class="col-12 text-center py-5">
                                          <i class="bi bi-tools fs-1 text-muted" style="font-size: 4rem !important;"></i>
                                          <p class="mt-2 text-muted">No facilities found.</p>
                                        </div>
                                      `;
          // Clear pagination when no results
          paginationContainer.innerHTML = '';
          return;
        }

        noResultsMessage.classList.add("d-none");

        // Calculate pagination
        totalPages = Math.ceil(facilitiesList.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, facilitiesList.length);

        // Render only the current page's items
        for (let i = startIndex; i < endIndex; i++) {
          const facilities = facilitiesList[i];
          const statusClass = getStatusClass(facilities.status.status_name);
          const primaryImage =
            facilities.images?.find((img) => img.type_id === 1)?.image_url ||
            "https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp"

          const card = document.createElement("div");
          card.className = "col-md-4 col-lg-3 facilities-card mb-3";
          card.dataset.status = facilities.status.status_id.toString();
          card.dataset.category = facilities.category.category_id.toString();
          card.dataset.title = facilities.facility_name.toLowerCase();



card.innerHTML = `
  <div class="card h-100">
    <img src="${primaryImage}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="${facilities.facilities_name}">
    <div class="card-body d-flex flex-column p-2">
      <div>
        <h6 class="card-title mb-1 fw-bold">${facilities.facility_name}</h6>
        <p class="card-text text-muted mb-1 small">
          <i class="bi bi-tag-fill text-primary me-1"></i>
          ${facilities.category.category_name} 
          <span class="ms-2">| ${facilities.subcategory.subcategory_name}</span>
        </p>
        <span class="badge ${statusClass} mb-2">${facilities.status.status_name}</span>
        <p class="card-text mb-2 small text-truncate">${facilities.description || "No description available"}</p>
      </div>
      <div class="facilities-actions mt-auto d-grid gap-1">
        <a href="/admin/edit-facility?id=${facilities.facility_id}" class="btn btn-sm btn-primary btn-manage">Manage</a>
        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${facilities.facility_id}">Delete</button>
      </div>
    </div>
  </div>
`;



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
      function setupEventListeners() {
        // Filter controls
        [
          searchInput,
          layoutSelect,
          statusFilter,
          categoryFilter,
        ].forEach((control) => {
          control.addEventListener("change", filterFacilities);
        });
        searchInput.addEventListener("input", filterFacilities);
      }

      // Add event listeners to manage and delete buttons
      function addButtonEventListeners() {
        // Manage buttons
        document.querySelectorAll(".btn-manage").forEach((button) => {
          button.addEventListener("click", function () {
            const facilitiesId = this.dataset.id;
            window.location.href = `edit-facilities.html?id=${facilitiesId}`;
          });
        });

        // Delete buttons
        document.querySelectorAll(".btn-delete").forEach((button) => {
          button.addEventListener("click", async function () {
            const facilitiesId = this.dataset.id;
            if (confirm("Are you sure you want to delete this facilities?")) {
              try {
                await deleteFacilities(facilitiesId);
                await fetchFacilities(); // Refresh the list
              } catch (error) {
                console.error("Error deleting facilities:", error);
                alert("Failed to delete facilities");
              }
            }
          });
        });
      }

      // Delete facilities
      async function deleteFacilities(id) {
        try {
          const response = await fetch(
            `http://127.0.0.1:8000/api/admin/facilities/${id}`,
            {
              method: "DELETE",
              headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
              },
            }
          );

          if (!response.ok) {
            throw new Error("Failed to delete facilities");
          }

          return true;
        } catch (error) {
          console.error("Error deleting facilities:", error);
          throw error;
        }
      }

      // Filter Facilities based on all criteria
      function filterFacilities() {
        const searchTerm = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        const category = categoryFilter.value;
        const layout = layoutSelect.value;
        // Apply layout view
        facilitiesContainer.className = `row g-2 ${layout === "list" ? "list-view" : ""}`;

        // Reset to first page when filters change
        currentPage = 1;

        // Filter the facilities array
        filteredFacilities = allFacilities.filter(facilities => {
          const facilitiesStatus = facilities.status.status_id.toString();
          const facilitiesCategory = facilities.category.category_id.toString(); // Use category_id instead of category_name
          const facilitiesTitle = facilities.facilities_name.toLowerCase();

          const matchesSearch = facilitiesTitle.includes(searchTerm);
          const matchesStatus = status === "all" || facilitiesStatus === status;
          const matchesCategory = category === "all" || facilitiesCategory === category; // Compare by ID

          return matchesSearch && matchesStatus && matchesCategory;
        });
        // Re-render with filtered results
        renderFacilities(filteredFacilities);
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

        // Re-render the facilities with the new page
        renderFacilities(filteredFacilities);
      }

      // Update pagination controls
      function updatePagination() {
        const totalPages = Math.ceil(filteredFacilities.length / itemsPerPage);

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
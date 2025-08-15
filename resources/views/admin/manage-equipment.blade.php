@extends('layouts.admin')

@section('title', 'Equipment Management')

@section('content')
  <link rel="stylesheet" href="{{ asset('admin/css/equipment.css') }}">
  <div id="layout">
    <!-- Main Content -->
    <main id="main">
    <div class="container-fluid bg-light rounded p-4">
      <div class="container-fluid">
      <!-- Header & Controls -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Equipment</h2>
        <div>
        <a href="{{  url('/admin/add-equipment') }}" class="btn btn-primary">
          <i class="bi bi-plus-circle-fill me-2"></i>Add New Equipment
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
          <option value="available">Available</option>
          <option value="reserved">Reserved</option>
          <option value="unavailable">Unavailable</option>
          <option value="under maintenance">Under Maintenance</option>
        </select>
        </div>
        <div class="col-sm-6 col-md-2 col-lg-2">
        <select id="departmentFilter" class="form-select">
          <option value="all">All Departments</option>
          <!-- Departments will be populated dynamically -->
        </select>
        </div>
        <div class="col-sm-6 col-md-2 col-lg-2">
        <select id="categoryFilter" class="form-select">
          <option value="all">All Categories</option>
          <!-- Categories will be populated dynamically -->
        </select>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="search-container">
          <i class="bi bi-search"></i>
          <input type="text" id="searchInput" class="form-control" placeholder="Search Equipment..." />
        </div>
        </div>
      </div>

      <!-- Equipment List -->

      <div id="facilityContainer" class="row g-3">
        <div class="col-12 text-center py-5" id="loadingIndicator">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading equipment...</p>
        </div>
        <div class="col-12 text-center py-5 d-none" id="noResultsMessage">
        <i class="bi bi-exclamation-circle fs-1 text-muted"></i>
        <p class="mt-2 text-muted">
          No equipment found matching your criteria
        </p>
        </div>
      </div>
      <!-- Pagination Controls -->
      <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Equipment pagination">
        <ul class="pagination" id="paginationContainer">
          <li class="page-item disabled" id="prevPage">
          <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
          </li>
          <li class="page-item active">
          <a class="page-link" href="#" data-page="1">1</a>
          </li>
          <li class="page-item" id="nextPage">
          <a class="page-link" href="#" data-page="2">Next</a>
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
      window.location.href = "admin/admin-login";
      return;
    }

    // DOM elements
    const facilityContainer = document.getElementById("facilityContainer");
    const searchInput = document.getElementById("searchInput");
    const layoutSelect = document.getElementById("layoutSelect");
    const statusFilter = document.getElementById("statusFilter");
    const departmentFilter = document.getElementById("departmentFilter");
    const categoryFilter = document.getElementById("categoryFilter");
    const loadingIndicator = document.getElementById("loadingIndicator");
    const noResultsMessage = document.getElementById("noResultsMessage");
    const paginationContainer = document.getElementById(
      "paginationContainer"
    );

    // State variables
    let allEquipment = [];
    let userDepartments = [];
    let categories = [];
    let itemsPerPage = 9;
    let currentPage = 1;

    // Initialize the page
    async function init() {
      try {
      // Fetch user data and departments
      await fetchUserData();

      // Fetch equipment data
      await fetchEquipment();

      // Fetch and populate dropdowns
      await fetchDepartments();
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

    // Fetch user data including departments
    async function fetchUserData() {
      try {
      const response = await fetch(
        "http://127.0.0.1:8000/api/admin/profile",
        {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
        credentials: "include",
        }
      );

      if (response.status === 401) {
        console.error("Unauthorized: Invalid or expired token.");
        localStorage.removeItem("adminToken");
        alert("Your session has expired. Please log in again.");
        window.location.href = "/admin/admin-login";
        return;
      }

      if (!response.ok) {
        throw new Error(
        `Error ${response.status}: ${response.statusText}`
        );
      }

      const userData = await response.json();
      userDepartments = userData.departments || []; // Changed from userData.admin.departments to userData.departments

      // Populate department filter
      populateDepartmentFilter();

      // Update admin info in sidebar
      updateAdminInfo(userData);
      } catch (error) {
      console.error("Error fetching user data:", error);
      alert(
        "Failed to fetch user data. Please check your network connection."
      );
      }
    }

    // Add this new helper function
    function updateAdminInfo(userData) {
      const adminName = document.querySelector("#sidebar h5");
      const adminRole = document.querySelector("#sidebar p.text-muted");

      if (userData.first_name && userData.last_name) {
      adminName.textContent = `${userData.first_name} ${userData.last_name}`;
      }

      if (userData.role) {
      adminRole.textContent = userData.role.role_name || "Admin";
      }
    }

    // Populate department filter dropdown
    function populateDepartmentFilter() {
      departmentFilter.innerHTML =
      '<option value="all">All Departments</option>';

      userDepartments.forEach((dept) => {
      const option = document.createElement("option");
      option.value = dept.department_name;
      option.textContent = dept.department_name;
      departmentFilter.appendChild(option);
      });
    }

    // Fetch equipment data from API
    async function fetchEquipment() {
      try {
      loadingIndicator.classList.remove("d-none");
      noResultsMessage.classList.add("d-none");

      const response = await fetch(
        "http://127.0.0.1:8000/api/admin/equipment",
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

      // Render equipment dynamically
      renderEquipment(allEquipment);
      } catch (error) {
      console.error("Error fetching equipment:", error);
      loadingIndicator.classList.add("d-none");
      noResultsMessage.classList.remove("d-none");
      noResultsMessage.innerHTML = `<p class="text-danger">Failed to load equipment. Please try again later.</p>`;
      }
    }

    // Fetch and populate departments
    async function fetchDepartments() {
      try {
      const response = await fetch(
        "http://127.0.0.1:8000/api/departments",
        {
        headers: {
          Accept: "application/json",
        },
        }
      );

      if (response.status === 401) {
        console.error("Unauthorized: Invalid or expired token.");
        localStorage.removeItem("adminToken");
        alert("Your session has expired. Please log in again.");
        window.location.href = "/admin/admin-login";
        return;
      }

      if (!response.ok) {
        throw new Error(
        `Error ${response.status}: ${response.statusText}`
        );
      }

      const data = await response.json();
      populateDropdown(departmentFilter, data, "department_name");
      } catch (error) {
      console.error("Error fetching departments:", error);
      }
    }
    // Fetch and populate equipment categories
    async function fetchCategories() {
      try {
      const response = await fetch(
        "http://127.0.0.1:8000/api/equipment-categories",
        {
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
        window.location.href = "/admin/admin-login";
        return;
      }

      if (!response.ok) {
        throw new Error(
        `Error ${response.status}: ${response.statusText}`
        );
      }

      const data = await response.json();

      // Log to inspect
      console.log("Equipment categories:", data);

      populateDropdown(categoryFilter, data, "category_name");
      } catch (error) {
      console.error("Error fetching categories:", error);
      }
    }

    // Populate dropdown helper function
    function populateDropdown(dropdown, data, key) {
      dropdown.innerHTML = "";

      const defaultOption = document.createElement("option");
      defaultOption.value = "all";

      // Adjust label based on dropdown ID
      if (dropdown.id === "departmentFilter") {
      defaultOption.textContent = "All Departments";
      } else if (dropdown.id === "categoryFilter") {
      defaultOption.textContent = "All Categories";
      } else {
      defaultOption.textContent = "All";
      }

      dropdown.appendChild(defaultOption);

      data.forEach((item) => {
      const option = document.createElement("option");
      option.value = item[key];
      option.textContent = item[key];
      dropdown.appendChild(option);
      });
    }

    // Extract unique categories from equipment
    function extractCategories() {
      const categorySet = new Set();
      allEquipment.forEach((item) => {
      if (item.category && item.category.category_name) {
        categorySet.add(item.category.category_name);
      }
      });

      categories = Array.from(categorySet);
      populateCategoryFilter();
    }

    // Populate category filter dropdown
    function populateCategoryFilter() {
      categoryFilter.innerHTML =
      '<option value="all">All Categories</option>';

      categories.forEach((category) => {
      const option = document.createElement("option");
      option.value = category;
      option.textContent = category;
      categoryFilter.appendChild(option);
      });
    }

    // Render equipment cards
    function renderEquipment(equipmentList) {
      loadingIndicator.classList.add("d-none");

      // Clear existing content
      facilityContainer.innerHTML = "";

      if (equipmentList.length === 0) {
      noResultsMessage.classList.remove("d-none");
      return;
      }

      noResultsMessage.classList.add("d-none");

      equipmentList.forEach((equipment) => {
      const statusClass = getStatusClass(equipment.status.status_name);
      const primaryImage =
        equipment.images?.find((img) => img.type_id === 1)?.image_url ||
        "https://via.placeholder.com/150"; // Use a placeholder image

      const card = document.createElement("div");
      card.className = "col-md-4 facility-card mb-4";
      card.dataset.status = equipment.status.status_name.toLowerCase();
      card.dataset.department = equipment.department.department_name;
      card.dataset.category = equipment.category.category_name;
      card.dataset.title = equipment.equipment_name.toLowerCase();

      card.innerHTML = `
              <div class="card h-100">
                <img src="${primaryImage}" class="card-img-top" alt="${equipment.equipment_name
        }">
                <div class="card-body d-flex flex-column">
                  <div>
                    <h5 class="card-title">${equipment.equipment_name
        }</h5>
                    <p class="card-text text-muted mb-2">
                      <i class="bi bi-tag-fill text-primary"></i> ${equipment.category.category_name
        } |
                      <i class="bi bi-building-fill text-primary"></i> ${equipment.department.department_name
        }
                    </p>
                    <p class="${statusClass}">${equipment.status.status_name
        }</p>
                    <p class="card-text mb-3">${equipment.description ||
        "No description available"
        }</p>
                  </div>
                  <div class="facility-actions mt-auto pt-3">
                    <button class="btn btn-manage btn-flex" data-id="${equipment.equipment_id
        }">Manage</button>
                    <button class="btn btn-outline-danger btn-delete" data-id="${equipment.equipment_id
        }">Delete</button>
                  </div>
                </div>
              </div>
            `;

      facilityContainer.appendChild(card);
      });

      // Add event listeners to new buttons
      addButtonEventListeners();

      // Apply initial filters
      filterFacilities();
    }

    // Get appropriate status class
    function getStatusClass(status) {
      switch (status.toLowerCase()) {
      case "available":
        return "status-available";
      case "reserved":
        return "status-reserved";
      case "unavailable":
        return "status-unavailable";
      case "under maintenance":
        return "status-under-maintenance";
      default:
        return "status-unavailable";
      }
    }

    // Set up event listeners
    function setupEventListeners() {
      // Filter controls
      [
      searchInput,
      layoutSelect,
      statusFilter,
      departmentFilter,
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
        const equipmentId = this.dataset.id;
        window.location.href = `edit-equipment.html?id=${equipmentId}`;
      });
      });

      // Delete buttons
      document.querySelectorAll(".btn-delete").forEach((button) => {
      button.addEventListener("click", async function () {
        const equipmentId = this.dataset.id;
        if (confirm("Are you sure you want to delete this equipment?")) {
        try {
          await deleteEquipment(equipmentId);
          await fetchEquipment(); // Refresh the list
        } catch (error) {
          console.error("Error deleting equipment:", error);
          alert("Failed to delete equipment");
        }
        }
      });
      });
    }

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

      if (!response.ok) {
        throw new Error("Failed to delete equipment");
      }

      return true;
      } catch (error) {
      console.error("Error deleting equipment:", error);
      throw error;
      }
    }

    // Filter facilities based on all criteria
    function filterFacilities() {
      const searchTerm = searchInput.value.toLowerCase();
      const status = statusFilter.value;
      const department = departmentFilter.value;
      const category = categoryFilter.value;
      const layout = layoutSelect.value;

      // Apply layout view
      facilityContainer.className = `row g-3 ${layout === "list" ? "list-view" : ""
      }`;

      // Get all facility cards
      const cards = document.querySelectorAll(".facility-card");
      let visibleCount = 0;

      cards.forEach((card) => {
      const cardStatus = card.dataset.status;
      const cardDept = card.dataset.department;
      const cardCategory = card.dataset.category;
      const cardTitle = card.dataset.title;

      const matchesSearch = cardTitle.includes(searchTerm);
      const matchesStatus =
        status === "all" || cardStatus === status.toLowerCase();
      const matchesDept = department === "all" || cardDept === department;
      const matchesCategory =
        category === "all" || cardCategory === category;

      if (
        matchesSearch &&
        matchesStatus &&
        matchesDept &&
        matchesCategory
      ) {
        card.style.display = "block";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
      });

      // Show no results message if no cards are visible
      if (visibleCount === 0) {
      noResultsMessage.classList.remove("d-none");
      } else {
      noResultsMessage.classList.add("d-none");
      }

      // Update pagination
      updatePagination();
    }

    // Initialize pagination
    function initializePagination() {
      updatePagination();
      showPage(1);

      // Page links
      document.querySelectorAll(".page-link[data-page]").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const page = parseInt(this.getAttribute("data-page"));
        showPage(page);
      });
      });

      // Previous page button
      document
      .getElementById("prevPage")
      .addEventListener("click", function (e) {
        e.preventDefault();
        if (!this.classList.contains("disabled")) {
        showPage(currentPage - 1);
        }
      });

      document
      .getElementById("nextPage")
      .addEventListener("click", function (e) {
        e.preventDefault();
        if (!this.classList.contains("disabled")) {
        showPage(currentPage + 1);
        }
      });
    }

    // Show a specific page
    function showPage(page) {
      currentPage = page;
      const startIndex = (page - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;

      // Get all visible cards
      const visibleCards = Array.from(
      document.querySelectorAll(".facility-card")
      ).filter((card) => card.style.display !== "none");

      // Hide all cards first
      document.querySelectorAll(".facility-card").forEach((card) => {
      card.style.display = "none";
      });

      // Show cards for current page
      for (
      let i = startIndex;
      i < endIndex && i < visibleCards.length;
      i++
      ) {
      visibleCards[i].style.display = "block";
      }

      updatePagination();
    }

    // Update pagination controls
    function updatePagination() {
      const visibleCards = Array.from(
      document.querySelectorAll(".facility-card")
      ).filter((card) => card.style.display !== "none");
      const totalPages = Math.ceil(visibleCards.length / itemsPerPage);

      // Clear existing pagination
      paginationContainer.innerHTML = "";

      // Previous button
      const prevLi = document.createElement("li");
      prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
      prevLi.id = "prevPage";
      prevLi.innerHTML =
      '<a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>';
      paginationContainer.appendChild(prevLi);

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
      const pageLi = document.createElement("li");
      pageLi.className = `page-item ${i === currentPage ? "active" : ""}`;
      pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
      paginationContainer.appendChild(pageLi);
      }

      // Next button
      const nextLi = document.createElement("li");
      nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""
      }`;
      nextLi.id = "nextPage";
      nextLi.innerHTML =
      '<a class="page-link" href="#" data-page="' +
      (currentPage + 1) +
      '">Next</a>';
      paginationContainer.appendChild(nextLi);

      // Reattach event listeners
      document.querySelectorAll(".page-link[data-page]").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const page = parseInt(this.getAttribute("data-page"));
        showPage(page);
      });
      });

      document
      .getElementById("prevPage")
      .addEventListener("click", function (e) {
        e.preventDefault();
        if (!this.classList.contains("disabled")) {
        showPage(currentPage - 1);
        }
      });

      document
      .getElementById("nextPage")
      .addEventListener("click", function (e) {
        e.preventDefault();
        if (!this.classList.contains("disabled")) {
        showPage(currentPage + 1);
        }
      });
    }

    // Start the application
    init();
    });
  </script>
@endsection
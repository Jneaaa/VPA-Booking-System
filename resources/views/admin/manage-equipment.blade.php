<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .status-available {
            color: #198754;
            font-weight: 500;
        }
        .status-reserved {
            color: #fd7e14;
            font-weight: 500;
        }
        .status-unavailable {
            color: #dc3545;
            font-weight: 500;
        }
        .status-under-maintenance {
            color: #6c757d;
            font-weight: 500;
        }
        .search-container {
            position: relative;
        }
        .search-container i {
            position: absolute;
            left: 12px;
            top: 10px;
            color: #6c757d;
        }
        .search-container input {
            padding-left: 35px;
        }
        .facility-actions {
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .btn-manage {
            background-color: #0d6efd;
            color: white;
        }
        .btn-manage:hover {
            background-color: #0b5ed7;
            color: white;
        }
        .btn-flex {
            flex: 1;
            margin-right: 8px;
        }
        .list-view .facility-card {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .list-view .card {
            flex-direction: row;
        }
        .list-view .card-img-top {
            width: 200px;
            height: 200px;
            object-fit: cover;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        #loadingIndicator {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        #noResultsMessage {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
        #addEquipmentModal .modal-dialog {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container-fluid bg-light rounded p-4 mt-4">
        <div class="container-fluid">
            <!-- Header & Controls -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manage Equipment</h2>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                        <i class="bi bi-plus-circle-fill me-2"></i>Add New Equipment
                    </button>
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
                    <p class="mt-2 text-muted">No equipment found matching your criteria</p>
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

    <!-- Toast Container for Notifications -->
    <div class="toast-container"></div>

    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEquipmentModalLabel">Add New Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addEquipmentForm">
                        <div class="mb-3">
                            <label for="equipmentName" class="form-label">Equipment Name</label>
                            <input type="text" class="form-control" id="equipmentName" required>
                        </div>
                        <div class="mb-3">
                            <label for="equipmentDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="equipmentDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="equipmentCategory" class="form-label">Category</label>
                            <select class="form-select" id="equipmentCategory" required>
                                <option value="">Select a category</option>
                                <!-- Categories will be populated dynamically -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="equipmentDepartment" class="form-label">Department</label>
                            <select class="form-select" id="equipmentDepartment" required>
                                <option value="">Select a department</option>
                                <!-- Departments will be populated dynamically -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="equipmentStatus" class="form-label">Status</label>
                            <select class="form-select" id="equipmentStatus" required>
                                <option value="available">Available</option>
                                <option value="reserved">Reserved</option>
                                <option value="unavailable">Unavailable</option>
                                <option value="under maintenance">Under Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="equipmentImage" class="form-label">Equipment Image</label>
                            <input class="form-control" type="file" id="equipmentImage" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEquipmentBtn">Save Equipment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Authentication check
            const token = localStorage.getItem("adminToken");
            if (!token) {
                window.location.href = "/admin/admin-login";
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
            const paginationContainer = document.getElementById("paginationContainer");
            const saveEquipmentBtn = document.getElementById("saveEquipmentBtn");
            const equipmentCategory = document.getElementById("equipmentCategory");
            const equipmentDepartment = document.getElementById("equipmentDepartment");

            // State variables
            let allEquipment = [];
            let filteredEquipment = [];
            let userDepartments = [];
            let categories = [];
            let itemsPerPage = 9;
            let currentPage = 1;
            let totalPages = 1;

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
                    showToast("Failed to initialize page. Please try again.", "danger");
                }
            }

            // Fetch user data including departments
            async function fetchUserData() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/admin/profile", {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json",
                        },
                        credentials: "include",
                    });

                    if (response.status === 401) {
                        console.error("Unauthorized: Invalid or expired token.");
                        localStorage.removeItem("adminToken");
                        showToast("Your session has expired. Please log in again.", "warning");
                        setTimeout(() => {
                            window.location.href = "/admin/admin-login";
                        }, 2000);
                        return;
                    }

                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }

                    const userData = await response.json();
                    userDepartments = userData.departments || [];
                    
                    // Populate department filter
                    populateDepartmentFilter();
                } catch (error) {
                    console.error("Error fetching user data:", error);
                    showToast("Failed to fetch user data. Please check your network connection.", "danger");
                }
            }

            // Populate department filter dropdown
            function populateDepartmentFilter() {
                departmentFilter.innerHTML = '<option value="all">All Departments</option>';
                
                userDepartments.forEach((dept) => {
                    const option = document.createElement("option");
                    option.value = dept.department_name;
                    option.textContent = dept.department_name;
                    departmentFilter.appendChild(option);
                });

                // Also populate the department dropdown in the modal
                equipmentDepartment.innerHTML = '<option value="">Select a department</option>';
                userDepartments.forEach((dept) => {
                    const option = document.createElement("option");
                    option.value = dept.department_id;
                    option.textContent = dept.department_name;
                    equipmentDepartment.appendChild(option);
                });
            }

            // Fetch equipment data from API
            async function fetchEquipment() {
                try {
                    loadingIndicator.classList.remove("d-none");
                    noResultsMessage.classList.add("d-none");
                    
                    const response = await fetch("http://127.0.0.1:8000/api/admin/equipment", {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json",
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    allEquipment = data.data || [];
                    filteredEquipment = [...allEquipment];
                    
                    // Render equipment dynamically
                    renderEquipment();
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
                    const response = await fetch("http://127.0.0.1:8000/api/departments", {
                        headers: {
                            Accept: "application/json",
                        },
                    });

                    if (response.status === 401) {
                        console.error("Unauthorized: Invalid or expired token.");
                        localStorage.removeItem("adminToken");
                        showToast("Your session has expired. Please log in again.", "warning");
                        setTimeout(() => {
                            window.location.href = "/admin/admin-login";
                        }, 2000);
                        return;
                    }

                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
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
                    const response = await fetch("http://127.0.0.1:8000/api/equipment-categories", {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json",
                        },
                    });

                    if (response.status === 401) {
                        console.error("Unauthorized: Invalid or expired token.");
                        localStorage.removeItem("adminToken");
                        showToast("Your session has expired. Please log in again.", "warning");
                        setTimeout(() => {
                            window.location.href = "/admin/admin-login";
                        }, 2000);
                        return;
                    }

                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    populateDropdown(categoryFilter, data, "category_name");
                    
                    // Also populate the category dropdown in the modal
                    equipmentCategory.innerHTML = '<option value="">Select a category</option>';
                    data.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.category_id;
                        option.textContent = item.category_name;
                        equipmentCategory.appendChild(option);
                    });
                } catch (error) {
                    console.error("Error fetching categories:", error);
                }
            }

            // Populate dropdown helper function
            function populateDropdown(dropdown, data, key) {
                // Clear existing options except the first "all" option
                while (dropdown.options.length > 1) {
                    dropdown.remove(1);
                }
                
                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item[key];
                    option.textContent = item[key];
                    dropdown.appendChild(option);
                });
            }

            // Set up event listeners
            function setupEventListeners() {
                // Filter controls
                searchInput.addEventListener("input", filterFacilities);
                layoutSelect.addEventListener("change", updateLayout);
                statusFilter.addEventListener("change", filterFacilities);
                departmentFilter.addEventListener("change", filterFacilities);
                categoryFilter.addEventListener("change", filterFacilities);
                
                // Save equipment button
                saveEquipmentBtn.addEventListener("click", addEquipment);
            }

            // Update layout based on selection
            function updateLayout() {
                const layout = layoutSelect.value;
                facilityContainer.className = `row g-3 ${layout === "list" ? "list-view" : ""}`;
                renderEquipment();
            }

            // Filter facilities based on all criteria
            function filterFacilities() {
                const searchTerm = searchInput.value.toLowerCase();
                const status = statusFilter.value;
                const department = departmentFilter.value;
                const category = categoryFilter.value;
                
                filteredEquipment = allEquipment.filter(equipment => {
                    const matchesSearch = equipment.equipment_name.toLowerCase().includes(searchTerm) || 
                                         (equipment.description && equipment.description.toLowerCase().includes(searchTerm));
                    
                    const matchesStatus = status === "all" || 
                                         equipment.status.status_name.toLowerCase() === status.toLowerCase();
                    
                    const matchesDept = department === "all" || 
                                       equipment.department.department_name === department;
                    
                    const matchesCategory = category === "all" || 
                                           equipment.category.category_name === category;
                    
                    return matchesSearch && matchesStatus && matchesDept && matchesCategory;
                });
                
                currentPage = 1; // Reset to first page when filters change
                renderEquipment();
            }

            // Render equipment cards
            function renderEquipment() {
                loadingIndicator.classList.add("d-none");
                
                // Clear existing content
                facilityContainer.innerHTML = "";
                
                if (filteredEquipment.length === 0) {
                    noResultsMessage.classList.remove("d-none");
                    updatePagination();
                    return;
                }
                
                noResultsMessage.classList.add("d-none");
                
                // Calculate pagination
                totalPages = Math.ceil(filteredEquipment.length / itemsPerPage);
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, filteredEquipment.length);
                const currentEquipment = filteredEquipment.slice(startIndex, endIndex);
                
                currentEquipment.forEach((equipment) => {
                    const statusClass = getStatusClass(equipment.status.status_name);
                    const primaryImage = equipment.images?.find((img) => img.type_id === 1)?.image_url ||
                                        "https://via.placeholder.com/300x200?text=No+Image";
                    
                    const card = document.createElement("div");
                    card.className = "col-md-4 facility-card mb-4";
                    card.dataset.status = equipment.status.status_name.toLowerCase();
                    card.dataset.department = equipment.department.department_name;
                    card.dataset.category = equipment.category.category_name;
                    card.dataset.title = equipment.equipment_name.toLowerCase();
                    
                    card.innerHTML = `
                        <div class="card h-100">
                            <img src="${primaryImage}" class="card-img-top" alt="${equipment.equipment_name}">
                            <div class="card-body d-flex flex-column">
                                <div>
                                    <h5 class="card-title">${equipment.equipment_name}</h5>
                                    <p class="card-text text-muted mb-2">
                                        <i class="bi bi-tag-fill text-primary"></i> ${equipment.category.category_name} |
                                        <i class="bi bi-building-fill text-primary"></i> ${equipment.department.department_name}
                                    </p>
                                    <p class="${statusClass}">${equipment.status.status_name}</p>
                                    <p class="card-text mb-3">${equipment.description || "No description available"}</p>
                                </div>
                                <div class="facility-actions mt-auto pt-3">
                                    <button class="btn btn-manage btn-flex" data-id="${equipment.equipment_id}">Manage</button>
                                    <button class="btn btn-outline-danger btn-delete" data-id="${equipment.equipment_id}">Delete</button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    facilityContainer.appendChild(card);
                });
                
                // Add event listeners to new buttons
                addButtonEventListeners();
                
                // Update pagination
                updatePagination();
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

            // Add event listeners to manage and delete buttons
            function addButtonEventListeners() {
                // Manage buttons
                document.querySelectorAll(".btn-manage").forEach((button) => {
                    button.addEventListener("click", function () {
                        const equipmentId = this.dataset.id;
                        window.location.href = `/admin/edit-equipment/${equipmentId}`;
                    });
                });
                
                // Delete buttons
                document.querySelectorAll(".btn-delete").forEach((button) => {
                    button.addEventListener("click", async function () {
                        const equipmentId = this.dataset.id;
                        if (confirm("Are you sure you want to delete this equipment?")) {
                            try {
                                await deleteEquipment(equipmentId);
                            } catch (error) {
                                console.error("Error deleting equipment:", error);
                                showToast("Failed to delete equipment", "danger");
                            }
                        }
                    });
                });
            }

            // Add new equipment
            async function addEquipment() {
                const equipmentName = document.getElementById("equipmentName").value;
                const equipmentDescription = document.getElementById("equipmentDescription").value;
                const equipmentCategory = document.getElementById("equipmentCategory").value;
                const equipmentDepartment = document.getElementById("equipmentDepartment").value;
                const equipmentStatus = document.getElementById("equipmentStatus").value;
                const equipmentImage = document.getElementById("equipmentImage").files[0];
                
                if (!equipmentName || !equipmentCategory || !equipmentDepartment) {
                    showToast("Please fill in all required fields", "warning");
                    return;
                }
                
                try {
                    // Create form data for the request
                    const formData = new FormData();
                    formData.append("equipment_name", equipmentName);
                    formData.append("description", equipmentDescription);
                    formData.append("category_id", equipmentCategory);
                    formData.append("department_id", equipmentDepartment);
                    formData.append("status_id", getStatusId(equipmentStatus));
                    
                    if (equipmentImage) {
                        formData.append("image", equipmentImage);
                    }
                    
                    const response = await fetch("http://127.0.0.1:8000/api/admin/equipment", {
                        method: "POST",
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                        body: formData,
                    });
                    
                    if (!response.ok) {
                        throw new Error("Failed to add equipment");
                    }
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addEquipmentModal'));
                    modal.hide();
                    
                    // Reset the form
                    document.getElementById("addEquipmentForm").reset();
                    
                    // Show success message
                    showToast("Equipment added successfully", "success");
                    
                    // Refresh the equipment list
                    await fetchEquipment();
                } catch (error) {
                    console.error("Error adding equipment:", error);
                    showToast("Failed to add equipment", "danger");
                }
            }
            
            // Helper function to get status ID from status name
            function getStatusId(statusName) {
                const statusMap = {
                    "available": 1,
                    "reserved": 2,
                    "unavailable": 3,
                    "under maintenance": 4
                };
                return statusMap[statusName] || 3; // Default to unavailable
            }

            // Delete equipment
            async function deleteEquipment(id) {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${id}`, {
                        method: "DELETE",
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json",
                        },
                    });
                    
                    if (!response.ok) {
                        throw new Error("Failed to delete equipment");
                    }
                    
                    // Show success message
                    showToast("Equipment deleted successfully", "success");
                    
                    // Refresh the equipment list
                    await fetchEquipment();
                } catch (error) {
                    console.error("Error deleting equipment:", error);
                    showToast("Failed to delete equipment", "danger");
                    throw error;
                }
            }

            // Initialize pagination
            function initializePagination() {
                updatePagination();
                
                // Previous page button
                document.getElementById("prevPage").addEventListener("click", function (e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        renderEquipment();
                    }
                });
                
                // Next page button
                document.getElementById("nextPage").addEventListener("click", function (e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderEquipment();
                    }
                });
            }

            // Update pagination controls
            function updatePagination() {
                paginationContainer.innerHTML = "";
                
                // Previous button
                const prevLi = document.createElement("li");
                prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
                prevLi.innerHTML = '<a class="page-link" href="#" id="prevPage">Previous</a>';
                paginationContainer.appendChild(prevLi);
                
                // Page numbers
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, startPage + 4);
                
                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement("li");
                    pageLi.className = `page-item ${i === currentPage ? "active" : ""}`;
                    pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    paginationContainer.appendChild(pageLi);
                    
                    // Add event listener to page number
                    pageLi.querySelector('a').addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        renderEquipment();
                    });
                }
                
                // Next button
                const nextLi = document.createElement("li");
                nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`;
                nextLi.innerHTML = '<a class="page-link" href="#" id="nextPage">Next</a>';
                paginationContainer.appendChild(nextLi);
                
                // Reattach event listeners to prev/next buttons
                document.getElementById("prevPage").addEventListener("click", function (e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        renderEquipment();
                    }
                });
                
                document.getElementById("nextPage").addEventListener("click", function (e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderEquipment();
                    }
                });
            }
            
            // Show toast notification
            function showToast(message, type = "info") {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();
                
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type} border-0`;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                toast.id = toastId;
                
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;
                
                toastContainer.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                // Remove toast from DOM after it hides
                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
            }

            // Start the application
            init();
        });
    </script>
</body>
</html>
// Global variables
let currentPage = 1;
const itemsPerPage = 6;
let allFacilities = [];
let facilityCategories = [];
let filteredItems = [];
let currentLayout = "grid";
let currentCatalogType = "facilities";
let selectedItems = []; // Declared globally

// DOM elements
const loadingIndicator = document.getElementById("loadingIndicator");
const catalogItemsContainer = document.getElementById("catalogItemsContainer");
const categoryFilterList = document.getElementById("categoryFilterList");
const pagination = document.getElementById("pagination");
const layoutDropdown = document.getElementById("layoutDropdown");
let facilityDetailModal;
const chooseCatalogDropdown = document.getElementById("chooseCatalogDropdown");

// Utility Functions
async function fetchData(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const response = await fetch(url, {
        ...options,
        credentials: "include",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
            "Content-Type": "application/json",
            ...(options.headers || {}),
        },
    });

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return await response.json();
}

function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${
        type === "success" ? "success" : "danger"
    } border-0 position-fixed bottom-0 end-0 m-3`;
    toast.style.zIndex = "1100";
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${
                    type === "success"
                        ? "bi-check-circle-fill"
                        : "bi-exclamation-circle-fill"
                } me-2"></i>
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

function showError(message) {
    showToast(message, "error");
}

async function getSelectedItems() {
    try {
        const response = await fetchData("/api/requisition/calculate-fees");
        return response.data?.selected_items || [];
    } catch (e) {
        console.error("Error getting selected items:", e);
        return [];
    }
}

async function updateCartBadge() {
    try {
        const badge = document.getElementById("requisitionBadge");
        if (selectedItems.length > 0) {
            badge.textContent = selectedItems.length;
            badge.classList.remove("d-none");
        } else {
            badge.classList.add("d-none");
        }
    } catch (error) {
        console.error("Error updating cart badge:", error);
    }
}

// Main function to refresh the entire UI after an action
async function updateAllUI() {
    try {
        // First update our local selectedItems state
        selectedItems = await getSelectedItems();

        // Then re-render everything
        filterAndRenderItems();
        updateCartBadge();

        // Also update any open modals
        const modal = document.getElementById("facilityDetailModal");
        if (modal && modal.classList.contains("show")) {
            const facilityId = document.querySelector(
                "#facilityDetailModal .add-remove-btn"
            )?.dataset.id;
            if (facilityId) showFacilityDetails(facilityId);
        }
    } catch (error) {
        console.error("Error updating UI:", error);
    }
}

// Form Action Functions
async function addToForm(id, type, quantity = 1) {
    try {
        const requestBody = {
            type: type,
            facility_id: parseInt(id),
            quantity: quantity
        };

        const response = await fetchData("/api/requisition/add-item", {
            method: "POST",
            body: JSON.stringify(requestBody),
        });

        if (!response.success) {
            throw new Error(response.message || "Failed to add item");
        }
        
        // Update selectedItems with the server response
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

async function removeFromForm(id, type) {
    try {
        const requestBody = {
            type: type,
            facility_id: parseInt(id)
        };

        const response = await fetchData("/api/requisition/remove-item", {
            method: "POST",
            body: JSON.stringify(requestBody),
        });

        if (!response.success) {
            throw new Error(response.message || "Failed to remove item");
        }

        // Update selectedItems with the server response
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

// Updated Button HTML Generators
function getFacilityButtonHtml(facility) {
    const isSelected = selectedItems.some(
        (item) =>
            parseInt(item.id) === facility.facility_id &&
            item.type === "facility"
    );

    if (isSelected) {
        return `
            <button class="btn btn-danger add-remove-btn" 
                    data-id="${facility.facility_id}" 
                    data-type="facility" 
                    data-action="remove">
                Remove from Form
            </button>
        `;
    } else {
        return `
            <button class="btn btn-primary add-remove-btn" 
                    data-id="${facility.facility_id}" 
                    data-type="facility" 
                    data-action="add">
                Add to Form
            </button>
        `;
    }
}

// Render Functions
function renderCategoryFilters() {
    categoryFilterList.innerHTML = "";

    // Add "All Categories" option
    const allCategoriesItem = document.createElement("div");
    allCategoriesItem.className = "category-item";
    allCategoriesItem.innerHTML = `
        <div class="form-check">
            <input class="form-check-input category-filter" type="checkbox" id="allCategories" value="All" checked disabled>
            <label class="form-check-label" for="allCategories">All Categories</label>
        </div>
    `;

    const allCategoriesCheckbox =
        allCategoriesItem.querySelector(".form-check-input");

    allCategoriesCheckbox.addEventListener("change", function () {
        if (this.checked) {
            document
                .querySelectorAll(".category-filter:not(#allCategories)")
                .forEach((input) => {
                    input.checked = false;
                    input.disabled = false;
                });
            filterAndRenderItems();
        }
    });
    categoryFilterList.appendChild(allCategoriesItem);

    // Render facility categories
    facilityCategories.forEach((category) => {
        const categoryItem = document.createElement("div");
        categoryItem.className = "category-item";

        categoryItem.innerHTML = `
            <div class="form-check d-flex justify-content-between align-items-center">
                <div>
                    <input class="form-check-input category-filter" type="checkbox" 
                           id="category${category.category_id}" value="${
            category.category_id
        }">
                    <label class="form-check-label" for="category${
                        category.category_id
                    }">${category.category_name}</label>
                </div>
                <i class="bi bi-chevron-down toggle-arrow"></i>
            </div>
            <div class="subcategory-list ms-3" style="overflow: hidden; max-height: 0; transition: max-height 0.3s ease;">
                ${category.subcategories
                    .map(
                        (sub) => `
                    <div class="form-check">
                        <input class="form-check-input subcategory-filter" type="checkbox" 
                               id="subcategory${sub.subcategory_id}" value="${sub.subcategory_id}">
                        <label class="form-check-label" for="subcategory${sub.subcategory_id}">${sub.subcategory_name}</label>
                    </div>
                `
                    )
                    .join("")}
            </div>
        `;

        const toggleArrow = categoryItem.querySelector(".toggle-arrow");
        const subcategoryList =
            categoryItem.querySelector(".subcategory-list");
        const categoryCheckbox =
            categoryItem.querySelector(".category-filter");

        toggleArrow.addEventListener("click", function () {
            const isExpanded = subcategoryList.style.maxHeight !== "0px";
            if (isExpanded) {
                subcategoryList.style.maxHeight = "0";
            } else {
                subcategoryList.style.maxHeight = `${subcategoryList.scrollHeight}px`;
            }
            toggleArrow.classList.toggle("bi-chevron-up");
            toggleArrow.classList.toggle("bi-chevron-down");
        });

        subcategoryList.addEventListener("change", function (e) {
            if (e.target.classList.contains("subcategory-filter")) {
                allCategoriesCheckbox.checked = false;
                allCategoriesCheckbox.disabled = false;

                const anySubChecked =
                    Array.from(
                        subcategoryList.querySelectorAll(
                            ".subcategory-filter:checked"
                        )
                    ).length > 0;
                categoryCheckbox.checked = anySubChecked;

                filterAndRenderItems();
            }
        });

        categoryCheckbox.addEventListener("change", function () {
            allCategoriesCheckbox.checked = false;
            allCategoriesCheckbox.disabled = false;

            if (!this.checked) {
                subcategoryList
                    .querySelectorAll(".subcategory-filter")
                    .forEach((subCheckbox) => {
                        subCheckbox.checked = false;
                    });
            }
            filterAndRenderItems();
        });

        categoryFilterList.appendChild(categoryItem);
    });
}

function filterItems() {
    const allCategoriesCheckbox = document.getElementById("allCategories");

    filteredItems = [...allFacilities];

    if (allCategoriesCheckbox.checked) {
        return filteredItems;
    }

    const selectedCategories = Array.from(
        document.querySelectorAll(".category-filter:checked")
    ).map((input) => input.value);
    const selectedSubcategories = Array.from(
        document.querySelectorAll(".subcategory-filter:checked")
    ).map((input) => input.value);

    if (selectedCategories.length > 0) {
        filteredItems = filteredItems.filter((facility) =>
            selectedCategories.includes(
                facility.category.category_id.toString()
            )
        );
    }

    if (selectedSubcategories.length > 0) {
        filteredItems = filteredItems.filter((facility) =>
            selectedSubcategories.includes(
                facility.subcategory?.subcategory_id.toString()
            )
        );
    }

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

function getButtonHtml(item, type) {
    const id = item.facility_id;
    const isSelected = selectedItems.some(
        (selectedItem) =>
            parseInt(selectedItem.id) === id && selectedItem.type === type
    );

    if (isSelected) {
        return `<button class="btn btn-danger add-remove-btn" data-id="${id}" data-type="${type}" data-action="remove">Remove from Form</button>`;
    } else {
        return `<button class="btn btn-primary add-remove-btn" data-id="${id}" data-type="${type}" data-action="add">Add to Form</button>`;
    }
}

function renderFacilitiesGrid(facilities) {
    catalogItemsContainer.innerHTML = facilities
        .map((facility) => {
            const primaryImage =
                facility.images?.find((img) => img.image_type === "Primary")
                    ?.image_url || "https://via.placeholder.com/300x200";

            return `
            <div class="catalog-card">
                <img src="${primaryImage}" alt="${
                facility.facility_name
            }" class="catalog-card-img">
                <div class="catalog-card-details">
                    <h5 data-id="${facility.facility_id}">${
                facility.facility_name
            }</h5>
                    <span class="status-banner" style="background-color: ${
                        facility.status.color_code
                    };">
                        ${facility.status.status_name}
                    </span>
                    <div class="catalog-card-meta">
                        <span><i class="bi bi-people-fill"></i> ${
                            facility.capacity || "N/A"
                        }</span>
                        <span><i class="bi bi-tags-fill"></i> ${
                            facility.subcategory?.subcategory_name ||
                            facility.category.category_name
                        }</span>
                    </div>
                    <p class="facility-description">${
                        facility.description?.substring(0, 100) ||
                        "No description available."
                    }${facility.description?.length > 100 ? "..." : ""}</p>
                    <div class="catalog-card-fee">
                        <i class="bi bi-cash-stack"></i> ₱${parseFloat(
                            facility.external_fee
                        ).toLocaleString()} (${facility.rate_type})
                    </div>
                </div>
                <div class="catalog-card-actions">
                    ${getButtonHtml(facility, "facility")}
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
                <img src="${primaryImage}" alt="${
                facility.facility_name
            }" class="catalog-card-img">
                <div class="catalog-card-details">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 data-id="${facility.facility_id}">${
                facility.facility_name
            }</h5>
                        <span class="status-banner" style="background-color: ${
                            facility.status.color_code
                        };">
                            ${facility.status.status_name}
                        </span>
                    </div>
                    <div class="catalog-card-meta">
                        <span><i class="bi bi-people-fill"></i> ${
                            facility.capacity || "N/A"
                        }</span>
                        <span><i class="bi bi-tags-fill"></i> ${
                            facility.subcategory?.subcategory_name ||
                            facility.category.category_name
                        }</span>
                    </div>
                    <p class="facility-description">${
                        facility.description || "No description available."
                    }</p>
                    <div class="catalog-card-fee">
                        <i class="bi bi-cash-stack"></i> ₱${parseFloat(
                            facility.external_fee
                        ).toLocaleString()} (${facility.rate_type})
                    </div>
                </div>
                <div class="catalog-card-actions">
                    ${getButtonHtml(facility, "facility")}
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
        document
            .querySelectorAll(".layout-option")
            .forEach((opt) => opt.classList.remove("active"));
        option.classList.add("active");
        filterAndRenderItems();
    });
});

// Initialize the application when DOM is loaded
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
});

async function showFacilityDetails(facilityId) {
    try {
        const facility = allFacilities.find((f) => f.facility_id == facilityId);
        if (!facility) return;

        const primaryImage =
            facility.images?.find((img) => img.image_type === "Primary")
                ?.image_url || "https://via.placeholder.com/800x400";

        const isSelected = selectedItems.some(
            (selectedItem) =>
                parseInt(selectedItem.id) === facility.facility_id &&
                selectedItem.type === "facility"
        );

        document.getElementById("facilityDetailModalLabel").textContent =
            facility.facility_name;
        document.getElementById("facilityDetailContent").innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${primaryImage}" alt="${
            facility.facility_name
        }" class="facility-image img-fluid">
                </div>
                <div class="col-md-6">
                    <div class="facility-details">
                        <p><strong>Status:</strong> <span class="badge" style="background-color: ${
                            facility.status.color_code
                        }">${facility.status.status_name}</span></p>
                        <p><strong>Category:</strong> ${
                            facility.category.category_name
                        }</p>
                        <p><strong>Subcategory:</strong> ${
                            facility.subcategory?.subcategory_name || "N/A"
                        }</p>
                        <p><strong>Capacity:</strong> ${facility.capacity}</p>
                        <p><strong>Rate:</strong> ₱${parseFloat(
                            facility.external_fee
                        ).toLocaleString()} (${facility.rate_type})</p>
                        <p><strong>Description:</strong></p>
                        <p>${
                            facility.description || "No description available."
                        }</p>
                    </div>
                    <div class="mt-3">
                       <button class="btn ${
                           isSelected ? "btn-danger" : "btn-primary"
                       } add-remove-btn" 
                               data-id="${facility.facility_id}" 
                               data-type="facility" 
                               data-action="${isSelected ? "remove" : "add"}">
                           ${isSelected ? "Remove from Form" : "Add to Form"}
                       </button>
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
        const [
            facilitiesData,
            facilityCategoriesData,
        ] = await Promise.all([
            fetchData("/api/facilities"),
            fetchData("/api/facility-categories/index"),
        ]);

        // Fetch selected items separately and wait for it
        selectedItems = await getSelectedItems();

        allFacilities = facilitiesData.data || [];
        facilityCategories = facilityCategoriesData || [];

        renderCategoryFilters();
        filterAndRenderItems();
        setupEventListeners();
        updateCartBadge();
        // The core fix: remove the d-none class from the container to make it visible
        catalogItemsContainer.classList.remove("d-none");
    } catch (error) {
        console.error("Error initializing page:", error);
        showError("Failed to initialize the page. Please try again.");
    } finally {
        loadingIndicator.style.display = "none";
    }
}
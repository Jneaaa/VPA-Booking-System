// Global variables
let currentPage = 1;
const itemsPerPage = 6;
let allEquipment = [];
let equipmentCategories = [];
let filteredItems = [];
let currentLayout = "grid";
let selectedItems = []; // Declared globally

// DOM elements
const loadingIndicator = document.getElementById("loadingIndicator");
const catalogItemsContainer = document.getElementById("catalogItemsContainer");
const categoryFilterList = document.getElementById("categoryFilterList");
const pagination = document.getElementById("pagination");
const layoutDropdown = document.getElementById("layoutDropdown");

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
    } catch (error) {
        console.error("Error updating UI:", error);
    }
}

// Form Action Functions
async function addToForm(id, type, quantity = 1) {
    try {
        const requestBody = {
            type: type,
            equipment_id: parseInt(id),
            quantity: parseInt(quantity)
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
            equipment_id: parseInt(id)
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

function getEquipmentButtonHtml(equipment) {
    const isSelected = selectedItems.some(
        (item) =>
            parseInt(item.id) === equipment.equipment_id &&
            item.type === "equipment"
    );
    const selectedItem = isSelected
        ? selectedItems.find(
              (item) => parseInt(item.id) === equipment.equipment_id
          )
        : null;
    const currentQty = selectedItem ? selectedItem.quantity : 1;
    const maxQty = equipment.total_quantity || 1;

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

    // Render equipment categories
    equipmentCategories.forEach((category) => {
        const categoryItem = document.createElement("div");
        categoryItem.className = "category-item";

        categoryItem.innerHTML = `
            <div class="form-check">
                <input class="form-check-input category-filter" type="checkbox" 
                       id="category${category.category_id}" value="${category.category_id}">
                <label class="form-check-label" for="category${category.category_id}">${category.category_name}</label>
            </div>
        `;

        const categoryCheckbox =
            categoryItem.querySelector(".form-check-input");

        categoryCheckbox.addEventListener("change", function () {
            allCategoriesCheckbox.checked = false;
            allCategoriesCheckbox.disabled = false;
            filterAndRenderItems();
        });

        categoryFilterList.appendChild(categoryItem);
    });
}

function filterItems() {
    const allCategoriesCheckbox = document.getElementById("allCategories");

    filteredItems = [...allEquipment];

    if (allCategoriesCheckbox.checked) {
        return filteredItems;
    }

    const selectedCategories = Array.from(
        document.querySelectorAll(".category-filter:checked")
    ).map((input) => input.value);

    if (selectedCategories.length > 0) {
        filteredItems = filteredItems.filter((equipment) =>
            selectedCategories.includes(
                equipment.category.category_id.toString()
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
                <i class="bi bi-tools fs-1 text-muted"></i>
                <h4>No equipment found</h4>
            </div>
        `;
        return;
    }

    catalogItemsContainer.classList.remove("grid-layout", "list-layout");
    catalogItemsContainer.classList.add(`${currentLayout}-layout`);

    currentLayout === "grid"
        ? renderEquipmentGrid(paginatedItems)
        : renderEquipmentList(paginatedItems);
}

function renderEquipmentGrid(equipment) {
    catalogItemsContainer.innerHTML = equipment
        .map((item) => {
            const primaryImage =
                item.images?.find((img) => img.image_type === "Primary")
                    ?.image_url || "https://via.placeholder.com/300x200";
            const availableItems = item.available_quantity || 0;
            const totalItems = item.total_quantity || 0;

            return `
            <div class="catalog-card">
                <img src="${primaryImage}" alt="${
                item.equipment_name
            }" class="catalog-card-img">
                <div class="catalog-card-details">
                    <h5 data-id="${item.equipment_id}">${
                item.equipment_name
            }</h5>
                    <span class="status-banner" style="background-color: ${
                        item.status.color_code
                    };">
                        ${item.status.status_name}
                    </span>
                    <div class="catalog-card-meta">
                        <span><i class="bi bi-tags-fill"></i> ${
                            item.category.category_name
                        }</span>
                        <span><i class="bi bi-box-seam"></i> ${availableItems}/${totalItems} available</span>
                    </div>
                    <p class="facility-description">${
                        item.description?.substring(0, 100) ||
                        "No description available."
                    }${item.description?.length > 100 ? "..." : ""}</p>
                    <div class="catalog-card-fee">
                        <i class="bi bi-cash-stack"></i> ₱${parseFloat(
                            item.external_fee
                        ).toLocaleString()} (${item.rate_type})
                    </div>
                </div>
                <div class="catalog-card-actions">
                    ${getEquipmentButtonHtml(item)}
                    <button class="btn btn-outline-secondary">View Details</button>
                </div>
            </div>
        `;
        })
        .join("");
}

function renderEquipmentList(equipment) {
    catalogItemsContainer.innerHTML = equipment
        .map((item) => {
            const primaryImage =
                item.images?.find((img) => img.image_type === "Primary")
                    ?.image_url || "https://via.placeholder.com/300x200";
            const availableItems = item.available_quantity || 0;
            const totalItems = item.total_quantity || 0;

            return `
            <div class="catalog-card">
                <img src="${primaryImage}" alt="${
                item.equipment_name
            }" class="catalog-card-img">
                <div class="catalog-card-details">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 data-id="${item.equipment_id}">${
                item.equipment_name
            }</h5>
                        <span class="status-banner" style="background-color: ${
                            item.status.color_code
                        };">
                            ${item.status.status_name}
                        </span>
                    </div>
                    <div class="catalog-card-meta">
                        <span><i class="bi bi-tags-fill"></i> ${
                            item.category.category_name
                        }</span>
                        <span><i class="bi bi-box-seam"></i> ${availableItems}/${totalItems} available</span>
                    </div>
                    <p class="facility-description">${
                        item.description || "No description available."
                    }</p>
                    <div class="catalog-card-fee">
                        <i class="bi bi-cash-stack"></i> ₱${parseFloat(
                            item.external_fee
                        ).toLocaleString()} (${item.rate_type})
                    </div>
                </div>
                <div class="catalog-card-actions">
                    ${getEquipmentButtonHtml(item)}
                    <button class="btn btn-outline-secondary">View Details</button>
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
    
    // Add quantity change handler
    catalogItemsContainer.addEventListener('change', async (e) => {
        if (e.target.classList.contains('quantity-input')) {
            const card = e.target.closest('.catalog-card');
            const button = card.querySelector('.add-remove-btn');
            const id = button.dataset.id;
            const type = button.dataset.type;
            const action = button.dataset.action;
            const quantity = parseInt(e.target.value) || 1;
            
            if (action === 'remove') {
                // If item is already in cart, remove and re-add with new quantity
                await removeFromForm(id, type);
                await addToForm(id, type, quantity);
            }
        }
    });
}

// Quantity input validation for equipment
catalogItemsContainer.addEventListener("change", (e) => {
    if (e.target.classList.contains("quantity-input")) {
        const input = e.target;
        const max = parseInt(input.max) || 1;
        const value = parseInt(input.value) || 1;

        if (value < 1) input.value = 1;
        if (value > max) input.value = max;
    }
});

// Category filters
document.addEventListener("change", function (e) {
    if (e.target.classList.contains("category-filter")) {
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
    init();
});

// Main Initialization
async function init() {
    try {
        const [
            equipmentData,
            equipmentCategoriesData,
        ] = await Promise.all([
            fetchData("/api/equipment"),
            fetchData("/api/equipment-categories"),
        ]);

        // Fetch selected items separately and wait for it
        selectedItems = await getSelectedItems();

        allEquipment = equipmentData.data || [];
        equipmentCategories = equipmentCategoriesData || [];

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
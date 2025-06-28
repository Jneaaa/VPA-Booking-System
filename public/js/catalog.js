document.addEventListener("DOMContentLoaded", function () {

  // Toast notification function
  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Toggle item in requisition form
async function toggleItemInForm(type, id, button) {
    try {
        const isAdded = button.textContent.trim() === 'Remove';
        
        const response = await fetch(`/api/${isAdded ? 'remove-from' : 'add-to'}-form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                [`${type}_id`]: id,
                type: type
            }),
        });

        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        
        if (result.success) {
            button.textContent = isAdded ? 'Add to Form' : 'Remove';
            button.classList.toggle('btn-primary', !isAdded);
            button.classList.toggle('btn-outline-danger', isAdded);
            
            showToast(isAdded ? 'Removed successfully' : 'Added successfully');
            updateRequisitionButton();
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    }
}

// Update the renderCatalogItems function
function renderCatalogItems() {
    catalogItemsContainer.innerHTML = "";
    catalogItemsContainer.className = currentLayout + "-layout";

    const filteredItems = currentCategory === "All" 
        ? currentItems 
        : currentItems.filter(item => 
            item.category?.category_name === currentCategory || 
            item.subcategory?.subcategory_name === currentCategory
        );

    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedItems = filteredItems.slice(startIndex, startIndex + itemsPerPage);

    const itemsWrapper = document.createElement("div");
    itemsWrapper.className = "catalog-items-wrapper";
    catalogItemsContainer.appendChild(itemsWrapper);

    paginatedItems.forEach(item => {
        const isFacility = 'facility_name' in item;
        const type = isFacility ? 'facility' : 'equipment';
        const isInForm = selectedItems.some(selected => selected.id === item[`${type}_id`] && selected.type === type);
        
        const card = document.createElement("div");
        card.className = "catalog-card";
        
        card.innerHTML = `
            <img src="${item.images[0]?.image_url || 'images/default-image.jpg'}" 
                 class="catalog-card-img" alt="${item[`${type}_name`]}">
            <div class="catalog-card-details">
                <h5>${item[`${type}_name`]}</h5>
                <p>${item.description || "No description available."}</p>
                <div class="catalog-card-meta">
                    ${isFacility ? `
                    <div><i class="bi bi-tag"></i> ${item.category?.category_name || "Uncategorized"}</div>
                    <div><i class="bi bi-tags"></i> ${item.subcategory?.subcategory_name || "No Subcategory"}</div>
                    <div><i class="bi bi-building"></i> ${item.department?.department_name || "Unknown Department"}</div>
                    <div><i class="bi bi-people"></i> Capacity: ${item.capacity}</div>
                    ` : `
                    <div><i class="bi bi-tag"></i> ${item.category?.category_name || "Uncategorized"}</div>
                    <div><i class="bi bi-currency-exchange"></i> ${item.rateType?.type_name || "Standard"}</div>
                    <div><i class="bi bi-box"></i> Available: ${item.available_quantity}</div>
                    `}
                </div>
                ${item.status ? `
                <span class="badge ${item.status.color_code ? '' : 'bg-' + (item.status.status_name === 'Available' ? 'success' : 'danger')}" 
                      style="${item.status.color_code ? 'background-color:' + item.status.color_code : ''}">
                    ${item.status.status_name}
                </span>
                ` : ''}
            </div>
            <div class="catalog-card-actions">
                <div class="${type}-price">₱${(isFacility ? item.rental_fee : item.company_fee).toFixed(2)}</div>
                <button class="btn ${isInForm ? 'btn-outline-danger' : 'btn-primary'} btn-sm toggle-form-btn" 
                        data-${type}-id="${item[`${type}_id`]}" data-type="${type}">
                    ${isInForm ? 'Remove' : 'Add to Form'}
                </button>
                <button class="btn btn-outline-secondary btn-sm">View Calendar</button>
            </div>
        `;

        itemsWrapper.appendChild(card);
    });

    // Attach event listeners to all toggle buttons
    document.querySelectorAll('.toggle-form-btn').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset[`${type}Id`];
            toggleItemInForm(type, id, this);
        });
    });

    renderPagination(filteredItems.length);
}

// Update the updateRequisitionButton function
function updateRequisitionButton() {
    const selectedItems = JSON.parse(localStorage.getItem('selectedItems') || '[]'); // Fixed missing closing quote and default value
    const totalCount = selectedItems.length;

    if (totalCount > 0) {
        requisitionBadge.textContent = totalCount;
        requisitionBadge.classList.remove('d-none');
        requisitionFormButton.classList.remove('btn-outline-primary');
        requisitionFormButton.classList.add('btn-primary');
    } else {
        requisitionBadge.classList.add('d-none');
        requisitionFormButton.classList.remove('btn-primary');
        requisitionFormButton.classList.add('btn-outline-primary');
    }
}

// Add this to your CSS
const style = document.createElement('style');
style.textContent = `
    .toast {
        transition: opacity 0.3s ease;
    }
    .badge {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
`;
document.head.appendChild(style);

  const categoryFilterList =
    document.getElementById("categoryFilterList");
  const catalogItemsContainer = document.getElementById(
    "catalogItemsContainer"
  );
  const currentCategoryTitle = document.getElementById(
    "currentCategoryTitle"
  );
  const catalogHeroTitle = document.getElementById("catalogHeroTitle");
  const loadingIndicator = document.getElementById("loadingIndicator");
  const pagination = document.getElementById("pagination");

  let equipment = []; // Store fetched equipment
  let currentCategory = "All";
  let currentLayout = "list"; // Default layout
  let currentPage = 1;
  const itemsPerPage = 4; // Adjust as needed

  // Fetch equipment from the API
  async function fetchEquipment() {
    try {
      loadingIndicator.classList.remove("d-none");
      catalogItemsContainer.classList.add("d-none");

      const response = await fetch(
        "http://127.0.0.1:8000/api/equipment",
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          credentials: "same-origin", // or "include" if you need to send cookies
        }
      );

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      // Check if data structure is as expected
      if (!data.data) {
        throw new Error("Invalid data structure from API");
      }

      equipment = data.data;

      renderCategoryFilterList();
      renderCatalogItems();

      loadingIndicator.classList.add("d-none");
      catalogItemsContainer.classList.remove("d-none");
    } catch (error) {
      console.error("Error fetching equipment:", error);
      loadingIndicator.innerHTML = `
<div class="alert alert-danger">
  Failed to load equipment. Please try again later.
</div>
`;
      // Don't automatically retry - let the user decide to retry
    }
  }

  // Render category filter list
  function renderCategoryFilterList() {
    const categories = [
      "All",
      ...new Set(
        equipment.map((item) => item.category.category_name)
      ),
    ];
    categoryFilterList.innerHTML = "";
    categories.forEach((category) => {
      const listItem = document.createElement("a");
      listItem.href = "#";
      listItem.classList.add("filter-item");
      listItem.dataset.category = category;
      listItem.textContent = category;
      if (category === "All") listItem.classList.add("active");
      categoryFilterList.appendChild(listItem);
    });
    attachCategoryFilterListeners();
  }

  // Render catalog items
  function renderCatalogItems() {
    catalogItemsContainer.innerHTML = "";
    catalogItemsContainer.className = currentLayout + "-layout"; // Apply current layout class

    const filteredEquipment =
      currentCategory === "All"
        ? equipment
        : equipment.filter(
            (item) =>
              item.category.category_name === currentCategory
          );

    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedItems = filteredEquipment.slice(
      startIndex,
      startIndex + itemsPerPage
    );

    const itemsWrapper = document.createElement("div");
    itemsWrapper.className = "catalog-items-wrapper";
    catalogItemsContainer.appendChild(itemsWrapper);

    paginatedItems.forEach((item) => {
      const rentalFee =
        typeof item.rental_fee === "number"
          ? item.rental_fee.toFixed(2)
          : "0.00";
      const card = document.createElement("div");
      card.classList.add("catalog-card");

      if (currentLayout === "list") {
        card.innerHTML = `
          <img src="${
            item.images.length > 0
              ? item.images[0].image_url
              : "https://via.placeholder.com/180x120"
          }" class="catalog-card-img" alt="${item.equipment_name}">
          <div class="catalog-card-details">
              <h5>${item.equipment_name}</h5>
              <p>${item.description || "No description available."}</p>
              <div class="catalog-card-meta">
                  <div><i class="bi bi-tag"></i> ${
                    item.category.category_name
                  }</div>
                  <div><i class="bi bi-building"></i> ${
                    item.department.department_name
                  }</div>
                  <div><i class="bi bi-people"></i> Quantity: ${
                    item.quantity
                  }</div>
              </div>
          </div>
          <div class="catalog-card-actions">
              <div class="rental-fee">₱${rentalFee}</div>
              <button class="btn btn-sm btn-primary">Add to Form</button>
              <button class="btn btn-sm btn-outline-secondary">View Calendar</button>
          </div>
        `;
      } else {
        card.innerHTML = `
          <img src="${
            item.images.length > 0
              ? item.images[0].image_url
              : "https://via.placeholder.com/180x120"
          }" class="catalog-card-img" alt="${item.equipment_name}">
          <div class="catalog-card-details">
              <h5>${item.equipment_name}</h5>
              <div class="catalog-card-meta">
                  <div><i class="bi bi-tag"></i> ${
                    item.category.category_name
                  }</div>
                  <div><i class="bi bi-cash"></i> ₱${rentalFee}</div>
              </div>
              <button class="btn btn-sm btn-primary mt-2">Add to Form</button>
          </div>
        `;
      }

      itemsWrapper.appendChild(card);
    });

    renderPagination(filteredEquipment.length);
  }

  // Render pagination
  function renderPagination(totalItems) {
    pagination.innerHTML = "";

    const totalPages = Math.ceil(totalItems / itemsPerPage);

    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement("li");
      li.className = "page-item" + (i === currentPage ? " active" : "");
      li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
      li.addEventListener("click", function (e) {
        e.preventDefault();
        currentPage = i;
        renderCatalogItems();
      });
      pagination.appendChild(li);
    }
  }

  // Attach category filter listeners
  function attachCategoryFilterListeners() {
    const filterItems = document.querySelectorAll(
      "#categoryFilterList .filter-item"
    );
    filterItems.forEach((item) => {
      item.addEventListener("click", function (e) {
        e.preventDefault();
        filterItems.forEach((li) => li.classList.remove("active"));
        this.classList.add("active");
        currentCategory = this.dataset.category;
        currentCategoryTitle.textContent = `${currentCategory} Equipment`;
        currentPage = 1; // Reset to first page
        renderCatalogItems();
      });
    });
  }

  // Setup layout toggle
  function setupLayoutToggle() {
    const layoutDropdown = document.getElementById("layoutDropdown");
    const layoutOptions = document.querySelectorAll(".layout-option");

    layoutOptions.forEach((option) => {
      option.addEventListener("click", function (e) {
        e.preventDefault();
        currentLayout = this.dataset.layout;
        layoutDropdown.textContent = `${this.textContent} Layout`;
        renderCatalogItems();
      });
    });
  }

  // Initial fetch and render
  fetchEquipment();
  setupLayoutToggle();
});

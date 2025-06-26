document.addEventListener("DOMContentLoaded", function () {
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

  let equipment = []; // Store fetched equipment
  let currentCategory = "All";
  let currentLayout = "list"; // Default layout

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

    const itemsWrapper = document.createElement("div");
    itemsWrapper.className = "catalog-items-wrapper";
    catalogItemsContainer.appendChild(itemsWrapper);

    filteredEquipment.forEach((item) => {
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

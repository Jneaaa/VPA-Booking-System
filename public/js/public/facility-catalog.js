document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentPage = 1;
    const itemsPerPage = 6;
    let allFacilities = [];
    let allEquipment = [];
    let facilityCategories = [];
    let equipmentCategories = [];
    let filteredItems = [];
    let currentLayout = 'grid';
    let currentCatalogType = 'facilities'; // 'facilities' or 'equipment'

    // DOM elements
    const loadingIndicator = document.getElementById('loadingIndicator');
    const catalogItemsContainer = document.getElementById('catalogItemsContainer');
    const categoryFilterList = document.getElementById('categoryFilterList');
    const pagination = document.getElementById('pagination');
    const layoutDropdown = document.getElementById('layoutDropdown');
    const facilityDetailModal = new bootstrap.Modal(document.getElementById('facilityDetailModal'));
    const chooseCatalogDropdown = document.getElementById('chooseCatalogDropdown');

    // Initialize the page
    init();

    async function init() {
        try {
            const [facilitiesData, equipmentData, facilityCategoriesData, equipmentCategoriesData] = await Promise.all([
                fetchData('http://127.0.0.1:8000/api/facilities'),
                fetchData('http://127.0.0.1:8000/api/equipment'),
                fetchData('http://127.0.0.1:8000/api/facility-categories/index'),
                fetchData('http://127.0.0.1:8000/api/equipment-categories') // New fetch for equipment categories
            ]);

            allFacilities = facilitiesData.data || [];
            allEquipment = equipmentData.data || [];
            facilityCategories = facilityCategoriesData || [];
            equipmentCategories = equipmentCategoriesData || [];

            renderCategoryFilters();
            filterAndRenderItems();
            setupEventListeners();
            updateCartBadge();
        } catch (error) {
            console.error('Error initializing page:', error);
            showError('Failed to load data. Please try again later.');
        } finally {
            loadingIndicator.classList.add('d-none');
            catalogItemsContainer.classList.remove('d-none');
        }
    }

    async function fetchData(url) {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    }

    function renderCategoryFilters() {
        categoryFilterList.innerHTML = '';

        // Add "All Categories" option
        const allCategoriesItem = document.createElement('div');
        allCategoriesItem.className = 'category-item';
        allCategoriesItem.innerHTML = `
            <div class="form-check">
                <input class="form-check-input category-filter" type="checkbox" id="allCategories" value="All" checked disabled>
                <label class="form-check-label" for="allCategories">All Categories</label>
            </div>
        `;
        
        const allCategoriesCheckbox = allCategoriesItem.querySelector('.form-check-input');
        
        allCategoriesCheckbox.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.category-filter:not(#allCategories)').forEach(input => {
                    input.checked = false;
                    input.disabled = false;
                });
                filterAndRenderItems();
            }
        });
        categoryFilterList.appendChild(allCategoriesItem);

        // Render categories based on the current catalog type
        if (currentCatalogType === 'facilities') {
            facilityCategories.forEach(category => {
                const categoryItem = document.createElement('div');
                categoryItem.className = 'category-item';

                categoryItem.innerHTML = `
                    <div class="form-check d-flex justify-content-between align-items-center">
                        <div>
                            <input class="form-check-input category-filter" type="checkbox" 
                                   id="category${category.category_id}" value="${category.category_id}">
                            <label class="form-check-label" for="category${category.category_id}">${category.category_name}</label>
                        </div>
                        <i class="bi bi-chevron-down toggle-arrow"></i>
                    </div>
                    <div class="subcategory-list ms-3" style="overflow: hidden; max-height: 0; transition: max-height 0.3s ease;">
                        ${category.subcategories.map(sub => `
                            <div class="form-check">
                                <input class="form-check-input subcategory-filter" type="checkbox" 
                                       id="subcategory${sub.subcategory_id}" value="${sub.subcategory_id}">
                                <label class="form-check-label" for="subcategory${sub.subcategory_id}">${sub.subcategory_name}</label>
                            </div>
                        `).join('')}
                    </div>
                `;

                const toggleArrow = categoryItem.querySelector('.toggle-arrow');
                const subcategoryList = categoryItem.querySelector('.subcategory-list');
                const categoryCheckbox = categoryItem.querySelector('.category-filter');

                toggleArrow.addEventListener('click', function() {
                    const isExpanded = subcategoryList.style.maxHeight !== '0px';
                    if (isExpanded) {
                        subcategoryList.style.maxHeight = '0';
                    } else {
                        subcategoryList.style.maxHeight = `${subcategoryList.scrollHeight}px`;
                    }
                    toggleArrow.classList.toggle('bi-chevron-up');
                    toggleArrow.classList.toggle('bi-chevron-down');
                });

                subcategoryList.addEventListener('change', function(e) {
                    if (e.target.classList.contains('subcategory-filter')) {
                        allCategoriesCheckbox.checked = false;
                        allCategoriesCheckbox.disabled = false;
                        
                        const anySubChecked = Array.from(subcategoryList.querySelectorAll('.subcategory-filter:checked')).length > 0;
                        categoryCheckbox.checked = anySubChecked;
                        
                        filterAndRenderItems();
                    }
                });

                categoryCheckbox.addEventListener('change', function() {
                    allCategoriesCheckbox.checked = false;
                    allCategoriesCheckbox.disabled = false;
                    
                    if (!this.checked) {
                        subcategoryList.querySelectorAll('.subcategory-filter').forEach(subCheckbox => {
                            subCheckbox.checked = false;
                        });
                    }
                    filterAndRenderItems();
                });

                categoryFilterList.appendChild(categoryItem);
            });
        } else if (currentCatalogType === 'equipment') {
            equipmentCategories.forEach(category => {
                const categoryItem = document.createElement('div');
                categoryItem.className = 'category-item';

                categoryItem.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input category-filter" type="checkbox" 
                               id="category${category.category_id}" value="${category.category_id}">
                        <label class="form-check-label" for="category${category.category_id}">${category.category_name}</label>
                    </div>
                `;

                const categoryCheckbox = categoryItem.querySelector('.category-filter');

                categoryCheckbox.addEventListener('change', function() {
                    allCategoriesCheckbox.checked = false;
                    allCategoriesCheckbox.disabled = false;
                    filterAndRenderItems();
                });

                categoryFilterList.appendChild(categoryItem);
            });
        }
    }

    function filterItems() {
        if (currentCatalogType === 'facilities') {
            filteredItems = [...allFacilities];

            const allCategoriesCheckbox = document.getElementById('allCategories');
            if (allCategoriesCheckbox.checked) {
                return filteredItems;
            }

            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(input => input.value);
            const selectedSubcategories = Array.from(document.querySelectorAll('.subcategory-filter:checked')).map(input => input.value);

            if (selectedCategories.length > 0) {
                filteredItems = filteredItems.filter(facility => 
                    selectedCategories.includes(facility.category.category_id.toString())
                );
            }

            if (selectedSubcategories.length > 0) {
                filteredItems = filteredItems.filter(facility => 
                    selectedSubcategories.includes(facility.subcategory?.subcategory_id.toString())
                );
            }
        } else {
            filteredItems = [...allEquipment];

            const allCategoriesCheckbox = document.getElementById('allCategories');
            if (allCategoriesCheckbox.checked) {
                return filteredItems;
            }

            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(input => input.value);

            if (selectedCategories.length > 0) {
                filteredItems = filteredItems.filter(equipment => 
                    selectedCategories.includes(equipment.category.category_id.toString())
                );
            }
        }

        return filteredItems;
    }

    function filterAndRenderItems() {
        const filtered = filterItems();
        renderItems(filtered);
        renderPagination(filtered.length);
    }

    function renderItems(items) {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedItems = items.slice(startIndex, startIndex + itemsPerPage);

        catalogItemsContainer.innerHTML = '';

        if (paginatedItems.length === 0) {
            catalogItemsContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-${currentCatalogType === 'facilities' ? 'building' : 'tools'} fs-1 text-muted"></i>
                    <h4>No ${currentCatalogType} found</h4>
                </div>
            `;
            return;
        }

        if (currentLayout === 'grid') {
            currentCatalogType === 'facilities' ? renderFacilitiesGrid(paginatedItems) : renderEquipmentGrid(paginatedItems);
        } else {
            currentCatalogType === 'facilities' ? renderFacilitiesList(paginatedItems) : renderEquipmentList(paginatedItems);
        }

        // Add event listeners to item name links
        document.querySelectorAll('.catalog-card-details h5').forEach(title => {
            title.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (currentCatalogType === 'facilities') {
                    showFacilityDetails(id);
                } else {
                    // Future: Show equipment details if needed
                }
            });
        });

        // Add event listeners to "Add to Form" buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const type = currentCatalogType === 'facilities' ? 'facility' : 'equipment';
                addToCart(id, type);
            });
        });
    }

    function renderFacilitiesGrid(facilities) {
        catalogItemsContainer.classList.remove('list-layout');
        catalogItemsContainer.classList.add('grid-layout');

        catalogItemsContainer.innerHTML = facilities.map(facility => {
            const primaryImage = facility.images?.find(img => img.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/300x200';

            return `
                <div class="catalog-card">
                    <img src="${primaryImage}" alt="${facility.facility_name}" class="catalog-card-img">
                    <div class="catalog-card-details">
                        <h5 data-id="${facility.facility_id}">${facility.facility_name}</h5>
                        <span class="status-banner" style="background-color: ${facility.status.color_code};">
                            ${facility.status.status_name}
                        </span>
                        <div class="catalog-card-meta">
                            <span><i class="bi bi-people-fill"></i> ${facility.capacity || 'N/A'}</span>
                            <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || facility.category.category_name}</span>
                        </div>
                        <p class="facility-description">${facility.description?.substring(0, 100) || 'No description available.'}${facility.description?.length > 100 ? '...' : ''}</p>
                        <div class="catalog-card-fee">
                            <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
                        </div>
                    </div>
                    <div class="catalog-card-actions">
                        <button class="btn btn-primary add-to-cart-btn" data-id="${facility.facility_id}">Add to Form</button>
                        <button class="btn btn-outline-secondary">View Calendar</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderEquipmentGrid(equipment) {
        catalogItemsContainer.classList.remove('list-layout');
        catalogItemsContainer.classList.add('grid-layout');

        catalogItemsContainer.innerHTML = equipment.map(item => {
            const primaryImage = item.images?.find(img => img.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/300x200';
            const availableItems = item.available_quantity || 0;
            const totalItems = item.total_quantity || 0;

            return `
                <div class="catalog-card">
                    <img src="${primaryImage}" alt="${item.equipment_name}" class="catalog-card-img">
                    <div class="catalog-card-details">
                        <h5 data-id="${item.equipment_id}">${item.equipment_name}</h5>
                        <span class="status-banner" style="background-color: ${item.status.color_code};">
                            ${item.status.status_name}
                        </span>
                        <div class="catalog-card-meta">
                            <span><i class="bi bi-tags-fill"></i> ${item.category.category_name}</span>
                            <span><i class="bi bi-box-seam"></i> ${availableItems}/${totalItems} available</span>
                        </div>
                        <p class="facility-description">${item.description?.substring(0, 100) || 'No description available.'}${item.description?.length > 100 ? '...' : ''}</p>
                        <div class="catalog-card-fee">
                            <i class="bi bi-cash-stack"></i> ₱${parseFloat(item.external_fee).toLocaleString()} (${item.rate_type})
                        </div>
                    </div>
                    <div class="catalog-card-actions">
                        <button class="btn btn-primary add-to-cart-btn" data-id="${item.equipment_id}">Add to Form</button>
                        <button class="btn btn-outline-secondary">View Details</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderFacilitiesList(facilities) {
        catalogItemsContainer.classList.remove('grid-layout');
        catalogItemsContainer.classList.add('list-layout');

        catalogItemsContainer.innerHTML = facilities.map(facility => {
            const primaryImage = facility.images?.find(img => img.type_id === 1)?.image_url || 'https://via.placeholder.com/300x200';

            return `
                <div class="catalog-card">
                    <img src="${primaryImage}" alt="${facility.facility_name}" class="catalog-card-img">
                    <div class="catalog-card-details">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 data-id="${facility.facility_id}">${facility.facility_name}</h5>
                            <span class="status-banner" style="background-color: ${facility.status.color_code};">
                                ${facility.status.status_name}
                            </span>
                        </div>
                        <div class="catalog-card-meta">
                            <span><i class="bi bi-people-fill"></i> ${facility.capacity || 'N/A'}</span>
                            <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || facility.category.category_name}</span>
                        </div>
                        <p class="facility-description">${facility.description || 'No description available.'}</p>
                        <div class="catalog-card-fee">
                            <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
                        </div>
                    </div>
                    <div class="catalog-card-actions">
                        <button class="btn btn-primary add-to-cart-btn" data-id="${facility.facility_id}">Add to Form</button>
                        <button class="btn btn-outline-secondary">View Calendar</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderEquipmentList(equipment) {
        catalogItemsContainer.classList.remove('grid-layout');
        catalogItemsContainer.classList.add('list-layout');

        catalogItemsContainer.innerHTML = equipment.map(item => {
            const primaryImage = item.images?.find(img => img.type_id === 1)?.image_url || 'https://via.placeholder.com/300x200';
            const availableItems = item.items?.filter(i => [1, 2, 3].includes(i.condition_id)).length || 0;
            const totalItems = item.items?.length || 0;

            return `
                <div class="catalog-card">
                    <img src="${primaryImage}" alt="${item.equipment_name}" class="catalog-card-img">
                    <div class="catalog-card-details">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 data-id="${item.equipment_id}">${item.equipment_name}</h5>
                            <span class="status-banner" style="background-color: ${item.status.color_code};">
                                ${item.status.status_name}
                            </span>
                        </div>
                        <div class="catalog-card-meta">
                            <span><i class="bi bi-tags-fill"></i> ${item.category.category_name}</span>
                            <span><i class="bi bi-box-seam"></i> ${availableItems}/${totalItems} available</span>
                        </div>
                        <p class="facility-description">${item.description || 'No description available.'}</p>
                        <div class="catalog-card-fee">
                            <i class="bi bi-cash-stack"></i> ₱${parseFloat(item.external_fee).toLocaleString()} (${item.rate_type})
                        </div>
                    </div>
                    <div class="catalog-card-actions">
                        <button class="btn btn-primary add-to-cart-btn" data-id="${item.equipment_id}">Add to Form</button>
                        <button class="btn btn-outline-secondary">View Details</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        pagination.innerHTML = '';

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageItem.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = i;
                filterAndRenderItems();
                window.scrollTo({ top: catalogItemsContainer.offsetTop - 100, behavior: 'smooth' });
            });
            pagination.appendChild(pageItem);
        }
    }

    function setupEventListeners() {
        // Category and subcategory filters
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('category-filter') || e.target.classList.contains('subcategory-filter')) {
                const label = e.target.nextElementSibling;
                if (e.target.checked) {
                    label.style.fontWeight = 'bold';
                } else {
                    label.style.fontWeight = '';
                }
                currentPage = 1;
                filterAndRenderItems();
            }
        });

        // Layout toggle
        document.querySelectorAll('.layout-option').forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                currentLayout = option.dataset.layout;
                document.querySelectorAll('.layout-option').forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                filterAndRenderItems();
            });
        });

        // Catalog type dropdown (facilities/equipment)
        document.querySelectorAll('.dropdown-item[data-catalog-type]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                currentCatalogType = this.dataset.catalogType;
                chooseCatalogDropdown.textContent = this.textContent; // Update dropdown text
                currentPage = 1;

                // Re-render category filters and items
                renderCategoryFilters();
                filterAndRenderItems();
            });
        });
    }

    async function showFacilityDetails(facilityId) {
        try {
            const facility = allFacilities.find(f => f.facility_id == facilityId);
            if (!facility) return;

            const primaryImage = facility.images?.find(img => img.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/800x400';

            document.getElementById('facilityDetailModalLabel').textContent = facility.facility_name;
            document.getElementById('facilityDetailContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <img src="${primaryImage}" alt="${facility.facility_name}" class="facility-image img-fluid">
                    </div>
                    <div class="col-md-6">
                        <div class="facility-details">
                            <p><strong>Status:</strong> <span class="badge" style="background-color: ${facility.status.color_code}">${facility.status.status_name}</span></p>
                            <p><strong>Category:</strong> ${facility.category.category_name}</p>
                            <p><strong>Subcategory:</strong> ${facility.subcategory?.subcategory_name || 'N/A'}</p>
                            <p><strong>Capacity:</strong> ${facility.capacity}</p>
                            <p><strong>Rate:</strong> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})</p>
                            <p><strong>Description:</strong></p>
                            <p>${facility.description || 'No description available.'}</p>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary add-to-cart-btn" data-id="${facility.facility_id}">Add to Form</button>
                        </div>
                    </div>
                </div>
            `;

            // Add event listener to the modal's "Add to Form" button
            document.querySelector('#facilityDetailModal .add-to-cart-btn').addEventListener('click', function() {
                addToCart(facilityId, 'facility');
                facilityDetailModal.hide();
            });

            facilityDetailModal.show();
        } catch (error) {
            console.error('Error showing facility details:', error);
            showError('Failed to load facility details.');
        }
    }

    async function addToCart(id, type) {
        try {
            const response = await fetch('/api/requisition/add-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    [`${type}_id`]: id,
                    type: type
                })
            });

            if (response.ok) {
                updateCartBadge();
                showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} added to form`);
            } else {
                throw new Error('Failed to add item');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showToast('Failed to add item to form', 'error');
        }
    }

    async function updateCartBadge() {
        try {
            const response = await fetch('/api/requisition/calculate-fees');
            if (!response.ok) throw new Error('Failed to fetch cart count');
            
            const data = await response.json();
            const totalItems = data.data?.selected_items?.length || 0;
            const badge = document.getElementById('requisitionBadge');
            
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        } catch (error) {
            console.error('Error updating cart badge:', error);
        }
    }

    function showToast(message, type = 'success') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3`;
        toast.style.zIndex = '1100';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it hides
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    function showError(message) {
        showToast(message, 'error');
    }
});
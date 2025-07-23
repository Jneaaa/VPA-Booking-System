document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentPage = 1;
    const itemsPerPage = 6;
    let allFacilities = [];
    let categories = [];
    let filteredFacilities = [];
    let currentLayout = 'grid';

    // DOM elements
    const loadingIndicator = document.getElementById('loadingIndicator');
    const catalogItemsContainer = document.getElementById('catalogItemsContainer');
    const categoryFilterList = document.getElementById('categoryFilterList');
    const pagination = document.getElementById('pagination');
    const layoutDropdown = document.getElementById('layoutDropdown');
    const facilityDetailModal = new bootstrap.Modal(document.getElementById('facilityDetailModal'));

    // Initialize the page
    init();

    async function init() {
        try {
            const [facilitiesData, categoriesData] = await Promise.all([
                fetchData('http://127.0.0.1:8000/api/facilities'),
                fetchData('http://127.0.0.1:8000/api/facility-categories/index')
            ]);

            allFacilities = facilitiesData.data || [];
            categories = categoriesData || [];

            renderCategoryFilters();
            filterAndRenderFacilities();
            setupEventListeners();
        } catch (error) {
            console.error('Error initializing page:', error);
            showError('Failed to load facilities. Please try again later.');
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

    // Replace the renderCategoryFilters function with this updated version
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
    
    // Store reference to the allCategories checkbox
    const allCategoriesCheckbox = allCategoriesItem.querySelector('.form-check-input');
    
    allCategoriesCheckbox.addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.category-filter:not(#allCategories), .subcategory-filter').forEach(input => {
                input.checked = false;
                input.disabled = false;
            });
            filterAndRenderFacilities();
        }
    });
    categoryFilterList.appendChild(allCategoriesItem);

    // Add categories and subcategories
    categories.forEach(category => {
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
                // Uncheck and disable "All Categories" when any subcategory is selected
                allCategoriesCheckbox.checked = false;
                allCategoriesCheckbox.disabled = false;
                
                // Update category checkbox based on subcategory selections
                const anySubChecked = Array.from(subcategoryList.querySelectorAll('.subcategory-filter:checked')).length > 0;
                categoryCheckbox.checked = anySubChecked;
                
                filterAndRenderFacilities();
            }
        });

        categoryCheckbox.addEventListener('change', function() {
            // Uncheck and disable "All Categories" when any category is selected
            allCategoriesCheckbox.checked = false;
            allCategoriesCheckbox.disabled = false;
            
            if (!this.checked) {
                subcategoryList.querySelectorAll('.subcategory-filter').forEach(subCheckbox => {
                    subCheckbox.checked = false;
                });
            }
            filterAndRenderFacilities();
        });

        categoryFilterList.appendChild(categoryItem);
    });
}

// Replace the setupEventListeners function with this simplified version
function setupEventListeners() {
    // Layout toggle
    document.querySelectorAll('.layout-option').forEach(option => {
        option.addEventListener('click', (e) => {
            e.preventDefault();
            currentLayout = option.dataset.layout;
            document.querySelectorAll('.layout-option').forEach(opt => opt.classList.remove('active'));
            option.classList.add('active');
            filterAndRenderFacilities();
        });
    });
}

    function filterFacilities() {
        filteredFacilities = [...allFacilities];

        const allCategoriesCheckbox = document.getElementById('allCategories');
        if (allCategoriesCheckbox.checked) {
            return filteredFacilities; // Show all facilities when "All Categories" is checked
        }

        const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(input => input.value);
        const selectedSubcategories = Array.from(document.querySelectorAll('.subcategory-filter:checked')).map(input => input.value);

        if (selectedCategories.length > 0) {
            filteredFacilities = filteredFacilities.filter(facility => 
                selectedCategories.includes(facility.category.category_id.toString())
            );
        }

        if (selectedSubcategories.length > 0) {
            filteredFacilities = filteredFacilities.filter(facility => 
                selectedSubcategories.includes(facility.subcategory?.subcategory_id.toString())
            );
        }

        return filteredFacilities;
    }

    function filterAndRenderFacilities() {
        const filtered = filterFacilities();
        renderFacilities(filtered);
        renderPagination(filtered.length);
    }

    function renderFacilities(facilities) {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedFacilities = facilities.slice(startIndex, startIndex + itemsPerPage);

        catalogItemsContainer.innerHTML = '';

        if (paginatedFacilities.length === 0) {
            catalogItemsContainer.innerHTML = `
                <div class="col-12 text-cencatalog-items-empty
                    <i class="bi bi-building fs-1 text-muted"></i>
                    <h4>No facilities found</h4>
                </div>
            `;
            return;
        }

        if (currentLayout === 'grid') {
            renderGridLayout(paginatedFacilities);
        } else {
            renderListLayout(paginatedFacilities);
        }

        // Add event listeners to facility name links
        document.querySelectorAll('.catalog-card-details h5').forEach(title => {
            title.addEventListener('click', function() {
                const facilityId = this.getAttribute('data-facility-id');
                showFacilityDetails(facilityId);
            });
        });
    }

    function renderGridLayout(facilities) {
        catalogItemsContainer.classList.remove('list-layout');
        catalogItemsContainer.classList.add('grid-layout');

        catalogItemsContainer.innerHTML = facilities.map(facility => {
            const primaryImage = facility.images.find(img => img.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/300x200';

            return `
                <div class="catalog-card">
                    <img src="${primaryImage}" alt="${facility.facility_name}" class="catalog-card-img">
                    <div class="catalog-card-details">
                        <h5 data-facility-id="${facility.facility_id}">${facility.facility_name}</h5>
                        <span class="status-banner" style="background-color: ${facility.status.color_code};">
                            ${facility.status.status_name}
                        </span>
                        <div class="catalog-card-meta">
                            <span><i class="bi bi-people-fill"></i> ${facility.capacity}</span>
                            <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || 'N/A'}</span>
                        </div>
                        <p class="facility-description">${facility.description || 'No description available.'}</p>
                        <div class="catalog-card-fee">
                            <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
                        </div>
                    </div>
                    <div class="catalog-card-actions">
                        <button class="btn btn-primary">Add to Form</button>
                        <button class="btn btn-outline-secondary">View Calendar</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderListLayout(facilities) {
        catalogItemsContainer.classList.remove('grid-layout');
        catalogItemsContainer.classList.add('list-layout');

        catalogItemsContainer.innerHTML = facilities.map(facility => {
            const primaryImage = facility.images.find(img => img.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/300x200';

            return `
                <div class="catalog-card">
                    <img src="${primaryImage}" alt="${facility.facility_name}" class="catalog-card-img">
                    <div class="catalog-card-details">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 data-facility-id="${facility.facility_id}">${facility.facility_name}</h5>
                            <span class="status-banner" style="background-color: ${facility.status.color_code};">
                                ${facility.status.status_name}
                            </span>
                        </div>
                        <div class="catalog-card-meta">
                            <span><i class="bi bi-people-fill"></i> ${facility.capacity}</span>
                            <span><i class="bi bi-tags-fill"></i> ${facility.subcategory?.subcategory_name || 'N/A'}</span>
                        </div>
                        <p class="facility-description">${facility.description || 'No description available.'}</p>
                        <div class="catalog-card-fee">
                            <i class="bi bi-cash-stack"></i> ₱${parseFloat(facility.external_fee).toLocaleString()} (${facility.rate_type})
                        </div>
                    </div>
                    <div class="catalog-card-actions">
                        <button class="btn btn-primary">Add to Form</button>
                        <button class="btn btn-outline-secondary">View Calendar</button>
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
                filterAndRenderFacilities();
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
                    label.style.backgroundColor = '#e9ecef';
                    label.style.fontWeight = 'bold';
                } else {
                    label.style.backgroundColor = '';
                    label.style.fontWeight = '';
                }
                currentPage = 1;
                filterAndRenderFacilities();
            }
        });

        // Layout toggle
        document.querySelectorAll('.layout-option').forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                currentLayout = option.dataset.layout;
                document.querySelectorAll('.layout-option').forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                filterAndRenderFacilities();
            });
        });
    }

    async function showFacilityDetails(facilityId) {
        try {
            const facility = allFacilities.find(f => f.facility_id == facilityId);
            if (!facility) return;

            const primaryImage = facility.images.find(img => img.image_type === 'Primary')?.image_url || 'https://via.placeholder.com/800x400';

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
                    </div>
                </div>
            `;

            facilityDetailModal.show();
        } catch (error) {
            console.error('Error showing facility details:', error);
            showError('Failed to load facility details.');
        }
    }

    function showError(message) {
        const toast = document.createElement('div');
        toast.className = 'toast show align-items-center text-white bg-danger';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
});
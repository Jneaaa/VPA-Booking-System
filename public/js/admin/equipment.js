document.addEventListener('DOMContentLoaded', function() {
    // Authentication and authorization check
    const token = localStorage.getItem('adminToken');
    if (!token) {
        window.location.href = '/admin/admin-login';
        return;
    }

    // Get user data
    let currentUser = null;
    let userDepartments = [];
    let allEquipment = [];
    
    // Fetch user data first
    fetchUserData().then(() => {
        // Only proceed if user is Head Admin or Inventory Manager
        if (currentUser.role_id !== 1 && currentUser.role_id !== 2) { // Assuming 1=Head Admin, 3=Inventory Manager
            window.location.href = 'unauthorized.html';
            return;
        }
        
        // Initialize page
        initializePage();
    });

    async function fetchUserData() {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/user', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch user data');
            }

            const data = await response.json();
            currentUser = data.user;
            userDepartments = data.user.departments.map(dept => dept.department_id);
            
            // Populate department filter dropdown
            populateDepartmentFilter(data.user.departments);
        } catch (error) {
            console.error('Error fetching user data:', error);
            alert('Failed to load user data');
        }
    }

    function populateDepartmentFilter(departments) {
        const departmentFilter = document.getElementById('departmentFilter');
        departmentFilter.innerHTML = '<option value="all">All Departments</option>';
        
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.department_id;
            option.textContent = dept.department_name;
            departmentFilter.appendChild(option);
        });
    }

    function initializePage() {
        // Fetch equipment data
        fetchEquipment();
        
        // Set up event listeners
        setupEventListeners();
        
        // Hide add button if user isn't Head Admin or doesn't have permission
        if (currentUser.role_id !== 1) {
            document.querySelector('.btn-primary').style.display = 'none';
        }
    }

    async function fetchEquipment() {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/equipment', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch equipment');
            }

            const data = await response.json();
            allEquipment = data.data;
            
            // Filter equipment based on user's departments if not Head Admin
            if (currentUser.role_id !== 1) {
                allEquipment = allEquipment.filter(equip => 
                    userDepartments.includes(equip.department.department_id))
            }
            
            renderEquipment(allEquipment);
            populateCategoryFilter(allEquipment);
        } catch (error) {
            console.error('Error fetching equipment:', error);
            alert('Failed to load equipment data');
        }
    }

    function populateCategoryFilter(equipmentList) {
        const categoryFilter = document.getElementById('categoryFilter');
        const categories = [...new Set(equipmentList.map(equip => equip.category.category_name))];
        
        categoryFilter.innerHTML = '<option value="all">All Categories</option>';
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category;
            option.textContent = category;
            categoryFilter.appendChild(option);
        });
    }

    function renderEquipment(equipmentList) {
        const container = document.getElementById('facilityContainer');
        if (!container) return;

        container.innerHTML = ''; // Clear existing content

        if (equipmentList.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-5"><h4>No equipment found</h4></div>';
            return;
        }

        equipmentList.forEach(equipment => {
            const statusClass = getStatusClass(equipment.status.status_name);
            const primaryImage = equipment.images?.find(img => img.type_id === 1)?.image_url || 'assets/placeholder.jpg';

            const card = document.createElement('div');
            card.className = 'col-md-4 facility-card mb-4';
            card.dataset.status = equipment.status.status_name.toLowerCase();
            card.dataset.department = equipment.department.department_id;
            card.dataset.category = equipment.category.category_name;
            card.dataset.title = equipment.equipment_name.toLowerCase();

            card.innerHTML = `
                <div class="card h-100">
                    <img src="${primaryImage}" class="card-img-top" alt="${equipment.equipment_name}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">${equipment.equipment_name}</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> ${equipment.category.category_name} |
                                <i class="bi bi-building-fill text-primary"></i> ${equipment.department.department_name}
                            </p>
                            <p class="${statusClass}">${equipment.status.status_name}</p>
                            <p class="card-text mb-3">${equipment.description || 'No description available'}</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex" data-id="${equipment.equipment_id}">Manage</button>
                            <button class="btn btn-outline-danger btn-delete" data-id="${equipment.equipment_id}">Delete</button>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(card);
        });

        // Initialize pagination
        initializePagination();
    }

    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'available': return 'status-available';
            case 'unavailable': return 'status-unavailable';
            case 'reserved': return 'status-reserved';
            case 'under maintenance': return 'status-maintenance';
            default: return '';
        }
    }

    function setupEventListeners() {
        // Add event listeners for all filter controls
        const searchInput = document.getElementById('searchInput');
        const layoutSelect = document.getElementById('layoutSelect');
        const statusFilter = document.getElementById('statusFilter');
        const departmentFilter = document.getElementById('departmentFilter');
        const categoryFilter = document.getElementById('categoryFilter');

        [searchInput, layoutSelect, statusFilter, departmentFilter, categoryFilter].forEach(control => {
            control.addEventListener('change', filterFacilities);
        });
        searchInput.addEventListener('input', filterFacilities);

        // Add event listeners to dynamically created buttons using event delegation
        document.addEventListener('click', function(e) {
            // Manage button
            if (e.target.classList.contains('btn-manage')) {
                const equipmentId = e.target.dataset.id;
                window.location.href = `edit-equipment.html?id=${equipmentId}`;
            }
            
            // Delete button
            if (e.target.classList.contains('btn-delete')) {
                const equipmentId = e.target.dataset.id;
                if (confirm('Are you sure you want to delete this equipment?')) {
                    deleteEquipment(equipmentId);
                }
            }
        });
    }

    async function deleteEquipment(id) {
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to delete equipment');
            }

            // Refresh the list
            fetchEquipment();
            alert('Equipment deleted successfully');
        } catch (error) {
            console.error('Error deleting equipment:', error);
            alert('Failed to delete equipment');
        }
    }

    // Filter functions
    function filterFacilities() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const department = document.getElementById('departmentFilter').value;
        const category = document.getElementById('categoryFilter').value;
        const layout = document.getElementById('layoutSelect').value;

        // Apply layout view
        const container = document.getElementById('facilityContainer');
        container.className = `row g-3 ${layout === 'list' ? 'list-view' : ''}`;

        // Filter equipment cards
        const cards = document.querySelectorAll('.facility-card');
        cards.forEach(card => {
            const cardStatus = card.dataset.status;
            const cardDept = card.dataset.department;
            const cardCategory = card.dataset.category;
            const cardTitle = card.dataset.title;

            const matchesSearch = cardTitle.includes(searchTerm);
            const matchesStatus = (status === 'all' || cardStatus === status.toLowerCase());
            const matchesDept = (department === 'all' || cardDept === department);
            const matchesCategory = (category === 'all' || cardCategory.toLowerCase() === category.toLowerCase());

            card.style.display = (matchesSearch && matchesStatus && matchesDept && matchesCategory) ? 'block' : 'none';
        });

        // Update pagination
        showPage(1);
    }

    // Pagination functions
    function initializePagination() {
        updatePagination();
        showPage(1);

        // Add event listeners to page links
        document.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                showPage(page);
            });
        });

        // Previous page button
        document.getElementById('prevPage').addEventListener('click', function(e) {
            e.preventDefault();
            if (!this.classList.contains('disabled')) {
                showPage(currentPage - 1);
            }
        });

        // Next page button
        document.getElementById('nextPage').addEventListener('click', function(e) {
            e.preventDefault();
            if (!this.classList.contains('disabled')) {
                showPage(currentPage + 1);
            }
        });
    }

    let currentPage = 1;
    const itemsPerPage = 6;

    function showPage(page) {
        currentPage = page;
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        // Get all visible cards after filtering
        const visibleCards = Array.from(document.querySelectorAll('.facility-card'))
            .filter(card => card.style.display !== 'none');

        // Hide all cards first
        document.querySelectorAll('.facility-card').forEach(card => {
            card.style.display = 'none';
        });

        // Show cards for current page
        for (let i = startIndex; i < endIndex && i < visibleCards.length; i++) {
            visibleCards[i].style.display = 'block';
        }

        updatePagination();
    }

    function updatePagination() {
        const visibleCards = Array.from(document.querySelectorAll('.facility-card'))
            .filter(card => card.style.display !== 'none');
        const totalPages = Math.ceil(visibleCards.length / itemsPerPage);
        const pageLinks = document.querySelectorAll('.page-link[data-page]');
        const prevPageBtn = document.getElementById('prevPage');
        const nextPageBtn = document.getElementById('nextPage');

        // Update active state of page links
        pageLinks.forEach(link => {
            const page = parseInt(link.getAttribute('data-page'));
            link.parentElement.classList.toggle('active', page === currentPage);

            // Hide page links that are beyond total pages
            if (page > totalPages) {
                link.parentElement.style.display = 'none';
            } else {
                link.parentElement.style.display = 'block';
            }
        });

        // Disable/enable previous and next buttons
        prevPageBtn.classList.toggle('disabled', currentPage === 1);
        nextPageBtn.classList.toggle('disabled', currentPage === totalPages || totalPages === 0);
    }
});
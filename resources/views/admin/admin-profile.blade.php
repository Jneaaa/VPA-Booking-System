@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="profile-wrapper">
    <!-- Hero/Wallpaper Section -->
    <div class="profile-hero position-relative mb-5" style="height: 200px; background-color: #f8f9fa;">
        <div id="wallpaper-container" class="w-100 h-100">
            <!-- Wallpaper will be loaded here -->
        </div>
        <button class="btn btn-light position-absolute bottom-0 end-0 m-3">
            <i class="bi bi-image me-2"></i>Change Cover
        </button>
    </div>
    <input type="file" id="wallpaper-upload" class="d-none" accept="image/*">

    <div class="container position-relative">
        <!-- Profile Avatar -->
        <div class="position-absolute" style="top: -70px; left: 50px; z-index: 10;">
            <div class="position-relative">
                <div class="avatar-container rounded-circle border border-4 border-white" 
                     style="width: 150px; height: 150px; overflow: hidden;">
                    <img id="profile-photo" src="{{ url('https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png') }}"
                        class="w-100 h-100 object-fit-cover">
                </div>
                <button class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm" 
                        style="width: 32px; height: 32px;" onclick="document.getElementById('photo-upload').click()">
                    <i class="bi bi-pencil"></i>
                </button>
                <input type="file" id="photo-upload" class="d-none" accept="image/*">
            </div>
        </div>

        <!-- Profile Content -->
        <div class="row mt-5 pt-5 g-3"> <!-- Changed spacing -->
            <!-- Main Info Card -->
            <div class="col-md-8">
                <div class="card shadow-sm h-100"> <!-- Added h-100 -->
                    <div class="card-body p-3"> <!-- Reduced padding -->
                        <div id="main-info-loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="main-info-content" style="display: none; margin-top: 15px; margin-left: 10px;">
                            <h2 class="card-title mb-4" id="admin-full-name"></h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p><strong>School ID:</strong> <span id="admin-school-id"></span></p>
                                    <p><strong>Email:</strong> <span id="admin-email"></span></p>
                                    <p><strong>Contact:</strong> <span id="admin-contact"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Member Since:</strong> <span id="admin-created"></span></p>
                                    <p><strong>Last Updated:</strong> <span id="admin-updated"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role & Departments Card -->
            <div class="col-md-4 d-flex flex-column">  <!-- Added d-flex and flex-column -->
                <div class="card shadow-sm mb-3"> <!-- Reduced margin -->
                    <div class="card-body">
                        <h5 class="card-title">Role Details</h5>
                        <div id="role-content"></div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Managing Departments</h5>
                        <div id="departments-content">
                            <div class="text-muted">No departments assigned</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('adminToken');
    if (!token) {
        window.location.href = '/admin/admin-login';
        return;
    }

    // Load profile data
    fetch('http://127.0.0.1:8000/api/admin/profile', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        // Load profile photo
        if (data.photo_url) {
            const img = new Image();
            img.onload = () => {
                document.getElementById('profile-photo').src = data.photo_url;
            };
            img.src = data.photo_url;
        }

        // Load wallpaper if exists
        if (data.wallpaper_url) {
            document.getElementById('wallpaper-container').style.backgroundImage = `url(${data.wallpaper_url})`;
            document.getElementById('wallpaper-container').style.backgroundSize = 'cover';
            document.getElementById('wallpaper-container').style.backgroundPosition = 'center';
        }

        // Update main info
        document.getElementById('admin-full-name').textContent = `${data.first_name} ${data.last_name}`;
        document.getElementById('admin-school-id').textContent = data.school_id || 'Not set';
        document.getElementById('admin-email').textContent = data.email;
        document.getElementById('admin-contact').textContent = data.contact_number || 'Not set';
        document.getElementById('admin-role').textContent = data.role?.role_title;
        document.getElementById('admin-created').textContent = new Date(data.created_at).toLocaleDateString();
        document.getElementById('admin-updated').textContent = new Date(data.updated_at).toLocaleDateString();

        // Update role details
        if (data.role) {
            document.getElementById('role-content').innerHTML = `
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">${data.role.role_title}</span>
                </div>
                <p class="text-muted small">${data.role.description}</p>
            `;
        }

        // Update departments
        if (data.departments && data.departments.length > 0) {
            const deptList = data.departments.map(dept => 
                `<div class="badge bg-light text-dark me-2 mb-2">${dept.name}</div>`
            ).join('');
            document.getElementById('departments-content').innerHTML = deptList;
        }

        // Show content
        document.getElementById('main-info-loading').style.display = 'none';
        document.getElementById('main-info-content').style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('profile-loading').innerHTML = 
            '<i class="bi bi-person-circle fs-1 text-secondary"></i>';
    });

    // Handle photo upload
    document.getElementById('photo-upload').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('type', 'photo');

        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/update-photo', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to upload photo');
            }
            
            const result = await response.json();
            if (result.photo_url) {
                document.getElementById('profile-photo').src = result.photo_url;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to upload photo: ' + error.message);
        }
    });

    // Handle wallpaper upload
    document.querySelector('.profile-hero button').addEventListener('click', () => {
        document.getElementById('wallpaper-upload').click();
    });

    document.getElementById('wallpaper-upload').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('wallpaper', file);
        formData.append('type', 'wallpaper');

        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/update-photo', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to upload wallpaper');
            }

            const result = await response.json();
            if (result.wallpaper_url) {
                document.getElementById('wallpaper-container').style.backgroundImage = 
                    `url(${result.wallpaper_url})`;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to upload wallpaper: ' + error.message);
        }
    });
});
</script>
@endsection
        



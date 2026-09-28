@extends('layouts.app')

@section('title', 'Admin Dashboard - Portfolio Builder')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="admin-wrapper">
    <!-- Mobile Header -->
    <div class="admin-mobile-nav">
        <button class="menu-toggle" id="menu-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="mobile-brand">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Portfolio Admin</span>
        </div>
    </div>

    <!-- Sidebar Backdrop -->
    <div class="admin-sidebar-backdrop" id="sidebar-backdrop"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Portfolio Admin</span>
            <button class="sidebar-close" id="sidebar-close">&times;</button>
        </div>
        
        <div class="admin-user-info">
            <span class="user-name">Welcome, Admin</span>
            <span class="user-role">Super Administrator</span>
        </div>
        
        <nav class="admin-nav">
            <button class="nav-tab active" onclick="switchTab(event, 'tab-profile')">
                <i class="fa-regular fa-user"></i> Profile & About
            </button>
            <button class="nav-tab" onclick="switchTab(event, 'tab-education')">
                <i class="fa-solid fa-graduation-cap"></i> Education
            </button>
            <button class="nav-tab" onclick="switchTab(event, 'tab-experience')">
                <i class="fa-solid fa-briefcase"></i> Experience
            </button>
            <button class="nav-tab" onclick="switchTab(event, 'tab-achievements')">
                <i class="fa-solid fa-award"></i> Achievements
            </button>
            <button class="nav-tab" onclick="switchTab(event, 'tab-skills')">
                <i class="fa-solid fa-screwdriver-wrench"></i> Skills
            </button>
            <button class="nav-tab" onclick="switchTab(event, 'tab-projects')">
                <i class="fa-solid fa-code"></i> Projects
            </button>
        </nav>
        
        <div class="admin-sidebar-footer">
            <a href="{{ route('portfolio.preview') }}" target="_blank" class="btn btn-outline btn-block">
                <i class="fa-solid fa-eye"></i> Live Preview
            </a>
            <a href="{{ route('portfolio.export') }}" class="btn btn-secondary btn-block mt-2">
                <i class="fa-solid fa-file-pdf"></i> Export PDF Resume
            </a>
            <form action="{{ route('admin.logout') }}" method="POST" class="logout-form mt-4">
                @csrf
                <button type="submit" class="btn btn-danger btn-block">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Panel -->
    <main class="admin-main">
        <!-- Top Navigation -->
        <header class="admin-header">
            <h2 id="current-tab-title">Profile & About</h2>
            <div class="header-actions">
                <span class="date-badge"><i class="fa-regular fa-calendar"></i> {{ date('F d, Y') }}</span>
            </div>
        </header>

        <!-- Notification Toast -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div class="alert-content">
                    <strong>Success!</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div class="alert-content">
                    <strong>Error!</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
            </div>
        @endif

        <!-- TAB CONTENT PLACES -->
        <div class="tab-viewport">
            
            <!-- 1. PROFILE TAB -->
            <div id="tab-profile" class="tab-pane active">
                <div class="card">
                    <div class="card-header">
                        <h3>Personal Details & Biography</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $profile->name }}" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Headline / Subtitle</label>
                                    <input type="text" name="headline" class="form-control" value="{{ $profile->headline }}" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ $profile->email }}" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $profile->phone }}" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">LinkedIn URL</label>
                                    <input type="url" name="linkedin_url" class="form-control" value="{{ $profile->linkedin_url }}">
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Instagram URL</label>
                                    <input type="url" name="instagram_url" class="form-control" value="{{ $profile->instagram_url }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Biography / About Me</label>
                                <textarea name="about_text" class="form-control" rows="6" required>{{ $profile->about_text }}</textarea>
                            </div>

                            <div class="form-row media-section">
                                <div class="form-group col-6">
                                    <label class="form-label">Profile Photo (Leave blank to keep current)</label>
                                    <input type="file" name="photo" class="form-control file-input" accept="image/*">
                                    @if($profile->photo_path)
                                        <div class="current-media-preview">
                                            <img src="{{ asset($profile->photo_path) }}" alt="Current Photo">
                                            <span>Current Photo</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Original CV (PDF) (Leave blank to keep current)</label>
                                    <input type="file" name="cv" class="form-control file-input" accept="application/pdf">
                                    @if($profile->cv_path)
                                        <div class="current-media-preview">
                                            <div class="pdf-icon"><i class="fa-solid fa-file-pdf"></i></div>
                                            <a href="{{ asset($profile->cv_path) }}" target="_blank">View Current CV</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 2. EDUCATION TAB -->
            <div id="tab-education" class="tab-pane">
                <!-- Add Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Add New Education Record</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.education.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Institution / School</label>
                                    <input type="text" name="institution" class="form-control" placeholder="e.g. Binus University" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Degree</label>
                                    <input type="text" name="degree" class="form-control" placeholder="e.g. Bachelor of Computer Science" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label class="form-label">Major</label>
                                    <input type="text" name="major" class="form-control" placeholder="e.g. Intelligent Systems">
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">GPA (optional)</label>
                                    <input type="text" name="gpa" class="form-control" placeholder="e.g. 3.06">
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Sort Order (optional)</label>
                                    <input type="number" name="sort_order" class="form-control" placeholder="Auto-increment">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" name="start_date" class="form-control" placeholder="e.g. Aug 2018" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">End Date</label>
                                    <input type="text" name="end_date" class="form-control" placeholder="e.g. Jul 2022" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description / Bullet Points</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Describe projects, GPA details or courses..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Attachments / Certificates (Multiple Images)</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Education</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Items List -->
                <div class="card">
                    <div class="card-header d-flex-between">
                        <h3>Existing Records</h3>
                        <form action="{{ route('admin.sorting.update') }}" method="POST" class="sorting-form">
                            @csrf
                            <input type="hidden" name="type" value="education">
                            <label class="sort-label">Sort by:</label>
                            <select name="sort_by" class="sort-select" onchange="this.form.submit()">
                                <option value="sort_order" {{ session('portfolio_sort_education') === 'sort_order' ? 'selected' : '' }}>Default (Sort Order)</option>
                                <option value="institution" {{ session('portfolio_sort_education') === 'institution' ? 'selected' : '' }}>Institution Name</option>
                                <option value="degree" {{ session('portfolio_sort_education') === 'degree' ? 'selected' : '' }}>Degree</option>
                            </select>
                        </form>
                    </div>

                    <!-- Bulk Delete Form -->
                    <form action="{{ route('admin.education.bulk-delete') }}" method="POST" id="bulk-delete-education" onsubmit="return confirm('Are you sure you want to delete all selected education records?')">
                        @csrf
                        <div class="bulk-actions-bar" id="bulk-bar-education" style="display: none;">
                            <span class="selected-count" id="count-education">0 items selected</span>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete Selected</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" onclick="toggleSelectAll(this, 'education')" class="master-checkbox"></th>
                                    <th>Institution</th>
                                    <th>Degree / Major</th>
                                    <th>Period</th>
                                    <th>GPA</th>
                                    <th>Sort</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($education as $edu)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $edu->id }}" form="bulk-delete-education" onclick="updateSelectedCount('education')" class="child-checkbox-education"></td>
                                        <td><strong>{{ $edu->institution }}</strong></td>
                                        <td>{{ $edu->degree }} @if($edu->major) ({{ $edu->major }}) @endif</td>
                                        <td>{{ $edu->start_date }} - {{ $edu->end_date }}</td>
                                        <td>{{ $edu->gpa ?? '-' }}</td>
                                        <td>{{ $edu->sort_order }}</td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm" onclick="openEditEduModal({{ json_encode($edu) }})">Edit</button>
                                            <form action="{{ route('admin.education.destroy', $edu->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No education records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. EXPERIENCE TAB -->
            <div id="tab-experience" class="tab-pane">
                <!-- Add Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Add New Experience Record</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.experience.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Company / Organization</label>
                                    <input type="text" name="company" class="form-control" placeholder="e.g. Google Bangkit" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Role / Job Title</label>
                                    <input type="text" name="role" class="form-control" placeholder="e.g. Cloud Computing Cohort" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" name="start_date" class="form-control" placeholder="e.g. Feb 2021" required>
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">End Date</label>
                                    <input type="text" name="end_date" class="form-control" placeholder="e.g. Jun 2021" required>
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Sort Order (optional)</label>
                                    <input type="number" name="sort_order" class="form-control" placeholder="Auto-increment">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description / Responsibilities</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Detail your roles, courses, achievements..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Attachments / Documentation (Multiple Images)</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Experience</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Items List -->
                <div class="card">
                    <div class="card-header d-flex-between">
                        <h3>Existing Records</h3>
                        <form action="{{ route('admin.sorting.update') }}" method="POST" class="sorting-form">
                            @csrf
                            <input type="hidden" name="type" value="experience">
                            <label class="sort-label">Sort by:</label>
                            <select name="sort_by" class="sort-select" onchange="this.form.submit()">
                                <option value="sort_order" {{ session('portfolio_sort_experience') === 'sort_order' ? 'selected' : '' }}>Default (Sort Order)</option>
                                <option value="company" {{ session('portfolio_sort_experience') === 'company' ? 'selected' : '' }}>Company Name</option>
                                <option value="role" {{ session('portfolio_sort_experience') === 'role' ? 'selected' : '' }}>Role / Title</option>
                            </select>
                        </form>
                    </div>

                    <!-- Bulk Delete Form -->
                    <form action="{{ route('admin.experience.bulk-delete') }}" method="POST" id="bulk-delete-experience" onsubmit="return confirm('Are you sure you want to delete all selected experience records?')">
                        @csrf
                        <div class="bulk-actions-bar" id="bulk-bar-experience" style="display: none;">
                            <span class="selected-count" id="count-experience">0 items selected</span>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete Selected</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" onclick="toggleSelectAll(this, 'experience')" class="master-checkbox"></th>
                                    <th>Company</th>
                                    <th>Role</th>
                                    <th>Period</th>
                                    <th>Sort</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($experiences as $exp)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $exp->id }}" form="bulk-delete-experience" onclick="updateSelectedCount('experience')" class="child-checkbox-experience"></td>
                                        <td><strong>{{ $exp->company }}</strong></td>
                                        <td>{{ $exp->role }}</td>
                                        <td>{{ $exp->start_date }} - {{ $exp->end_date }}</td>
                                        <td>{{ $exp->sort_order }}</td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm" onclick="openEditExpModal({{ json_encode($exp) }})">Edit</button>
                                            <form action="{{ route('admin.experience.destroy', $exp->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No experience records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. ACHIEVEMENTS TAB -->
            <div id="tab-achievements" class="tab-pane">
                <!-- Add Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Add New Achievement / Certificate</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.achievements.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Certificate / Award Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Web Development Basic Certificate" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Issuer</label>
                                    <input type="text" name="issuer" class="form-control" placeholder="e.g. Dicoding">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label class="form-label">Date</label>
                                    <input type="text" name="date" class="form-control" placeholder="e.g. 2021">
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Credential URL</label>
                                    <input type="url" name="credential_url" class="form-control" placeholder="e.g. https://dicoding.com/cert/...">
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Sort Order (optional)</label>
                                    <input type="number" name="sort_order" class="form-control" placeholder="Auto-increment">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Achievement</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Items List -->
                <div class="card">
                    <div class="card-header d-flex-between">
                        <h3>Existing Records</h3>
                        <form action="{{ route('admin.sorting.update') }}" method="POST" class="sorting-form">
                            @csrf
                            <input type="hidden" name="type" value="achievement">
                            <label class="sort-label">Sort by:</label>
                            <select name="sort_by" class="sort-select" onchange="this.form.submit()">
                                <option value="sort_order" {{ session('portfolio_sort_achievement') === 'sort_order' ? 'selected' : '' }}>Default (Sort Order)</option>
                                <option value="title" {{ session('portfolio_sort_achievement') === 'title' ? 'selected' : '' }}>Title</option>
                                <option value="issuer" {{ session('portfolio_sort_achievement') === 'issuer' ? 'selected' : '' }}>Issuer</option>
                            </select>
                        </form>
                    </div>

                    <!-- Bulk Delete Form -->
                    <form action="{{ route('admin.achievements.bulk-delete') }}" method="POST" id="bulk-delete-achievement" onsubmit="return confirm('Are you sure you want to delete all selected achievements?')">
                        @csrf
                        <div class="bulk-actions-bar" id="bulk-bar-achievement" style="display: none;">
                            <span class="selected-count" id="count-achievement">0 items selected</span>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete Selected</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" onclick="toggleSelectAll(this, 'achievement')" class="master-checkbox"></th>
                                    <th>Title</th>
                                    <th>Issuer</th>
                                    <th>Date</th>
                                    <th>Credential</th>
                                    <th>Sort</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($achievements as $ach)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $ach->id }}" form="bulk-delete-achievement" onclick="updateSelectedCount('achievement')" class="child-checkbox-achievement"></td>
                                        <td><strong>{{ $ach->title }}</strong></td>
                                        <td>{{ $ach->issuer ?? '-' }}</td>
                                        <td>{{ $ach->date ?? '-' }}</td>
                                        <td>
                                            @if($ach->credential_url)
                                                <a href="{{ $ach->credential_url }}" target="_blank">View <i class="fa-solid fa-up-right-from-square"></i></a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $ach->sort_order }}</td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm" onclick="openEditAchModal({{ json_encode($ach) }})">Edit</button>
                                            <form action="{{ route('admin.achievements.destroy', $ach->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No achievements found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. SKILLS TAB -->
            <div id="tab-skills" class="tab-pane">
                <!-- Add Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Add New Skill / Language</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.skills.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-5">
                                    <label class="form-label">Skill Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Figma or English" required>
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-control" required>
                                        <option value="Technical">Technical</option>
                                        <option value="Soft Skill">Soft Skill</option>
                                        <option value="Language">Language</option>
                                    </select>
                                </div>
                                <div class="form-group col-3">
                                    <label class="form-label">Sort Order (optional)</label>
                                    <input type="number" name="sort_order" class="form-control" placeholder="Auto-increment">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Skill</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Items List -->
                <div class="card">
                    <div class="card-header d-flex-between">
                        <h3>Existing Skills & Languages</h3>
                        <form action="{{ route('admin.sorting.update') }}" method="POST" class="sorting-form">
                            @csrf
                            <input type="hidden" name="type" value="skill">
                            <label class="sort-label">Sort by:</label>
                            <select name="sort_by" class="sort-select" onchange="this.form.submit()">
                                <option value="sort_order" {{ session('portfolio_sort_skill') === 'sort_order' ? 'selected' : '' }}>Default (Sort Order)</option>
                                <option value="category" {{ session('portfolio_sort_skill') === 'category' ? 'selected' : '' }}>Category</option>
                                <option value="name" {{ session('portfolio_sort_skill') === 'name' ? 'selected' : '' }}>Skill Name</option>
                            </select>
                        </form>
                    </div>

                    <!-- Bulk Delete Form -->
                    <form action="{{ route('admin.skills.bulk-delete') }}" method="POST" id="bulk-delete-skill" onsubmit="return confirm('Are you sure you want to delete all selected skills?')">
                        @csrf
                        <div class="bulk-actions-bar" id="bulk-bar-skill" style="display: none;">
                            <span class="selected-count" id="count-skill">0 items selected</span>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete Selected</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" onclick="toggleSelectAll(this, 'skill')" class="master-checkbox"></th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Sort</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($skills as $skill)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $skill->id }}" form="bulk-delete-skill" onclick="updateSelectedCount('skill')" class="child-checkbox-skill"></td>
                                        <td><strong>{{ $skill->name }}</strong></td>
                                        <td><span class="badge badge-info">{{ $skill->category }}</span></td>
                                        <td>{{ $skill->sort_order }}</td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm" onclick="openEditSkillModal({{ json_encode($skill) }})">Edit</button>
                                            <form action="{{ route('admin.skills.destroy', $skill->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No skills found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 6. PROJECTS TAB -->
            <div id="tab-projects" class="tab-pane">
                <!-- Add Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Add New Project Record</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="form-label">Project Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Heartcare Application" required>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Project External URL (optional)</label>
                                    <input type="url" name="project_url" class="form-control" placeholder="e.g. https://github.com/...">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label class="form-label">Project Images (Leave blank if none, multiple)</label>
                                    <input type="file" name="images[]" class="form-control file-input" accept="image/*" multiple>
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Project Video (Leave blank if none)</label>
                                    <input type="file" name="video" class="form-control file-input" accept="video/*" onchange="generateVideoThumbnail(this, 'add-proj-video-thumbnail')">
                                    <input type="hidden" name="video_thumbnail" id="add-proj-video-thumbnail">
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Sort Order (optional)</label>
                                    <input type="number" name="sort_order" class="form-control" placeholder="Auto-increment">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Project Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Detail features, team size, tools used..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Project</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Items Grid -->
                <div class="card">
                    <div class="card-header d-flex-between">
                        <h3>Existing Projects</h3>
                        <form action="{{ route('admin.sorting.update') }}" method="POST" class="sorting-form">
                            @csrf
                            <input type="hidden" name="type" value="project">
                            <label class="sort-label">Sort by:</label>
                            <select name="sort_by" class="sort-select" onchange="this.form.submit()">
                                <option value="sort_order" {{ session('portfolio_sort_project') === 'sort_order' ? 'selected' : '' }}>Default (Sort Order)</option>
                                <option value="title" {{ session('portfolio_sort_project') === 'title' ? 'selected' : '' }}>Project Title</option>
                            </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <!-- Bulk Delete Form -->
                        <form action="{{ route('admin.projects.bulk-delete') }}" method="POST" id="bulk-delete-project" onsubmit="return confirm('Are you sure you want to delete all selected projects?')">
                            @csrf
                            <div class="bulk-actions-bar" id="bulk-bar-project" style="display: none; margin-bottom: 20px;">
                                <span class="selected-count" id="count-project">0 items selected</span>
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete Selected</button>
                            </div>
                        </form>

                        <!-- Project Cards with Master Checkbox -->
                        @if($projects->count() > 0)
                            <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" id="master-project-checkbox" onclick="toggleSelectAllProjects(this)" class="master-checkbox">
                                <label for="master-project-checkbox" style="font-size: 0.9rem; font-weight: 500; cursor: pointer;">Select All Projects</label>
                            </div>
                        @endif

                        <div class="admin-projects-grid">
                            @forelse($projects as $project)
                                <div class="admin-project-item position-relative">
                                    <!-- Checkbox Overlay for Projects -->
                                    <div class="project-checkbox-wrapper">
                                        <input type="checkbox" name="ids[]" value="{{ $project->id }}" form="bulk-delete-project" onclick="updateSelectedCount('project')" class="child-checkbox-project">
                                    </div>
                                    <div class="admin-project-preview">
                                        @if($project->video_path)
                                            <div class="admin-video-prev">
                                                <i class="fa-solid fa-video"></i> Video Uploaded
                                            </div>
                                        @elseif($project->image_path)
                                            <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}">
                                        @else
                                            <div class="admin-placeholder-prev">
                                                <i class="fa-solid fa-code"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="admin-project-meta">
                                        <h4>{{ $project->title }} (Sort: {{ $project->sort_order }})</h4>
                                        <p>{{ Str::limit($project->description, 100) }}</p>
                                        <div class="admin-project-actions mt-2">
                                            <button class="btn btn-secondary btn-sm" onclick="openEditProjModal({{ json_encode($project) }})">Edit</button>
                                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center w-100 py-4">No projects found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ================= EDIT MODALS / FORMS ================= -->

<!-- 1. EDUCATION EDIT MODAL -->
<div id="modal-edit-edu" class="admin-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Education Record</h3>
            <button onclick="closeModal('modal-edit-edu')" class="modal-close">&times;</button>
        </div>
        <form id="form-edit-edu" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">Institution / School</label>
                        <input type="text" name="institution" id="edit-edu-institution" class="form-control" required>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">Degree</label>
                        <input type="text" name="degree" id="edit-edu-degree" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-4">
                        <label class="form-label">Major</label>
                        <input type="text" name="major" id="edit-edu-major" class="form-control">
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">GPA</label>
                        <input type="text" name="gpa" id="edit-edu-gpa" class="form-control">
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Sort Order (optional)</label>
                        <input type="number" name="sort_order" id="edit-edu-sort-order" class="form-control" placeholder="Auto-increment">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="start_date" id="edit-edu-start-date" class="form-control" required>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">End Date</label>
                        <input type="text" name="end_date" id="edit-edu-end-date" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit-edu-description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group mt-3">
                    <label class="form-label">Upload New Attachments (Multiple Images)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                <div class="form-group mt-3" id="edit-edu-images-container" style="display: none;">
                    <label class="form-label">Current Attachments</label>
                    <div id="edit-edu-images-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <!-- Loaded dynamically via JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-edu')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. EXPERIENCE EDIT MODAL -->
<div id="modal-edit-exp" class="admin-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Experience Record</h3>
            <button onclick="closeModal('modal-edit-exp')" class="modal-close">&times;</button>
        </div>
        <form id="form-edit-exp" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">Company / Organization</label>
                        <input type="text" name="company" id="edit-exp-company" class="form-control" required>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">Role / Job Title</label>
                        <input type="text" name="role" id="edit-exp-role" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-4">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="start_date" id="edit-exp-start-date" class="form-control" required>
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">End Date</label>
                        <input type="text" name="end_date" id="edit-exp-end-date" class="form-control" required>
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Sort Order (optional)</label>
                        <input type="number" name="sort_order" id="edit-exp-sort-order" class="form-control" placeholder="Auto-increment">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description / Responsibilities</label>
                    <textarea name="description" id="edit-exp-description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group mt-3">
                    <label class="form-label">Upload New Attachments (Multiple Images)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                <div class="form-group mt-3" id="edit-exp-images-container" style="display: none;">
                    <label class="form-label">Current Attachments</label>
                    <div id="edit-exp-images-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <!-- Loaded dynamically via JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-exp')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. ACHIEVEMENT EDIT MODAL -->
<div id="modal-edit-ach" class="admin-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Achievement Record</h3>
            <button onclick="closeModal('modal-edit-ach')" class="modal-close">&times;</button>
        </div>
        <form id="form-edit-ach" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">Certificate / Award Title</label>
                        <input type="text" name="title" id="edit-ach-title" class="form-control" required>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">Issuer</label>
                        <input type="text" name="issuer" id="edit-ach-issuer" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-4">
                        <label class="form-label">Date</label>
                        <input type="text" name="date" id="edit-ach-date" class="form-control">
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Credential URL</label>
                        <input type="url" name="credential_url" id="edit-ach-credential-url" class="form-control">
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Sort Order (optional)</label>
                        <input type="number" name="sort_order" id="edit-ach-sort-order" class="form-control" placeholder="Auto-increment">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-ach')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. SKILL EDIT MODAL -->
<div id="modal-edit-skill" class="admin-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Skill Record</h3>
            <button onclick="closeModal('modal-edit-skill')" class="modal-close">&times;</button>
        </div>
        <form id="form-edit-skill" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-5">
                        <label class="form-label">Skill Name</label>
                        <input type="text" name="name" id="edit-skill-name" class="form-control" required>
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Category</label>
                        <select name="category" id="edit-skill-category" class="form-control" required>
                            <option value="Technical">Technical</option>
                            <option value="Soft Skill">Soft Skill</option>
                            <option value="Language">Language</option>
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label class="form-label">Sort Order (optional)</label>
                        <input type="number" name="sort_order" id="edit-skill-sort-order" class="form-control" placeholder="Auto-increment">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-skill')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 5. PROJECT EDIT MODAL -->
<div id="modal-edit-proj" class="admin-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Project Record</h3>
            <button onclick="closeModal('modal-edit-proj')" class="modal-close">&times;</button>
        </div>
        <form id="form-edit-proj" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">Project Title</label>
                        <input type="text" name="title" id="edit-proj-title" class="form-control" required>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">Project External URL</label>
                        <input type="url" name="project_url" id="edit-proj-url" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-4">
                        <label class="form-label">Add Project Images (optional, multiple)</label>
                        <input type="file" name="images[]" class="form-control file-input" accept="image/*" multiple>
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Update Project Video (optional)</label>
                        <input type="file" name="video" class="form-control file-input" accept="video/*" onchange="generateVideoThumbnail(this, 'edit-proj-video-thumbnail')">
                        <input type="hidden" name="video_thumbnail" id="edit-proj-video-thumbnail">
                        <div id="edit-proj-video-preview" style="margin-top: 8px; display: none;">
                            <!-- Current video thumbnail populated by JS -->
                        </div>
                    </div>
                    <div class="form-group col-4">
                        <label class="form-label">Sort Order (optional)</label>
                        <input type="number" name="sort_order" id="edit-proj-sort-order" class="form-control" placeholder="Auto-increment">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Existing Project Images (Click trash icon to delete)</label>
                    <div id="edit-proj-images-container" class="edit-proj-images-grid">
                        <!-- Dynamically populated via JS -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Project Description</label>
                    <textarea name="description" id="edit-proj-description" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-proj')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Mobile sidebar toggle logic
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menu-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.querySelector('.admin-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        
        if (menuToggle && sidebar && backdrop) {
            const toggleSidebar = () => {
                sidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
            };
            
            menuToggle.addEventListener('click', toggleSidebar);
            backdrop.addEventListener('click', toggleSidebar);
            if (sidebarClose) {
                sidebarClose.addEventListener('click', toggleSidebar);
            }
            
            // Auto close drawer when clicking a navigation link (tab)
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                });
            });
        }
    });

    // Bulk delete selection logic
    function toggleSelectAll(masterCheckbox, type) {
        const checkboxes = document.querySelectorAll('.child-checkbox-' + type);
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
        updateSelectedCount(type);
    }

    function toggleSelectAllProjects(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.child-checkbox-project');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
        updateSelectedCount('project');
    }

    function updateSelectedCount(type) {
        const checkedCount = document.querySelectorAll('.child-checkbox-' + type + ':checked').length;
        const bulkBar = document.getElementById('bulk-bar-' + type);
        const countSpan = document.getElementById('count-' + type);
        
        if (bulkBar && countSpan) {
            if (checkedCount > 0) {
                bulkBar.style.display = 'flex';
                countSpan.textContent = checkedCount + ' item(s) selected';
            } else {
                bulkBar.style.display = 'none';
            }
        }

        // Also synchronize master checkboxes
        if (type === 'project') {
            const master = document.getElementById('master-project-checkbox');
            const total = document.querySelectorAll('.child-checkbox-project').length;
            if (master) {
                master.checked = (checkedCount === total && total > 0);
            }
        } else {
            const table = document.querySelector('#bulk-bar-' + type).nextElementSibling;
            if (table) {
                const master = table.querySelector('.master-checkbox');
                const total = document.querySelectorAll('.child-checkbox-' + type).length;
                if (master) {
                    master.checked = (checkedCount === total && total > 0);
                }
            }
        }
    }

    // Tab switching logic
    function switchTab(evt, tabId) {
        // Get all elements with class="tab-pane" and hide them
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabPanes.forEach(pane => {
            pane.classList.remove('active');
        });

        // Get all elements with class="nav-tab" and remove the class "active"
        const navTabs = document.querySelectorAll('.nav-tab');
        navTabs.forEach(tab => {
            tab.classList.remove('active');
        });

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');

        // Update the header title
        document.getElementById('current-tab-title').textContent = evt.currentTarget.innerText.trim();
        
        // Save selected tab in sessionStorage for page reload persistence
        sessionStorage.setItem('activePortfolioTab', tabId);
        sessionStorage.setItem('activePortfolioTabTitle', evt.currentTarget.innerText.trim());
    }

    // Modal helpers
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // Edit modal triggers with auto-population
    function openEditEduModal(edu) {
        document.getElementById('form-edit-edu').action = '/education/' + edu.id;
        document.getElementById('edit-edu-institution').value = edu.institution;
        document.getElementById('edit-edu-degree').value = edu.degree;
        document.getElementById('edit-edu-major').value = edu.major || '';
        document.getElementById('edit-edu-gpa').value = edu.gpa || '';
        document.getElementById('edit-edu-sort-order').value = edu.sort_order;
        document.getElementById('edit-edu-start-date').value = edu.start_date;
        document.getElementById('edit-edu-end-date').value = edu.end_date;
        document.getElementById('edit-edu-description').value = edu.description || '';

        // Populate existing images
        const container = document.getElementById('edit-edu-images-list');
        container.innerHTML = '';
        if (edu.images && edu.images.length > 0) {
            document.getElementById('edit-edu-images-container').style.display = 'block';
            edu.images.forEach(img => {
                const wrapper = document.createElement('div');
                wrapper.className = 'edit-img-thumbnail-wrapper';
                wrapper.id = 'education-image-' + img.id;
                wrapper.innerHTML = `
                    <img src="${img.image_path}" class="edit-img-thumbnail">
                    <button type="button" class="btn-delete-image-overlay" onclick="deleteEducationImage(this, ${img.id})" title="Delete image">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `;
                container.appendChild(wrapper);
            });
        } else {
            document.getElementById('edit-edu-images-container').style.display = 'none';
        }

        openModal('modal-edit-edu');
    }

    function openEditExpModal(exp) {
        document.getElementById('form-edit-exp').action = '/experience/' + exp.id;
        document.getElementById('edit-exp-company').value = exp.company;
        document.getElementById('edit-exp-role').value = exp.role;
        document.getElementById('edit-exp-start-date').value = exp.start_date;
        document.getElementById('edit-exp-end-date').value = exp.end_date;
        document.getElementById('edit-exp-sort-order').value = exp.sort_order;
        document.getElementById('edit-exp-description').value = exp.description || '';

        // Populate existing images
        const container = document.getElementById('edit-exp-images-list');
        container.innerHTML = '';
        if (exp.images && exp.images.length > 0) {
            document.getElementById('edit-exp-images-container').style.display = 'block';
            exp.images.forEach(img => {
                const wrapper = document.createElement('div');
                wrapper.className = 'edit-img-thumbnail-wrapper';
                wrapper.id = 'experience-image-' + img.id;
                wrapper.innerHTML = `
                    <img src="${img.image_path}" class="edit-img-thumbnail">
                    <button type="button" class="btn-delete-image-overlay" onclick="deleteExperienceImage(this, ${img.id})" title="Delete image">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `;
                container.appendChild(wrapper);
            });
        } else {
            document.getElementById('edit-exp-images-container').style.display = 'none';
        }

        openModal('modal-edit-exp');
    }

    function openEditAchModal(ach) {
        document.getElementById('form-edit-ach').action = '/achievements/' + ach.id;
        document.getElementById('edit-ach-title').value = ach.title;
        document.getElementById('edit-ach-issuer').value = ach.issuer || '';
        document.getElementById('edit-ach-date').value = ach.date || '';
        document.getElementById('edit-ach-credential-url').value = ach.credential_url || '';
        document.getElementById('edit-ach-sort-order').value = ach.sort_order;
        openModal('modal-edit-ach');
    }

    function openEditSkillModal(skill) {
        document.getElementById('form-edit-skill').action = '/skills/' + skill.id;
        document.getElementById('edit-skill-name').value = skill.name;
        document.getElementById('edit-skill-category').value = skill.category;
        document.getElementById('edit-skill-sort-order').value = skill.sort_order;
        openModal('modal-edit-skill');
    }

    function openEditProjModal(proj) {
        document.getElementById('form-edit-proj').action = '/projects/' + proj.id;
        document.getElementById('edit-proj-title').value = proj.title;
        document.getElementById('edit-proj-url').value = proj.project_url || '';
        document.getElementById('edit-proj-sort-order').value = proj.sort_order;
        document.getElementById('edit-proj-description').value = proj.description;
        
        // Populate existing images
        const container = document.getElementById('edit-proj-images-container');
        container.innerHTML = '';
        if (proj.images && proj.images.length > 0) {
            proj.images.forEach(img => {
                const wrapper = document.createElement('div');
                wrapper.className = 'edit-img-thumbnail-wrapper';
                wrapper.id = 'project-image-' + img.id;
                wrapper.innerHTML = `
                    <img src="${img.image_path}" class="edit-img-thumbnail">
                    <button type="button" class="btn-delete-image-overlay" onclick="deleteProjectImage(this, ${img.id})" title="Delete image">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `;
                container.appendChild(wrapper);
            });
        } else {
            container.innerHTML = '<span class="text-muted">No images uploaded for this project yet.</span>';
        }

        // Video thumbnail preview
        const videoPreview = document.getElementById('edit-proj-video-preview');
        const hiddenVideoThumb = document.getElementById('edit-proj-video-thumbnail');
        hiddenVideoThumb.value = ''; // Reset hidden thumbnail input
        
        if (proj.video_thumbnail_path) {
            videoPreview.style.display = 'block';
            videoPreview.innerHTML = `
                <div style="position: relative; width: 120px; height: 75px; border-radius: 4px; overflow: hidden; border: 1px solid var(--admin-border);">
                    <img src="${proj.video_thumbnail_path}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-play" style="color: #fff; font-size: 1.2rem;"></i>
                    </div>
                </div>
                <span class="text-muted" style="font-size: 0.8rem; display: block; margin-top: 4px;">Current Video Cover</span>
            `;
        } else {
            videoPreview.style.display = 'none';
            videoPreview.innerHTML = '';
        }

        openModal('modal-edit-proj');
    }

    function generateVideoThumbnail(input, hiddenInputId) {
        const file = input.files[0];
        if (!file) return;

        // Check if file is a video
        if (!file.type.startsWith('video/')) return;

        const video = document.createElement('video');
        video.preload = 'metadata';
        video.muted = true;
        video.playsInline = true;
        video.src = URL.createObjectURL(file);

        video.onloadedmetadata = function() {
            // Seek to 0.5s to get a good frame
            video.currentTime = 0.5;
        };

        video.onseeked = function() {
            const canvas = document.createElement('canvas');
            // Maintain aspect ratio, set max width to 640px for good quality and reasonable payload size
            const scale = Math.min(1, 640 / video.videoWidth);
            canvas.width = video.videoWidth * scale;
            canvas.height = video.videoHeight * scale;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Get base64 jpeg data URL (quality = 0.8)
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            
            // Set dataUrl to hidden input
            document.getElementById(hiddenInputId).value = dataUrl;

            // Revoke object URL
            URL.revokeObjectURL(video.src);
            
            console.log('Video thumbnail generated successfully.');
        };

        video.onerror = function() {
            console.error('Error loading video file for thumbnail generation.');
        };
    }

    function deleteProjectImage(button, imageId) {
        if (!confirm('Are you sure you want to delete this image?')) return;
        
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('/project-images/' + imageId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const element = document.getElementById('project-image-' + imageId);
                element.style.transition = 'all 0.3s ease';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    element.remove();
                    // If no images left, display placeholder
                    const container = document.getElementById('edit-proj-images-container');
                    if (container.querySelectorAll('.edit-img-thumbnail-wrapper').length === 0) {
                        container.innerHTML = '<span class="text-muted">No images uploaded for this project yet.</span>';
                    }
                }, 300);
            } else {
                alert('Failed to delete image: ' + (data.message || 'Unknown error'));
                button.disabled = false;
                button.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting image. Please try again.');
            button.disabled = false;
            button.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
        });
    }

    function deleteEducationImage(button, imageId) {
        if (!confirm('Are you sure you want to delete this image?')) return;
        
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('/education-images/' + imageId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const element = document.getElementById('education-image-' + imageId);
                element.style.transition = 'all 0.3s ease';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    element.remove();
                    const container = document.getElementById('edit-edu-images-list');
                    if (container.querySelectorAll('.edit-img-thumbnail-wrapper').length === 0) {
                        document.getElementById('edit-edu-images-container').style.display = 'none';
                    }
                }, 300);
            } else {
                alert('Failed to delete image: ' + (data.message || 'Unknown error'));
                button.disabled = false;
                button.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting image. Please try again.');
            button.disabled = false;
            button.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
        });
    }

    function deleteExperienceImage(button, imageId) {
        if (!confirm('Are you sure you want to delete this image?')) return;
        
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('/experience-images/' + imageId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const element = document.getElementById('experience-image-' + imageId);
                element.style.transition = 'all 0.3s ease';
                element.style.opacity = '0';
                element.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    element.remove();
                    const container = document.getElementById('edit-exp-images-list');
                    if (container.querySelectorAll('.edit-img-thumbnail-wrapper').length === 0) {
                        document.getElementById('edit-exp-images-container').style.display = 'none';
                    }
                }, 300);
            } else {
                alert('Failed to delete image: ' + (data.message || 'Unknown error'));
                button.disabled = false;
                button.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting image. Please try again.');
            button.disabled = false;
            button.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
        });
    }

    // Persist active tab across page refreshes
    document.addEventListener('DOMContentLoaded', function() {
        const savedTab = sessionStorage.getItem('activePortfolioTab');
        const savedTitle = sessionStorage.getItem('activePortfolioTabTitle');
        if (savedTab) {
            // Find tab button
            const tabButton = Array.from(document.querySelectorAll('.nav-tab')).find(button => 
                button.getAttribute('onclick').includes(savedTab)
            );
            if (tabButton) {
                // Remove active classes
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
                
                // Set active
                document.getElementById(savedTab).classList.add('active');
                tabButton.classList.add('active');
                if (savedTitle) {
                    document.getElementById('current-tab-title').textContent = savedTitle;
                }
            }
        }
    });
</script>
@endsection

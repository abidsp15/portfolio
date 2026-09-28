@extends('layouts.app')

@section('title', $profile->name . ' - Portfolio')

@section('content')

@if($isPreview)
    <!-- Preview Banner -->
    <div class="preview-banner">
        <div class="preview-banner-content">
            <span><i class="fa-solid fa-eye"></i> You are viewing in <strong>Preview Mode</strong>.</span>
            <a href="{{ route('admin.dashboard') }}" class="btn-preview-back"><i class="fa-solid fa-gauge"></i> Return to Dashboard</a>
        </div>
    </div>
@endif

<div class="portfolio-wrapper {{ $isPreview ? 'with-preview-banner' : '' }}">
    <!-- Sidebar / Hero Header -->
    <header class="portfolio-sidebar">
        <div class="sidebar-sticky">
            <div class="profile-card">
                <div class="profile-image-container">
                    @if($profile->photo_path)
                        <img src="{{ asset($profile->photo_path) }}" alt="{{ $profile->name }}" class="profile-img">
                    @else
                        <div class="profile-avatar-fallback">
                            {{ substr($profile->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <h1 class="profile-name">{{ $profile->name }}</h1>
                <p class="profile-headline">{{ $profile->headline }}</p>
                
                <div class="contact-info">
                    <a href="mailto:{{ $profile->email }}" class="contact-item">
                        <i class="fa-regular fa-envelope"></i>
                        <span>{{ $profile->email }}</span>
                    </a>
                    <a href="tel:{{ $profile->phone }}" class="contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $profile->phone }}</span>
                    </a>
                </div>

                <div class="social-bar">
                    @if($profile->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" aria-label="LinkedIn" class="social-link">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    @endif
                    @if($profile->instagram_url)
                        <a href="{{ $profile->instagram_url }}" target="_blank" aria-label="Instagram" class="social-link">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    @endif
                </div>

                <div class="action-buttons">
                    <a href="{{ route('portfolio.export') }}" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-file-pdf"></i> Export PDF Resume
                    </a>
                    @if($profile->cv_path)
                        <a href="{{ asset($profile->cv_path) }}" target="_blank" class="btn btn-secondary btn-block mt-2">
                            <i class="fa-solid fa-download"></i> Download Original CV
                        </a>
                    @endif
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#about" class="nav-link active"><i class="fa-regular fa-user"></i> About</a></li>
                    <li><a href="#education" class="nav-link"><i class="fa-solid fa-graduation-cap"></i> Education</a></li>
                    <li><a href="#experience" class="nav-link"><i class="fa-solid fa-briefcase"></i> Experience</a></li>
                    <li><a href="#achievements" class="nav-link"><i class="fa-solid fa-award"></i> Achievements</a></li>
                    <li><a href="#skills" class="nav-link"><i class="fa-solid fa-screwdriver-wrench"></i> Skills</a></li>
                    <li><a href="#projects" class="nav-link"><i class="fa-solid fa-code"></i> Projects</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="admin-link"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                    <span class="divider">|</span>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="admin-link"><i class="fa-solid fa-lock"></i> Admin Login</a>
                @endauth
                <p class="copyright">&copy; {{ date('Y') }} {{ $profile->name }}</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="portfolio-main">
        
        <!-- About Section -->
        <section id="about" class="content-section">
            <h2 class="section-title">About Me</h2>
            <div class="section-content lead-text">
                {!! App\Helpers\TextFormatter::format($profile->about_text) !!}
            </div>
        </section>

        <!-- Education Section -->
        <section id="education" class="content-section">
            <h2 class="section-title">Education</h2>
            <div class="timeline">
                @forelse($education as $edu)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">{{ $edu->start_date }} - {{ $edu->end_date }}</div>
                        <div class="timeline-card">
                            <h3 class="timeline-header">{{ $edu->institution }}</h3>
                            <h4 class="timeline-subheader">
                                {{ $edu->degree }}@if($edu->major), {{ $edu->major }}@endif
                                @if($edu->gpa) <span class="gpa-tag">GPA: {{ $edu->gpa }}</span> @endif
                            </h4>
                            @if($edu->description)
                                <div class="timeline-body">
                                    {!! App\Helpers\TextFormatter::format($edu->description) !!}
                                </div>
                            @endif
                            @if($edu->images && $edu->images->count() > 0)
                                <div class="timeline-attachments" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
                                    @foreach($edu->images as $img)
                                        <a href="{{ asset($img->image_path) }}" target="_blank" class="attachment-preview-link">
                                            <img src="{{ asset($img->image_path) }}" alt="Certificate" style="max-width: 150px; max-height: 100px; border-radius: 6px; border: 1px solid var(--border-color); object-fit: cover;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="no-data">No education details added yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Experience Section -->
        <section id="experience" class="content-section">
            <h2 class="section-title">Working Experience</h2>
            <div class="timeline">
                @forelse($experiences as $exp)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">{{ $exp->start_date }} - {{ $exp->end_date }}</div>
                        <div class="timeline-card">
                            <h3 class="timeline-header">{{ $exp->company }}</h3>
                            <h4 class="timeline-subheader">{{ $exp->role }}</h4>
                            @if($exp->description)
                                <div class="timeline-body">
                                    {!! App\Helpers\TextFormatter::format($exp->description) !!}
                                </div>
                            @endif
                            @if($exp->images && $exp->images->count() > 0)
                                <div class="timeline-attachments" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
                                    @foreach($exp->images as $img)
                                        <a href="{{ asset($img->image_path) }}" target="_blank" class="attachment-preview-link">
                                            <img src="{{ asset($img->image_path) }}" alt="Documentation" style="max-width: 150px; max-height: 100px; border-radius: 6px; border: 1px solid var(--border-color); object-fit: cover;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="no-data">No experience details added yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Achievements Section -->
        <section id="achievements" class="content-section">
            <h2 class="section-title">Achievements & Certifications</h2>
            <div class="achievements-grid">
                @forelse($achievements as $ach)
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <div class="achievement-details">
                            <h3 class="achievement-title">{{ $ach->title }}</h3>
                            <p class="achievement-meta">
                                @if($ach->issuer) <span>{{ $ach->issuer }}</span> @endif
                                @if($ach->date) <span class="dot-separator">&middot;</span> <span>{{ $ach->date }}</span> @endif
                            </p>
                            @if($ach->credential_url)
                                <a href="{{ $ach->credential_url }}" target="_blank" class="achievement-link">
                                    View Credential <i class="fa-solid fa-up-right-from-square"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="no-data">No achievements added yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Skills Section -->
        <section id="skills" class="content-section">
            <h2 class="section-title">Skills & Languages</h2>
            <div class="skills-wrapper">
                @php
                    $groupedSkills = $skills->groupBy('category');
                @endphp

                @forelse($groupedSkills as $category => $skillList)
                    <div class="skill-category-block">
                        <h3 class="skill-category-title">{{ $category }}</h3>
                        <div class="skills-tags">
                            @foreach($skillList as $skill)
                                <span class="skill-tag">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="no-data">No skills details added yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Projects Section -->
        <section id="projects" class="content-section">
            <h2 class="section-title">Projects</h2>
            <div class="projects-grid">
                @forelse($projects as $project)
                    <div class="project-card">
                        <div class="project-media">
                            @if($project->video_path)
                                <div class="video-container">
                                    <video controls preload="metadata" class="project-video-element">
                                        <source src="{{ asset($project->video_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @elseif($project->images->count() > 1)
                                <div class="project-slider" id="slider-{{ $project->id }}">
                                    <div class="slider-slides">
                                        @foreach($project->images as $index => $img)
                                            <div class="slider-slide {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ asset($img->image_path) }}" alt="{{ $project->title }} - Image {{ $index + 1 }}" class="project-img">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="slider-arrow prev" onclick="moveSlide('{{ $project->id }}', -1)" aria-label="Previous image">&lt;</button>
                                    <button class="slider-arrow next" onclick="moveSlide('{{ $project->id }}', 1)" aria-label="Next image">&gt;</button>
                                    <div class="slider-dots">
                                        @foreach($project->images as $index => $img)
                                            <span class="slider-dot {{ $index === 0 ? 'active' : '' }}" onclick="setSlide('{{ $project->id }}', {{ $index }})"></span>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($project->images->count() === 1)
                                <img src="{{ asset($project->images->first()->image_path) }}" alt="{{ $project->title }}" class="project-img">
                            @elseif($project->image_path)
                                <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}" class="project-img">
                            @else
                                <div class="project-media-placeholder">
                                    <i class="fa-solid fa-code"></i>
                                </div>
                            @endif
                        </div>
                        <div class="project-info">
                            <h3 class="project-card-title">{{ $project->title }}</h3>
                            <div class="project-card-desc">
                                {!! App\Helpers\TextFormatter::format($project->description) !!}
                            </div>
                            
                            @if($project->project_url)
                                <a href="{{ $project->project_url }}" target="_blank" class="btn btn-outline btn-sm">
                                    Visit Project <i class="fa-solid fa-up-right-from-square"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="no-data">No projects added yet.</p>
                @endforelse
            </div>
        </section>

    </main>
</div>

@endsection

@section('scripts')
<script>
    // Smooth scrolling & active navigation indicator on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
        
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').substring(1) === current) {
                    link.classList.add('active');
                }
            });
        });
    });

    // Slider functionality
    const sliders = {};

    window.moveSlide = function(projectId, direction) {
        if (!sliders[projectId]) {
            sliders[projectId] = { current: 0 };
        }
        
        const sliderEl = document.getElementById('slider-' + projectId);
        if (!sliderEl) return;

        const slides = sliderEl.querySelectorAll('.slider-slide');
        const dots = sliderEl.querySelectorAll('.slider-dot');
        let currentIdx = sliders[projectId].current;

        // Hide current
        slides[currentIdx].classList.remove('active');
        dots[currentIdx].classList.remove('active');

        // Calculate new index
        currentIdx = (currentIdx + direction + slides.length) % slides.length;
        sliders[projectId].current = currentIdx;

        // Show new
        slides[currentIdx].classList.add('active');
        dots[currentIdx].classList.add('active');
    };

    window.setSlide = function(projectId, index) {
        if (!sliders[projectId]) {
            sliders[projectId] = { current: 0 };
        }

        const sliderEl = document.getElementById('slider-' + projectId);
        if (!sliderEl) return;

        const slides = sliderEl.querySelectorAll('.slider-slide');
        const dots = sliderEl.querySelectorAll('.slider-dot');
        let currentIdx = sliders[projectId].current;

        // Hide current
        slides[currentIdx].classList.remove('active');
        dots[currentIdx].classList.remove('active');

        // Set and show new index
        currentIdx = index;
        sliders[projectId].current = currentIdx;

        slides[currentIdx].classList.add('active');
        dots[currentIdx].classList.add('active');
    };
</script>
@endsection

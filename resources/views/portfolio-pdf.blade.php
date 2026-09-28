<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portfolio - {{ $profile->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap');
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 9.5pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        a {
            color: #4f46e5;
            text-decoration: none;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e7ff;
            padding-bottom: 15px;
        }
        .name {
            font-family: 'Montserrat', sans-serif;
            font-size: 24pt;
            font-weight: 800;
            color: #4f46e5;
            margin: 0 0 4px 0;
            letter-spacing: -0.5px;
        }
        .headline {
            font-family: 'Montserrat', sans-serif;
            font-size: 11pt;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
        }
        .contact-info {
            font-size: 8.5pt;
            color: #4b5563;
        }
        .contact-item {
            margin-right: 15px;
            margin-bottom: 4px;
            display: inline-block;
        }
        .profile-photo-container {
            text-align: right;
            vertical-align: top;
            width: 100px;
        }
        .profile-photo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid #818cf8;
            object-fit: cover;
        }
        .section {
            margin-bottom: 22px;
        }
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 11pt;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e0e7ff;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }
        .section-content {
            margin-left: 5px;
        }
        .item {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .item-meta {
            font-size: 9.5pt;
            color: #4b5563;
            margin-bottom: 4px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        .item-meta .date {
            float: right;
            font-weight: 600;
            color: #6b7280;
        }
        .item-header {
            font-size: 9pt;
            font-weight: 500;
            color: #4b5563;
            margin-top: -2px;
            font-style: italic;
        }
        .item-body {
            color: #374151;
            text-align: justify;
            margin-top: 5px;
        }
        .gpa {
            font-weight: 600;
            color: #111827;
        }
        
        /* Skills section badges */
        .skills-table {
            width: 100%;
            border-collapse: collapse;
        }
        .skills-category {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 9pt;
            width: 25%;
            vertical-align: top;
            padding: 6px 0;
            color: #1f2937;
        }
        .skills-list {
            width: 75%;
            vertical-align: top;
            padding: 4px 0;
        }
        .skill-badge {
            display: inline-block;
            background-color: #f5f3ff;
            color: #5b21b6;
            border: 1px solid #ddd6fe;
            padding: 3px 8px;
            font-size: 8pt;
            font-weight: 500;
            border-radius: 8px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        /* Achievement list styling */
        .achievement-item {
            margin-bottom: 8px;
            page-break-inside: avoid;
            background-color: #f9fafb;
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid #818cf8;
        }
        .achievement-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #1f2937;
            font-size: 9pt;
        }
        .achievement-issuer {
            font-size: 8.5pt;
            color: #4b5563;
            font-weight: 500;
        }
        .achievement-date {
            float: right;
            font-weight: 600;
            font-size: 9pt;
            color: #6b7280;
        }
        /* Bullet list inside PDF */
        .bullet-list {
            margin: 4px 0 6px 15px;
            padding-left: 0;
            list-style-type: disc;
        }
        .bullet-list li {
            margin-bottom: 3px;
            color: #4b5563;
            font-size: 9pt;
        }
        
        /* Video project overlay styles */
        .video-thumb-container {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 80px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        .video-thumb-image {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            display: block;
        }
        .video-play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.35);
            text-align: center;
        }
        .video-play-button {
            display: inline-block;
            width: 28px;
            height: 28px;
            line-height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: #1f2937;
            font-size: 10pt;
            margin-top: 26px; /* center vertically (80px height - 28px) / 2 */
            text-align: center;
        }
        }
        
        /* Premium Project Link Badge */
        .project-link {
            display: inline-block;
            background-color: #f5f3ff;
            color: #5b21b6;
            border: 1px solid #ddd6fe;
            padding: 3px 8px;
            font-size: 7.5pt;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <h1 class="name">{{ $profile->name }}</h1>
                <div class="headline">{{ $profile->headline }}</div>
                <div class="contact-info">
                    <span class="contact-item"><strong>Email:</strong> {{ $profile->email }}</span>
                    <span class="contact-item"><strong>Phone:</strong> {{ $profile->phone }}</span>
                    <br>
                    @if($profile->linkedin_url)
                        <span class="contact-item"><strong>LinkedIn:</strong> <a href="{{ $profile->linkedin_url }}" target="_blank" style="color: #4f46e5; text-decoration: underline;">linkedin.com/in/{{ basename($profile->linkedin_url) }}</a></span>
                    @endif
                    @if($profile->instagram_url)
                        <span class="contact-item"><strong>Instagram:</strong> <a href="{{ $profile->instagram_url }}" target="_blank" style="color: #4f46e5; text-decoration: underline;">{{ '@' . basename($profile->instagram_url) }}</a></span>
                    @endif
                </div>
            </td>
            @if($profile->photo_path && file_exists(public_path(ltrim($profile->photo_path, '/'))))
                <td class="profile-photo-container">
                    <img src="{{ public_path(ltrim($profile->photo_path, '/')) }}" class="profile-photo">
                </td>
            @endif
        </tr>
    </table>

    <!-- Summary / About Section -->
    <div class="section">
        <div class="section-title">Summary</div>
        <div class="section-content">
            <div class="item-body">
                {!! App\Helpers\TextFormatter::format($profile->about_text) !!}
            </div>
        </div>
    </div>

    <!-- Experience Section -->
    <div class="section">
        <div class="section-title">Working Experience</div>
        <div class="section-content">
            @foreach($experiences as $exp)
                <div class="item">
                    <div class="item-meta">
                        <strong>{{ $exp->company }}</strong>
                        <span class="date">{{ $exp->start_date }} – {{ $exp->end_date }}</span>
                    </div>
                    <div class="item-header">
                        {{ $exp->role }}
                    </div>
                    @if($exp->description)
                        <div class="item-body">
                            {!! App\Helpers\TextFormatter::format($exp->description) !!}
                        </div>
                    @endif
                    @php
                        $validExpImages = $exp->images->filter(function($img) {
                            return file_exists(public_path(ltrim($img->image_path, '/')));
                        });
                    @endphp
                    @if($validExpImages->count() > 0)
                        <div style="margin-top: 10px; margin-bottom: 5px;">
                            @foreach($validExpImages as $img)
                                <div style="display: inline-block; margin-right: 12px; margin-bottom: 8px; vertical-align: top; page-break-inside: avoid;">
                                    <img src="{{ public_path(ltrim($img->image_path, '/')) }}" style="max-height: 220px; max-width: 320px; border-radius: 6px; border: 1px solid #cbd5e1; display: block;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Education Section -->
    <div class="section">
        <div class="section-title">Education</div>
        <div class="section-content">
            @foreach($education as $edu)
                <div class="item">
                    <div class="item-meta">
                        <strong>{{ $edu->institution }}</strong>
                        <span class="date">{{ $edu->start_date }} – {{ $edu->end_date }}</span>
                    </div>
                    <div class="item-header">
                        {{ $edu->degree }}@if($edu->major), {{ $edu->major }}@endif
                        @if($edu->gpa) <span class="gpa">(GPA: {{ $edu->gpa }})</span> @endif
                    </div>
                    @if($edu->description)
                        <div class="item-body">
                            {!! App\Helpers\TextFormatter::format($edu->description) !!}
                        </div>
                    @endif
                    @php
                        $validEduImages = $edu->images->filter(function($img) {
                            return file_exists(public_path(ltrim($img->image_path, '/')));
                        });
                    @endphp
                    @if($validEduImages->count() > 0)
                        <div style="margin-top: 10px; margin-bottom: 5px;">
                            @foreach($validEduImages as $img)
                                <div style="display: inline-block; margin-right: 12px; margin-bottom: 8px; vertical-align: top; page-break-inside: avoid;">
                                    <img src="{{ public_path(ltrim($img->image_path, '/')) }}" style="max-height: 220px; max-width: 320px; border-radius: 6px; border: 1px solid #cbd5e1; display: block;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Skills Section -->
    <div class="section">
        <div class="section-title">Skills & Languages</div>
        <div class="section-content">
            <table class="skills-table">
                @php
                    $groupedSkills = $skills->groupBy('category');
                @endphp
                @foreach($groupedSkills as $category => $skillList)
                    <tr>
                        <td class="skills-category">{{ $category }}</td>
                        <td class="skills-list">
                            @foreach($skillList as $sk)
                                <span class="skill-badge">{{ $sk->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Achievements Section -->
    @if($achievements->count() > 0)
        <div class="section">
            <div class="section-title">Achievements & Certifications</div>
            <div class="section-content">
                @foreach($achievements as $ach)
                    <div class="achievement-item">
                        @if($ach->date)
                            <span class="achievement-date">{{ $ach->date }}</span>
                        @endif
                        @if($ach->credential_url)
                            <span class="achievement-title"><a href="{{ $ach->credential_url }}" target="_blank" style="color: #4f46e5; text-decoration: underline;">{{ $ach->title }}</a></span>
                        @else
                            <span class="achievement-title">{{ $ach->title }}</span>
                        @endif
                        @if($ach->issuer)
                            <span class="achievement-issuer">({{ $ach->issuer }})</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Projects Section -->
    @if($projects->count() > 0)
        <div class="section">
            <div class="section-title">Projects</div>
            <div class="section-content">
                @foreach($projects as $project)
                    <div class="item">
                        <div class="item-meta" style="margin-bottom: 2px;">
                            @if($project->project_url)
                                <span class="date">
                                    <a href="{{ $project->project_url }}" target="_blank" class="project-link">
                                        View Project <span style="font-size: 7.5pt; font-weight: normal; margin-left: 2px;">&#x2197;</span>
                                    </a>
                                </span>
                            @endif
                            <strong>{{ $project->title }}</strong>
                        </div>
                        <div class="item-body" style="margin-top: 5px;">
                            {!! App\Helpers\TextFormatter::format($project->description) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</body>
</html>

<?php
$studentname = $user->user_information->lastname . ' ' . $user->user_information->firstname;
$lastname = $user->user_information->lastname;
$user_avatar = strtoupper(substr($user->user_information->firstname, 0, 1) . substr($user->user_information->lastname, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="student-id" content="{{ $studentId ?? '' }}">
    
    <title>EVSU Ormoc - Student Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student/dashboard.css') }}">
</head>
<body>
    <!-- Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="header-container">
                <div class="brand-section">
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="brand-logo">
                        E
                    </div>
                    
                    <div class="brand-text">
                        <h4>EVSU Student Dashboard</h4>
                        <p>Ormoc Campus • Academic Portal</p>
                    </div>
                </div>
                
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search courses, assignments...">
                    </div>
                    
                    <button class="notification-btn">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    
                    <div class="user-profile">
                        <div class="user-avatar">
                            {{$user_avatar}}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ $studentname }}</div>
                            <div class="user-role">BS Informtion Technology</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h5 class="sidebar-title">Student Navigation</h5>
                <p class="sidebar-subtitle">Access all academic tools</p>
            </div>
            
            <div class="nav-menu">
                <div class="nav-section">
                    <div class="nav-label">Main</div>
                    <div class="nav-item">
                        <a class="nav-link active" data-section="dashboard">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="profile">
                            <i class="fas fa-user-graduate nav-icon"></i>
                            <span>My Profile</span>
                        </a>
                    </div>
                    <!-- <div class="nav-item">
                        <a class="nav-link" data-section="schedule">
                            <i class="fas fa-calendar-alt nav-icon"></i>
                            <span>Schedule</span>
                            <span class="nav-badge">New</span>
                        </a>
                    </div> -->
                </div>
                
                <div class="nav-section">
                    <div class="nav-label">Academic</div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="courses">
                            <i class="fas fa-book-open nav-icon"></i>
                            <span>Courses</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="grades">
                            <i class="fas fa-chart-line nav-icon"></i>
                            <span>Grades</span>
                        </a>
                    </div>
                    <!-- <div class="nav-item">
                        <a class="nav-link" data-section="assignments">
                            <i class="fas fa-tasks nav-icon"></i>
                            <span>Assignments</span>
                            <span class="nav-badge">5</span>
                        </a>
                    </div> -->
                    <!-- <div class="nav-item">
                        <a class="nav-link" data-section="exams">
                            <i class="fas fa-file-alt nav-icon"></i>
                            <span>Exams</span>
                        </a>
                    </div> -->
                </div>
                
                <!-- <div class="nav-section">
                    <div class="nav-label">Resources</div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="elibrary">
                            <i class="fas fa-book nav-icon"></i>
                            <span>E-Library</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="finances">
                            <i class="fas fa-file-invoice-dollar nav-icon"></i>
                            <span>Finances</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="clubs">
                            <i class="fas fa-users nav-icon"></i>
                            <span>Student Clubs</span>
                        </a>
                    </div>
                </div> -->
                
                <div class="nav-section">
                    <div class="nav-label">Support</div>
                    <!-- <div class="nav-item">
                        <a class="nav-link" data-section="help">
                            <i class="fas fa-headset nav-icon"></i>
                            <span>Help Center</span>
                        </a>
                    </div> -->
                    <!-- <div class="nav-item">
                        <a class="nav-link" data-section="settings">
                            <i class="fas fa-cog nav-icon"></i>
                            <span>Settings</span>
                        </a>
                    </div> -->
                    @auth('student')
                        <div class="nav-item">
                            <a onclick="logout(event)" style="text-decoration: none; cursor: pointer" class="text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Content -->
            <section id="dashboard-content" class="content-section active">
                <!-- Welcome Section -->
                <section class="welcome-section">
                    <div class="welcome-content">
                        <h1>Welcome back</h1>
                        <input type="hidden" class="user-name2" value="{{ $lastname }}">
                        <!-- <p>You have 2 upcoming assignments and 1 quiz this week. Your next class starts in 45 minutes.</p> -->
                        
                        <div class="welcome-stats">
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>
                                        @if($hasEnrolledSubjects && isset($currentYearGWA))
                                            {{ number_format($currentYearGWA, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </h3>
                                    <p>Current GPA</p>
                                    @if($hasEnrolledSubjects && isset($gwa) && $gwa > 0)
                                        <!-- <small class="text-muted" style="font-size: 0.8rem; display: block; margin-top: 5px;">
                                            Overall: {{ number_format($gwa, 2) }}
                                        </small> -->
                                    @endif
                                </div>
                            </div>
                            
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>{{ $currentYearSubjects ?? 0 }}</h3>
                                    <p>Subjects</p>
                                    @if($hasEnrolledSubjects && isset($totalSubjects) && $totalSubjects > 0)
                                        <!-- <small class="text-muted" style="font-size: 0.8rem; display: block; margin-top: 5px;">
                                            Total: {{ $totalSubjects }} subjects
                                        </small> -->
                                    @endif
                                </div>
                            </div>
                            
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>{{ $currentYearUnits ?? 0 }}</h3>
                                    <p>Units Enrolled</p>
                                    @if($hasEnrolledSubjects && isset($totalUnits) && $totalUnits > 0)
                                        <!-- <small class="text-muted" style="font-size: 0.8rem; display: block; margin-top: 5px;">
                                            Total: {{ $totalUnits }} units
                                        </small> -->
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Dashboard Grid -->
                <div class="dashboard-grid">
                    
                    <!-- Campus Announcements -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-bullhorn"></i> 
                                @if($hasActiveWarnings)
                                    <span class="text-danger">Academic Warnings & Notices</span>
                                    <span class="badge bg-danger ms-2">{{ $warningCount }}</span>
                                @else
                                    Campus Announcements
                                @endif
                            </h5>
                            <div>
                                @if($hasActiveWarnings)
                                    <button class="btn btn-sm btn-outline-warning me-2" id="acknowledgeAllWarnings">
                                        <i class="fas fa-check-circle"></i> Acknowledge All
                                    </button>
                                @endif
                                <a href="#" class="card-link">View All <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                        
                        <div class="announcements-list">
                            @if($hasActiveWarnings)
                                {{-- Display Active Warnings First --}}
                                @foreach($activeWarnings as $warning)
                                    @php
                                        $warningType = $warning->warning_type;
                                        $warningClass = '';
                                        $iconClass = '';
                                        
                                        if($warningType == 'First Warning') {
                                            $warningClass = 'warning-low';
                                            $iconClass = 'fa-exclamation-circle text-warning';
                                        } elseif($warningType == 'Second Warning') {
                                            $warningClass = 'warning-medium';
                                            $iconClass = 'fa-exclamation-triangle text-warning';
                                        } elseif($warningType == 'Final Warning') {
                                            $warningClass = 'warning-high';
                                            $iconClass = 'fa-times-circle text-danger';
                                        }
                                        
                                        // Calculate days since issued
                                        $issuedDate = \Carbon\Carbon::parse($warning->issued_date);
                                        $daysAgo = $issuedDate->diffInDays(now());
                                        $dateText = $daysAgo == 0 ? 'Today' : ($daysAgo == 1 ? 'Yesterday' : $daysAgo . ' days ago');
                                    @endphp
                                    
                                    <div class="announcement-item {{ $warningClass }} warning-item" data-warning-id="{{ $warning->id }}">
                                        <div class="announcement-header">
                                            <div class="announcement-title">
                                                <i class="fas {{ $iconClass }} me-2"></i>
                                                <strong>{{ $warningType }}</strong>
                                            </div>
                                            <div class="announcement-date">{{ $dateText }}</div>
                                        </div>
                                        <div class="announcement-content">
                                            {{ $warning->reason }}
                                            @if($warning->expiry_date)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i> 
                                                    Valid until: {{ \Carbon\Carbon::parse($warning->expiry_date)->format('M d, Y') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="announcement-actions">
                                            <button class="btn btn-sm btn-outline-success btn-acknowledge" data-warning-id="{{ $warning->id }}">
                                                <i class="fas fa-check"></i> Acknowledge
                                            </button>
                                            <button class="btn btn-sm btn-outline-info btn-view-details" data-warning-id="{{ $warning->id }}">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                
                                {{-- Separator --}}
                                <hr class="my-3">
                            @endif
                            
                            {{-- Incomplete Grades Notices --}}
                            @if($incompleteGrades && $incompleteGrades->count() > 0)
                                @foreach($incompleteGrades as $inc)
                                    @php
                                        $daysRemaining = $inc['days_remaining'];
                                        $warningClass = $daysRemaining !== null && $daysRemaining < 30 ? 'warning-medium' : 'warning-low';
                                        $dateText = \Carbon\Carbon::parse($inc['completion_deadline'])->format('M d, Y');
                                    @endphp
                                    
                                    <div class="announcement-item {{ $warningClass }}" data-incomplete-id="{{ $inc['id'] }}">
                                        <div class="announcement-header">
                                            <div class="announcement-title">
                                                <i class="fas fa-hourglass-half text-info me-2"></i>
                                                <strong>Incomplete Grade: {{ $inc['subject_code'] }}</strong>
                                            </div>
                                            <div class="announcement-date">Due: {{ $dateText }}</div>
                                        </div>
                                        <div class="announcement-content">
                                            Subject: {{ $inc['subject_name'] }}
                                            @if($daysRemaining !== null)
                                                <br>
                                                <small class="{{ $daysRemaining < 0 ? 'text-danger' : ($daysRemaining < 30 ? 'text-warning' : 'text-muted') }}">
                                                    <i class="fas fa-clock"></i> 
                                                    @if($daysRemaining < 0)
                                                        <strong>OVERDUE by {{ abs($daysRemaining) }} days</strong>
                                                    @elseif($daysRemaining == 0)
                                                        <strong>Due today</strong>
                                                    @else
                                                        {{ $daysRemaining }} days remaining
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                        <div class="announcement-actions">
                                            <button class="btn btn-sm btn-outline-primary btn-submit-completion" data-incomplete-id="{{ $inc['id'] }}">
                                                <i class="fas fa-upload"></i> Submit
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($incompleteGrades->count() > 0)
                                    <hr class="my-3">
                                @endif
                            @endif
                            
                            {{-- Probation Notice --}}
                            @if($hasProbation)
                                @php
                                    $probation = $probationStatus;
                                    $startDate = \Carbon\Carbon::parse($probation->start_date);
                                    $endDate = $probation->end_date ? \Carbon\Carbon::parse($probation->end_date) : null;
                                    $daysInProbation = $startDate->diffInDays(now());
                                    $dateText = $daysInProbation == 0 ? 'Today' : $daysInProbation . ' days ago';
                                @endphp
                                
                                <div class="announcement-item warning-high">
                                    <div class="announcement-header">
                                        <div class="announcement-title">
                                            <i class="fas fa-user-graduate text-danger me-2"></i>
                                            <strong>Academic Probation</strong>
                                        </div>
                                        <div class="announcement-date">{{ $dateText }}</div>
                                    </div>
                                    <div class="announcement-content">
                                        You are currently on academic probation.
                                        @if($probation->reason)
                                            <br><small>Reason: {{ $probation->reason }}</small>
                                        @endif
                                        @if($probation->credit_limit)
                                            <br><small><i class="fas fa-info-circle"></i> Credit limit: {{ $probation->credit_limit }} units</small>
                                        @endif
                                        @if($endDate)
                                            <br><small><i class="far fa-calendar"></i> Review date: {{ $endDate->format('M d, Y') }}</small>
                                        @endif
                                    </div>
                                    <div class="announcement-actions">
                                        <button class="btn btn-sm btn-outline-warning btn-view-probation">
                                            <i class="fas fa-file-contract"></i> View Terms
                                        </button>
                                        @if($endDate && now()->diffInDays($endDate, false) < 30)
                                            <button class="btn btn-sm btn-outline-info btn-request-review">
                                                <i class="fas fa-handshake"></i> Request Review
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <hr class="my-3">
                            @endif
                            
                            {{-- Regular Campus Announcements --}}
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    <div class="announcement-title">Final Exam Schedule</div>
                                    <div class="announcement-date">Today</div>
                                </div>
                                <div class="announcement-content">
                                    The final examination schedule for the 2nd semester is now available on the student portal.
                                </div>
                            </div>
                            
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    <div class="announcement-title">Scholarship Applications</div>
                                    <div class="announcement-date">2 days ago</div>
                                </div>
                                <div class="announcement-content">
                                    Applications for academic scholarships for next school year are now open until April 30.
                                </div>
                            </div>
                            
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    <div class="announcement-title">University Week</div>
                                    <div class="announcement-date">5 days ago</div>
                                </div>
                                <div class="announcement-content">
                                    Join us for EVSU Ormoc Campus University Week celebration from April 10-14, 2024.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Academic Progress -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-chart-bar"></i> Academic Progress</h5>
                            @if($hasEnrolledSubjects)
                                <a href="#" class="card-link" data-bs-toggle="modal" data-bs-target="#allSubjectsModal">Details <i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="card-link disabled">Details <i class="fas fa-chevron-right"></i></span>
                            @endif
                        </div>
                        
                        <div class="progress-chart">
                            @if($hasEnrolledSubjects && count($academicProgress) > 0)
                                @foreach($academicProgress as $progress)
                                    <div class="progress-item">
                                        <div class="progress-header">
                                            <span class="progress-label" title="{{ $progress['subject_name'] }}">
                                                {{ Str::limit($progress['subject_name'], 20) }}
                                            </span>
                                            <span class="progress-value">
                                                @if($progress['numeric_grade'] > 0)
                                                    {{ $progress['grade'] }}
                                                @else
                                                    {{ $progress['grade'] }}
                                                @endif
                                            </span>
                                        </div>
                                        <!-- This should be in your HTML/template file -->
                                        <div class="progress-bar">
                                            <div class="progress-fill" 
                                                style="width: {{ $progress['progress_percentage'] }}%; 
                                                        background-color: {{ $progress['color'] }}; 
                                                        opacity: {{ $progress['numeric_grade'] <= 3.0 ? '1' : '0.7' }};">
                                            </div>
                                        </div>

                                        <div class="progress-meta small text-muted mt-1">
                                            <span>{{ $progress['subject_code'] }}</span>
                                            <span class="float-end">{{ $progress['units'] }} units</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                                    <h6 class="text-gray-500">No Academic Data</h6>
                                    <p class="text-muted small">
                                        @if($hasEnrolledSubjects)
                                            No graded subjects available for progress tracking.
                                        @else
                                            Enroll in subjects to see your academic progress.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        @if($hasEnrolledSubjects && $academicProgress->count() > 0)
                            <div class="card-footer bg-transparent border-top-0 pt-0">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle text-info"></i> 
                                            Showing {{ $academicProgress->count() }} of {{ $enrolledSubjects->count() }} subjects
                                        </small>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small>
                                            <span class="badge bg-success">1.0-1.5</span>
                                            <span class="badge bg-primary">1.6-2.5</span>
                                            <span class="badge bg-warning">2.6-3.0</span>
                                            <span class="badge bg-danger">4.0-5.0</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="dashboard-grid">
                    <!-- Upcoming Assignments -->
                    <!-- <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-tasks"></i> Upcoming Assignments</h5>
                            <a href="#" class="card-link">View All <i class="fas fa-chevron-right"></i></a>
                        </div>
                        
                        <div class="assignments-list">
                            <div class="assignment-item">
                                <div class="assignment-icon icon-high">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="assignment-details">
                                    <div class="assignment-title">Web Dev Project - Phase 2</div>
                                    <div class="assignment-info">Web Development • Prof. Garcia</div>
                                </div>
                                <div class="assignment-deadline">Due Tomorrow</div>
                            </div>
                            
                            <div class="assignment-item">
                                <div class="assignment-icon icon-medium">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="assignment-details">
                                    <div class="assignment-title">Database Normalization</div>
                                    <div class="assignment-info">Database Systems • Prof. Santos</div>
                                </div>
                                <div class="assignment-deadline">Due March 25</div>
                            </div>
                            
                            <div class="assignment-item">
                                <div class="assignment-icon icon-low">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div class="assignment-details">
                                    <div class="assignment-title">Algorithm Analysis</div>
                                    <div class="assignment-info">Data Structures • Prof. Cruz</div>
                                </div>
                                <div class="assignment-deadline">Due March 28</div>
                            </div>
                        </div>
                    </div> -->
                    
                    <!-- Today's Schedule -->
                    <!-- <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-calendar-day"></i> Today's Schedule</h5>
                            <a href="#" class="card-link">View All <i class="fas fa-chevron-right"></i></a>
                        </div>
                        
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="time-badge">08:00 AM</div>
                                <div class="class-details">
                                    <div class="class-name">Web Development</div>
                                    <div class="class-info">Room: IT-101 • Prof. Garcia</div>
                                </div>
                                <div class="class-status status-current">Ongoing</div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="time-badge">10:30 AM</div>
                                <div class="class-details">
                                    <div class="class-name">Database Systems</div>
                                    <div class="class-info">Room: CS Lab 2 • Prof. Santos</div>
                                </div>
                                <div class="class-status status-upcoming">Up Next</div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="time-badge">01:00 PM</div>
                                <div class="class-details">
                                    <div class="class-name">Software Engineering</div>
                                    <div class="class-info">Room: Main 304 • Prof. Reyes</div>
                                </div>
                                <div class="class-status status-upcoming">Later</div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="time-badge">03:30 PM</div>
                                <div class="class-details">
                                    <div class="class-name">Networking</div>
                                    <div class="class-info">Room: Tech 202 • Prof. Lim</div>
                                </div>
                                <div class="class-status status-upcoming">Later</div>
                            </div>
                        </div>
                    </div> -->
                </div>
                
                <!-- Quick Links -->
                <!-- <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-bolt"></i> Quick Links</h5>
                    </div>
                    
                    <div class="quick-links">
                        <a href="#" class="quick-link">
                            <div class="quick-link-icon" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="quick-link-label">Forms</div>
                        </a>
                        
                        <a href="#" class="quick-link">
                            <div class="quick-link-icon" style="background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="quick-link-label">E-Library</div>
                        </a>
                        
                        <a href="#" class="quick-link">
                            <div class="quick-link-icon" style="background: linear-gradient(135deg, var(--warning) 0%, #fbbf24 100%);">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="quick-link-label">Events</div>
                        </a>
                        
                        <a href="#" class="quick-link">
                            <div class="quick-link-icon" style="background: linear-gradient(135deg, var(--accent) 0%, #a78bfa 100%);">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="quick-link-label">Support</div>
                        </a>
                        
                        <a href="#" class="quick-link">
                            <div class="quick-link-icon" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="quick-link-label">Finances</div>
                        </a>
                        
                        <a href="#" class="quick-link">
                            <div class="quick-link-icon" style="background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="quick-link-label">Clubs</div>
                        </a>
                    </div>
                </div> -->
            </section>

            <!-- Profile Content -->
            <section id="profile-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">My Profile</h2>
                    <button class="btn-primary">Edit Profile</button>
                </div>
                
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">JD</div>
                        <div class="profile-info">
                            <h3>Carl James Duallo</h3>
                            <p>BS Information Technology - 3rd Year</p>
                            <p>Student ID: 2021-00123</p>
                            <p>EVSU Ormoc Campus</p>
                        </div>
                    </div>
                    
                    <div class="profile-details">
                        <div>
                            <div class="detail-item">
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value">carljames.duallo@evsu.edu.ph</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Phone Number</div>
                                <div class="detail-value">+63 912 345 6789</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Date of Birth</div>
                                <div class="detail-value">May 15, 2002</div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="detail-item">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">123 Ormoc City, Leyte</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Enrollment Status</div>
                                <div class="detail-value">Regular Student</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-history"></i> Academic History</h5>
                    </div>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="time-badge">2023-Present</div>
                            <div class="class-details">
                                <div class="class-name">BS Information Technology</div>
                                <div class="class-info">Third Year Standing • Current GPA: 3.78</div>
                            </div>
                            <div class="class-status status-current">Active</div>
                        </div>
                        <div class="timeline-item">
                            <div class="time-badge">2021-2023</div>
                            <div class="class-details">
                                <div class="class-name">BS Information Technology</div>
                                <div class="class-info">First & Second Year • Cumulative GPA: 3.65</div>
                            </div>
                            <div class="class-status status-upcoming">Completed</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Schedule Content -->
            <section id="schedule-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Class Schedule</h2>
                    <div>
                        <button class="btn-outline" style="margin-right: 10px;">Weekly View</button>
                        <button class="btn-primary">Monthly View</button>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-calendar-week"></i> This Week's Schedule</h5>
                        <a href="#" class="card-link">Download Schedule <i class="fas fa-download"></i></a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="grade-table">
                            <thead>
                                <tr>
                                    <th>Day/Time</th>
                                    <th>08:00-09:30</th>
                                    <th>10:00-11:30</th>
                                    <th>13:00-14:30</th>
                                    <th>15:00-16:30</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Monday</strong></td>
                                    <td>Web Development<br><small>IT-101</small></td>
                                    <td>Database Systems<br><small>CS Lab 2</small></td>
                                    <td>Software Engineering<br><small>Main 304</small></td>
                                    <td>Networking<br><small>Tech 202</small></td>
                                </tr>
                                <tr>
                                    <td><strong>Tuesday</strong></td>
                                    <td>Data Structures<br><small>CS Lab 1</small></td>
                                    <td>Web Development<br><small>IT-101</small></td>
                                    <td colspan="2" style="text-align: center; color: var(--gray);">No Class</td>
                                </tr>
                                <tr>
                                    <td><strong>Wednesday</strong></td>
                                    <td>Database Systems<br><small>CS Lab 2</small></td>
                                    <td>Software Engineering<br><small>Main 304</small></td>
                                    <td>Networking<br><small>Tech 202</small></td>
                                    <td>Data Structures<br><small>CS Lab 1</small></td>
                                </tr>
                                <tr>
                                    <td><strong>Thursday</strong></td>
                                    <td>Web Development<br><small>IT-101</small></td>
                                    <td colspan="2" style="text-align: center; color: var(--gray);">Laboratory Session</td>
                                    <td>Software Engineering<br><small>Main 304</small></td>
                                </tr>
                                <tr>
                                    <td><strong>Friday</strong></td>
                                    <td>Networking<br><small>Tech 202</small></td>
                                    <td>Data Structures<br><small>CS Lab 1</small></td>
                                    <td colspan="2" style="text-align: center; color: var(--gray);">Self-Study/Research</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-exclamation-circle"></i> Upcoming Schedule Changes</h5>
                    </div>
                    <div class="announcements-list">
                        <div class="announcement-item">
                            <div class="announcement-header">
                                <div class="announcement-title">Class Cancellation</div>
                                <div class="announcement-date">March 25</div>
                            </div>
                            <div class="announcement-content">
                                Networking class on March 25 (Friday) is cancelled due to a faculty seminar. Make-up class will be scheduled.
                            </div>
                        </div>
                        <div class="announcement-item">
                            <div class="announcement-header">
                                <div class="announcement-title">Room Change</div>
                                <div class="announcement-date">Starting April 1</div>
                            </div>
                            <div class="announcement-content">
                                Web Development class will move from IT-101 to IT-205 starting April 1, 2024.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Courses Content -->
            <section id="courses-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">My Courses</h2>
                    <button class="btn-primary">Add/Drop Course</button>
                </div>
                
                <!-- <div class="courses-grid">
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-code">CSC 301</div>
                            <div class="course-name">Web Development</div>
                            <div class="course-instructor">Prof. Maria Garcia</div>
                        </div>
                        <div class="course-body">
                            <div class="course-schedule">
                                <i class="fas fa-calendar"></i> Mon, Wed, Thu • 8:00-9:30 AM
                            </div>
                            <div class="course-schedule">
                                <i class="fas fa-map-marker-alt"></i> IT-101
                            </div>
                            <div class="course-progress">
                                <div class="progress-header">
                                    <span class="progress-label">Progress</span>
                                    <span class="progress-value">92%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 92%; background-color: var(--success);"></div>
                                </div>
                            </div>
                            <button class="btn-outline" style="width: 100%;">View Course Details</button>
                        </div>
                    </div>
                    
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-code">CSC 305</div>
                            <div class="course-name">Database Systems</div>
                            <div class="course-instructor">Prof. Juan Santos</div>
                        </div>
                        <div class="course-body">
                            <div class="course-schedule">
                                <i class="fas fa-calendar"></i> Mon, Wed • 10:00-11:30 AM
                            </div>
                            <div class="course-schedule">
                                <i class="fas fa-map-marker-alt"></i> CS Lab 2
                            </div>
                            <div class="course-progress">
                                <div class="progress-header">
                                    <span class="progress-label">Progress</span>
                                    <span class="progress-value">88%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 88%; background-color: var(--primary);"></div>
                                </div>
                            </div>
                            <button class="btn-outline" style="width: 100%;">View Course Details</button>
                        </div>
                    </div>
                    
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-code">CSC 310</div>
                            <div class="course-name">Software Engineering</div>
                            <div class="course-instructor">Prof. Andrea Reyes</div>
                        </div>
                        <div class="course-body">
                            <div class="course-schedule">
                                <i class="fas fa-calendar"></i> Mon, Thu • 1:00-2:30 PM
                            </div>
                            <div class="course-schedule">
                                <i class="fas fa-map-marker-alt"></i> Main 304
                            </div>
                            <div class="course-progress">
                                <div class="progress-header">
                                    <span class="progress-label">Progress</span>
                                    <span class="progress-value">90%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 90%; background-color: var(--accent);"></div>
                                </div>
                            </div>
                            <button class="btn-outline" style="width: 100%;">View Course Details</button>
                        </div>
                    </div>
                </div> -->
            </section>

            <!-- Grades Content -->
            <section id="grades-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Grades & Transcript</h2>
                    @if($hasEnrolledSubjects)
                        <button class="btn-primary" id="downloadTranscript">
                            <i class="fas fa-download"></i> Download Transcript
                        </button>
                    @endif
                </div>
                
                @if($hasEnrolledSubjects)
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-chart-line"></i> Academic Grades</h5>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">Passing: 1.0-3.0</span>
                                <span class="badge bg-danger">Failing: 4.0-5.0</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="grade-table">
                                <thead>
                                    <tr>
                                        <th width="15%">Code</th>
                                        <th width="40%">Subject Name</th>
                                        <th width="15%">Units</th>
                                        <th width="15%">Grade</th>
                                        <th width="15%">Equivalent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($gradesTableData) > 0)
                                        @foreach($gradesTableData as $grade)
                                        <tr>
                                            <td>
                                                <strong>{{ $grade['subject_code'] }}</strong>
                                            </td>
                                            <td>
                                                <div class="subject-name">{{ $grade['subject_name'] }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark">{{ $grade['units'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="grade-value {{ $grade['color_class'] }}">
                                                    {{ $grade['grade'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="grade-badge {{ $grade['color_class'] }}">
                                                    {{ $grade['equivalent'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
                                                <h6 class="text-gray-500">No Grade Data Available</h6>
                                                <p class="text-muted">You haven't enrolled in any subjects yet.</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if(count($gradesTableData) > 0)
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="2" class="text-end">
                                            <strong>Total:</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $totalUnits }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong>GWA: {{ number_format($gwa, 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $academicStanding }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-calculator"></i> GPA Summary</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-filter="all">All Semesters</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="current">Current Year</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="previous">Previous Years</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="dashboard-grid" style="margin-bottom: 0;">
                            <div>
                                <div class="welcome-stat">
                                    <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h3>{{ number_format($currentYearGWA, 2) }}</h3>
                                        <p>Current Year GWA</p>
                                        <small class="text-muted">{{ $currentYearSubjects }} subjects</small>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="welcome-stat">
                                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h3>{{ number_format($gwa, 2) }}</h3>
                                        <p>Cumulative GWA</p>
                                        <small class="text-muted">{{ $totalSubjects }} total subjects</small>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="welcome-stat">
                                    <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--accent);">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h3>{{ $academicStanding }}</h3>
                                        <p>Academic Standing</p>
                                        <small class="text-muted">
                                            @if(strpos($academicStanding, 'Lister') !== false)
                                                <i class="fas fa-star text-warning"></i> Excellent
                                            @else
                                                {{ $student->status }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Grade Distribution --}}
                        @if(isset($gradeDistribution))
                        <div class="mt-4">
                            <h6 class="mb-3">Grade Distribution</h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $totalGrades = array_sum($gradeDistribution);
                                            $excellentPercent = ($totalGrades > 0) ? ($gradeDistribution['excellent'] / $totalGrades) * 100 : 0;
                                            $goodPercent = ($totalGrades > 0) ? ($gradeDistribution['good'] / $totalGrades) * 100 : 0;
                                            $fairPercent = ($totalGrades > 0) ? ($gradeDistribution['fair'] / $totalGrades) * 100 : 0;
                                            $passingPercent = ($totalGrades > 0) ? ($gradeDistribution['passing'] / $totalGrades) * 100 : 0;
                                            $failingPercent = ($totalGrades > 0) ? ($gradeDistribution['failing'] / $totalGrades) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-success" style="width: {{ $excellentPercent }}%" 
                                            title="Excellent (1.0-1.5): {{ $gradeDistribution['excellent'] }}"></div>
                                        <div class="progress-bar bg-info" style="width: {{ $goodPercent }}%" 
                                            title="Good (1.6-2.0): {{ $gradeDistribution['good'] }}"></div>
                                        <div class="progress-bar bg-warning" style="width: {{ $fairPercent }}%" 
                                            title="Fair (2.1-2.5): {{ $gradeDistribution['fair'] }}"></div>
                                        <div class="progress-bar bg-secondary" style="width: {{ $passingPercent }}%" 
                                            title="Passing (2.6-3.0): {{ $gradeDistribution['passing'] }}"></div>
                                        <div class="progress-bar bg-danger" style="width: {{ $failingPercent }}%" 
                                            title="Failing (4.0-5.0): {{ $gradeDistribution['failing'] }}"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end small">
                                        <div class="mb-1">
                                            <span class="badge bg-success">1.0-1.5:</span> {{ $gradeDistribution['excellent'] }}
                                        </div>
                                        <div class="mb-1">
                                            <span class="badge bg-info">1.6-2.0:</span> {{ $gradeDistribution['good'] }}
                                        </div>
                                        <div class="mb-1">
                                            <span class="badge bg-warning">2.1-2.5:</span> {{ $gradeDistribution['fair'] }}
                                        </div>
                                        <div class="mb-1">
                                            <span class="badge bg-secondary">2.6-3.0:</span> {{ $gradeDistribution['passing'] }}
                                        </div>
                                        <div>
                                            <span class="badge bg-danger">4.0-5.0:</span> {{ $gradeDistribution['failing'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="fas fa-graduation-cap fa-4x text-gray-300 mb-4"></i>
                            <h4 class="text-gray-500">No Grades Available</h4>
                            <p class="text-muted mb-4">You need to enroll in subjects first to see your grades and GPA.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollmentModal">
                                <i class="fas fa-plus-circle"></i> Enroll Subjects Now
                            </button>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-chart-line"></i> Academic Grades</h5>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="fas fa-graduation-cap fa-4x text-gray-300 mb-4"></i>
                            <h4 class="text-gray-500">No Grades Available</h4>
                            <p class="text-muted mb-4">You need to enroll in subjects first to see your grades and GPA.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollmentModal">
                                <i class="fas fa-plus-circle"></i> Enroll Subjects Now
                            </button>
                        </div>
                    </div>
                @endif
            </section>

            <!-- Placeholder for other sections (assignments, exams, etc.) -->
            <section id="assignments-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Assignments</h2>
                    <button class="btn-primary">Submit Assignment</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-tasks"></i> All Assignments</h5>
                    </div>
                    <p>This section would display all assignments across all courses. Click "Assignments" in the sidebar to view this content.</p>
                </div>
            </section>

            <section id="exams-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Exams</h2>
                    <button class="btn-primary">View Exam Schedule</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-file-alt"></i> Upcoming Exams</h5>
                    </div>
                    <p>This section would display all upcoming exams and test schedules. Click "Exams" in the sidebar to view this content.</p>
                </div>
            </section>

            <section id="elibrary-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">E-Library</h2>
                    <button class="btn-primary">Browse Catalog</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-book"></i> Digital Resources</h5>
                    </div>
                    <p>This section would provide access to digital library resources, e-books, and academic journals. Click "E-Library" in the sidebar to view this content.</p>
                </div>
            </section>

            <section id="finances-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Finances</h2>
                    <button class="btn-primary">View Statement</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Financial Overview</h5>
                    </div>
                    <p>This section would display tuition fees, payment history, and scholarship information. Click "Finances" in the sidebar to view this content.</p>
                </div>
            </section>

            <section id="clubs-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Student Clubs</h2>
                    <button class="btn-primary">Browse Clubs</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-users"></i> Campus Organizations</h5>
                    </div>
                    <p>This section would list student clubs and organizations you can join. Click "Student Clubs" in the sidebar to view this content.</p>
                </div>
            </section>

            <section id="help-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Help Center</h2>
                    <button class="btn-primary">Submit Ticket</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-headset"></i> Support Resources</h5>
                    </div>
                    <p>This section would provide access to technical support, FAQs, and contact information. Click "Help Center" in the sidebar to view this content.</p>
                </div>
            </section>

            <section id="settings-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Settings</h2>
                    <button class="btn-primary">Save Changes</button>
                </div>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-cog"></i> Account Settings</h5>
                    </div>
                    <p>This section would allow you to update your account preferences, notification settings, and privacy options. Click "Settings" in the sidebar to view this content.</p>
                </div>
            </section>
        </main>

        @if(!$hasEnrolledSubjects && !empty($availableSubjects))
        <div class="modal fade" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="enrollmentModalLabel">Select Subjects</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!--  -->
                    <div class="modal-body">
                        <p class="mb-4">Please select the subjects you have already accomplished and provide your grades.</p>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You can use the search bar to find specific subjects.
                        </div>
                        
                        {{-- View Toggle Buttons --}}
                        <div class="mb-4">
                            <div class="btn-group" role="group">
                                <!-- <button type="button" class="btn btn-outline-primary active" id="tableViewBtn">
                                    <i class="fas fa-table"></i> Table View
                                </button> -->
                                <!-- <button type="button" class="btn btn-outline-primary" id="cardViewBtn">
                                    <i class="fas fa-th-large"></i> Card View
                                </button> -->
                                <!-- <button type="button" class="btn btn-outline-primary" id="quickAddBtnToggle">
                                    <i class="fas fa-bolt"></i> Quick Add
                                </button> -->
                            </div>
                        </div>
                        
                        {{-- TABLE VIEW (Only shown when table view is active) --}}
                        <div id="tableView" class="subject-view">
                            {{-- Search and Filters --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="subjectSearch" 
                                            placeholder="Search by subject code or name...">
                                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Type to filter subjects</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="showSelectedOnly">
                                                <label class="form-check-label" for="showSelectedOnly">
                                                    Show selected only
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="hideCompleted">
                                                <label class="form-check-label" for="hideCompleted">
                                                    Hide selected
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="expandAll">
                                                <label class="form-check-label" for="expandAll">
                                                    Expand all
                                                </label>
                                            </div>
                                            <!-- <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="selectAllVisible">
                                                <i class="fas fa-check-square"></i> Select all visible
                                            </button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="subjectSelectionContainer">
                                @foreach($availableSubjects as $yearLevel => $semesters)
                                    <div class="year-level-section mb-5">
                                        <h4 class="year-level-header d-flex justify-content-between align-items-center">
                                            <span>{{ $yearLevel }}</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary toggle-year" 
                                                    data-year="{{ $yearLevel }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </h4>
                                        
                                        @foreach($semesters as $semester => $subjects)
                                            <div class="semester-section mb-4">
                                                <h5 class="semester-header d-flex justify-content-between align-items-center">
                                                    <span>{{ $semester }}</span>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-semester" 
                                                            data-semester="{{ $semester }}">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover subject-table">
                                                        <thead>
                                                            <tr>
                                                                <th width="50">Select</th>
                                                                <th width="100">Code</th>
                                                                <th>Subject Name</th>
                                                                <th width="80">Units</th>
                                                                <th width="120">Grade</th>
                                                                <th width="100">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($subjects as $subject)
                                                            <tr data-subject-id="{{ $subject->id }}" 
                                                                data-code="{{ strtoupper($subject->code) }}"
                                                                data-name="{{ strtoupper($subject->name) }}"
                                                                class="subject-row">
                                                                <td class="text-center">
                                                                    <input type="checkbox" class="form-check-input subject-checkbox" 
                                                                        data-subject-id="{{ $subject->id }}"
                                                                        data-units="{{ $subject->units ?? 3 }}"
                                                                        id="subject_{{ $subject->id }}">
                                                                </td>
                                                                <td class="subject-code">{{ $subject->code }}</td>
                                                                <td class="subject-name">{{ $subject->name }}</td>
                                                                <td class="text-center">{{ $subject->units ?? 3 }}</td>
                                                                <td>
                                                                    <select class="form-select form-select-sm grade-select" 
                                                                            data-subject-id="{{ $subject->id }}" 
                                                                            disabled>
                                                                        <option value="">-- Select --</option>
                                                                        @for($i = 1.0; $i <= 3.1;$i += 0.1)
                                                                            @php
                                                                                $grade = number_format($i, 1);
                                                                                $gradeLabel = $grade;
                                                                            @endphp
                                                                            <option value="{{ $grade }}">{{ $gradeLabel }}</option>
                                                                        @endfor
                                                                        <option value="4.0">4.0</option>
                                                                        <option value="5.0">5.0</option>
                                                                        <option value="INC">INC</option>
                                                                        <option value="DRP">DRP</option>
                                                                        <option value="PASS">PASS</option>
                                                                        <option value="FAIL">FAIL</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary undo-btn" 
                                                                            data-subject-id="{{ $subject->id }}"
                                                                            style="display: none;">
                                                                        <i class="fas fa-undo"></i> Undo
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        {{-- CARD VIEW (Only shown when card view is active) --}}
                        <div id="cardView" class="subject-view" style="display: none;">
                            {{-- Quick Subject Add Dropdown --}}
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Add Subject</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Year Level</label>
                                            <select class="form-select" id="quickYearSelect">
                                                <option value="">All Years</option>
                                                @foreach($availableSubjects as $yearLevel => $semesters)
                                                    <option value="{{ $yearLevel }}">{{ $yearLevel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Semester</label>
                                            <select class="form-select" id="quickSemesterSelect">
                                                <option value="">All Semesters</option>
                                                <option value="1st Sem">1st Semester</option>
                                                <option value="2nd Sem">2nd Semester</option>
                                                <option value="Summer">Summer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Subject</label>
                                            <select class="form-select" id="quickSubjectSelect">
                                                <option value="">Select a subject</option>
                                                @foreach($availableSubjects as $yearLevel => $semesters)
                                                    @foreach($semesters as $semester => $subjects)
                                                        @foreach($subjects as $subject)
                                                            <option value="{{ $subject->id }}" 
                                                                    data-year="{{ $yearLevel }}"
                                                                    data-semester="{{ $semester }}"
                                                                    data-units="{{ $subject->units ?? 3 }}">
                                                                {{ $subject->code }} - {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Grade</label>
                                            <select class="form-select" id="quickGradeSelect">
                                                <option value="">Select Grade</option>
                                                @for($i = 1.0; $i <= 3.0; $i += 0.1)
                                                    @php
                                                        $grade = number_format($i, 1);
                                                        $gradeLabel = $grade;
                                                    @endphp
                                                    <option value="{{ $grade }}">{{ $gradeLabel }}</option>
                                                @endfor
                                                <option value="4.0">4.0</option>
                                                <option value="5.0">5.0</option>
                                                <option value="INC">INC</option>
                                                <option value="DRP">DRP</option>
                                                <option value="PASS">PASS</option>
                                                <option value="FAIL">FAIL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-primary w-100 mt-2" id="quickAddBtn">
                                                <i class="fas fa-plus"></i> Add Subject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Subject Cards --}}
                            <div class="accordion" id="subjectAccordion">
                                @foreach($availableSubjects as $yearLevel => $semesters)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse{{ str_replace(' ', '', $yearLevel) }}">
                                                <i class="fas fa-graduation-cap me-2"></i> {{ $yearLevel }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ str_replace(' ', '', $yearLevel) }}" class="accordion-collapse collapse show" 
                                            data-bs-parent="#subjectAccordion">
                                            <div class="accordion-body">
                                                @foreach($semesters as $semester => $subjects)
                                                    <div class="mb-4">
                                                        <h6 class="mb-3 border-bottom pb-2">
                                                            <i class="fas fa-calendar-alt me-2"></i> {{ $semester }}
                                                        </h6>
                                                        <div class="row">
                                                            @foreach($subjects as $subject)
                                                            <div class="col-md-6 col-lg-4 mb-3">
                                                                <div class="card subject-card h-100" data-subject-id="{{ $subject->id }}">
                                                                    <div class="card-body">
                                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                                            <div>
                                                                                <h6 class="card-title mb-1 text-primary">{{ $subject->code }}</h6>
                                                                                <p class="card-text small">{{ $subject->name }}</p>
                                                                            </div>
                                                                            <span class="badge bg-secondary">{{ $subject->units ?? 3 }} units</span>
                                                                        </div>
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input subject-checkbox-card" 
                                                                                    data-subject-id="{{ $subject->id }}"
                                                                                    data-units="{{ $subject->units ?? 3 }}"
                                                                                    id="subject_card_{{ $subject->id }}">
                                                                                <label class="form-check-label small" for="subject_card_{{ $subject->id }}">
                                                                                    Select
                                                                                </label>
                                                                            </div>
                                                                            <div class="grade-selection" style="display: none;">
                                                                                <select class="form-select form-select-sm grade-select-card" 
                                                                                        data-subject-id="{{ $subject->id }}"
                                                                                        style="width: 120px;">
                                                                                    <option value="">Grade</option>
                                                                                    @for($i = 1.0; $i <= 3.0; $i += 0.1)
                                                                                        @php
                                                                                            $grade = number_format($i, 1);
                                                                                            $gradeLabel = $grade;
                                                                                        @endphp
                                                                                        <option value="{{ $grade }}">{{ $gradeLabel }}</option>
                                                                                    @endfor
                                                                                    <option value="4.0">4.0</option>
                                                                                    <option value="5.0">5.0</option>
                                                                                    <option value="INC">INC</option>
                                                                                    <option value="DRP">DRP</option>
                                                                                    <option value="PASS">PASS</option>
                                                                                    <option value="FAIL">FAIL</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer bg-transparent border-top-0 pt-0">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 undo-btn-card" 
                                                                                data-subject-id="{{ $subject->id }}"
                                                                                style="display: none;">
                                                                            <i class="fas fa-times"></i> Remove
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        {{-- Selected Subjects Summary --}}
                        <div class="selected-subjects-summary mt-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-list-check"></i> Selected Subjects Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div id="selectedSubjectsList" class="mb-3" style="max-height: 200px; overflow-y: auto;">
                                        <p class="text-muted mb-0">No subjects selected yet.</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="alert alert-secondary">
                                                <small>Total Subjects</small>
                                                <h4 class="mb-0" id="totalSubjectsCount">0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="alert alert-info">
                                                <small>Total Units</small>
                                                <h4 class="mb-0" id="totalUnitsCount">0</h4>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <div class="alert alert-warning">
                                                <small>Grade Points</small>
                                                <h4 class="mb-0" id="totalGradePoints">0.00</h4>
                                            </div>
                                        </div> -->
                                        <div class="col-md-3">
                                            <div class="alert alert-primary">
                                                <small>GWA</small>
                                                <h4 class="mb-0" id="gwaPreview">0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Grade Distribution --}}
                                    <div class="mt-3">
                                        <h6>Grade Distribution</h6>
                                        <div class="progress mb-2" style="height: 25px;">
                                            <div class="progress-bar bg-success" id="passingGradeBar" style="width: 0%"></div>
                                            <div class="progress-bar bg-warning" id="conditionalGradeBar" style="width: 0%"></div>
                                            <div class="progress-bar bg-danger" id="failingGradeBar" style="width: 0%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span><span class="badge bg-success">1.0-1.5</span> <span id="excellentCount">0</span></span>
                                            <span><span class="badge bg-warning">1.6-3.0</span> <span id="goodCount">0</span></span>
                                            <span><span class="badge bg-danger">4.0-5.0</span> <span id="failingCount">0</span></span>
                                            <span><span class="badge bg-secondary">INC/DRP</span> <span id="specialCount">0</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Please review your selections before submitting. This action cannot be undone.
                        </div>
                    </div>
                    <!--  -->
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> -->
                        <button type="button" class="btn btn-primary" id="submitEnrollment" disabled>
                            <i class="fas fa-check"></i> Submit Enrollment
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="container">
            <div class="footer-links">
                <a href="#" class="footer-link">About EVSU</a>
                <a href="#" class="footer-link">Contact Us</a>
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Help Center</a>
            </div>
            <p class="mb-2">© 2026 Eastern Visayas State University - Ormoc Campus. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('js/jquery.js')}}"></script>
    <script src="{{asset('js/function/student/dashboard.js')}}"></script>
    <script src="{{ asset('js/function/student/scholastic-warnings.js') }}"></script>
</body>
</html>
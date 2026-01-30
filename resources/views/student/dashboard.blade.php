<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVSU Ormoc - Student Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary: #0ea5e9;
            --accent: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
        }
        
        /* Header Styles */
        .dashboard-header {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
        }
        
        .brand-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 22px;
            font-weight: 700;
        }
        
        .brand-text h4 {
            margin-bottom: 0;
            color: var(--dark);
        }
        
        .brand-text p {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            border: 1px solid var(--light-gray);
            border-radius: 30px;
            padding: 10px 20px 10px 45px;
            width: 300px;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
            font-size: 1.2rem;
            transition: var(--transition);
        }
        
        .notification-btn:hover {
            background-color: var(--light);
            color: var(--primary);
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .user-info {
            line-height: 1.4;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--dark);
        }
        
        .user-role {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: white;
            padding: 30px 0;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.03);
            position: relative;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 0 25px 30px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .sidebar-title {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .sidebar-subtitle {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .nav-menu {
            padding: 30px 0;
        }
        
        .nav-section {
            padding: 0 25px;
            margin-bottom: 30px;
        }
        
        .nav-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray);
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .nav-item {
            margin-bottom: 8px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border-radius: 10px;
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(37, 99, 235, 0.08);
            color: var(--primary);
        }
        
        .nav-icon {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .nav-badge {
            margin-left: auto;
            background-color: var(--primary);
            color: white;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        
        /* Content sections */
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-section::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        
        .welcome-content h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .welcome-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin-bottom: 25px;
        }
        
        .welcome-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .welcome-stat {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        
        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .card-title {
            font-size: 1.2rem;
            color: var(--dark);
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 12px;
            color: var(--primary);
        }
        
        .card-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .card-link i {
            margin-left: 5px;
            font-size: 0.8rem;
        }
        
        /* Schedule Timeline */
        .timeline {
            margin-top: 10px;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .timeline-item:last-child {
            border-bottom: none;
        }
        
        .time-badge {
            width: 80px;
            font-weight: 600;
            color: var(--primary);
            font-size: 0.95rem;
        }
        
        .class-details {
            flex: 1;
            padding: 0 20px;
        }
        
        .class-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .class-info {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .class-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-current {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }
        
        .status-upcoming {
            background-color: rgba(37, 99, 235, 0.15);
            color: var(--primary);
        }
        
        /* Progress Chart */
        .progress-chart {
            margin-top: 20px;
        }
        
        .progress-item {
            margin-bottom: 20px;
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .progress-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .progress-value {
            font-weight: 600;
            color: var(--primary);
        }
        
        .progress-bar {
            height: 10px;
            background-color: var(--light-gray);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 10px;
        }
        
        /* Assignments List */
        .assignments-list {
            margin-top: 10px;
        }
        
        .assignment-item {
            display: flex;
            align-items: center;
            padding: 18px;
            border-radius: 12px;
            background-color: var(--light);
            margin-bottom: 15px;
            transition: var(--transition);
        }
        
        .assignment-item:hover {
            background-color: rgba(37, 99, 235, 0.05);
        }
        
        .assignment-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.2rem;
        }
        
        .icon-high { background-color: var(--danger); }
        .icon-medium { background-color: var(--warning); }
        .icon-low { background-color: var(--success); }
        
        .assignment-details {
            flex: 1;
        }
        
        .assignment-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .assignment-info {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .assignment-deadline {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Announcements */
        .announcements-list {
            margin-top: 10px;
        }
        
        .announcement-item {
            padding: 20px;
            border-radius: 12px;
            background-color: rgba(37, 99, 235, 0.05);
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
        }
        
        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .announcement-title {
            font-weight: 600;
            color: var(--dark);
        }
        
        .announcement-date {
            font-size: 0.85rem;
            color: var(--gray);
            background: white;
            padding: 4px 12px;
            border-radius: 20px;
        }
        
        .announcement-content {
            font-size: 0.95rem;
            color: var(--dark);
            opacity: 0.8;
        }
        
        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .quick-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 25px 15px;
            background: white;
            border-radius: 15px;
            text-decoration: none;
            color: var(--dark);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }
        
        .quick-link:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
            color: var(--primary);
        }
        
        .quick-link-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: white;
            font-size: 1.4rem;
        }
        
        .quick-link-label {
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        /* Content for other sections */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.8rem;
            color: var(--dark);
        }
        
        .profile-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-right: 25px;
        }
        
        .profile-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .profile-info p {
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .detail-item {
            margin-bottom: 20px;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1.1rem;
            color: var(--dark);
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .course-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }
        
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .course-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px;
        }
        
        .course-code {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .course-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .course-instructor {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .course-body {
            padding: 25px;
        }
        
        .course-schedule {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .course-schedule i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .course-progress {
            margin-bottom: 20px;
        }
        
        .grade-table {
            width: 100%;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        
        .grade-table th {
            background-color: var(--primary);
            color: white;
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
        }
        
        .grade-table td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .grade-table tr:last-child td {
            border-bottom: none;
        }
        
        .grade-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .grade-A { background-color: rgba(16, 185, 129, 0.15); color: var(--success); }
        .grade-B { background-color: rgba(37, 99, 235, 0.15); color: var(--primary); }
        .grade-C { background-color: rgba(245, 158, 11, 0.15); color: var(--warning); }
        
        /* Mobile Menu Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
            font-size: 1.2rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 1200px) {
            .sidebar {
                position: fixed;
                left: -250px;
                top: 80px;
                height: calc(100vh - 80px);
                transition: left 0.3s ease;
                z-index: 100;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .mobile-toggle {
                display: flex;
            }
            
            .search-box input {
                width: 200px;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .overlay {
                display: none;
                position: fixed;
                top: 80px;
                left: 0;
                width: 100%;
                height: calc(100vh - 80px);
                background: rgba(0, 0, 0, 0.5);
                z-index: 90;
            }
            
            .overlay.show {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 20px 15px;
            }
            
            .welcome-section {
                padding: 30px 20px;
            }
            
            .welcome-content h1 {
                font-size: 1.8rem;
            }
            
            .welcome-stats {
                gap: 20px;
            }
            
            .search-box {
                display: none;
            }
            
            .quick-links {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .timeline-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .time-badge {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .class-details {
                padding: 0;
                margin-bottom: 10px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .courses-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .welcome-stats {
                flex-direction: column;
                gap: 15px;
            }
            
            .quick-links {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                display: none;
            }
            
            .dashboard-card {
                padding: 20px;
            }
        }
        
        /* Footer */
        .dashboard-footer {
            background: white;
            border-top: 1px solid var(--light-gray);
            padding: 25px 0;
            text-align: center;
            margin-top: 40px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 20px;
        }
        
        .footer-link {
            color: var(--gray);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-link:hover {
            color: var(--primary);
        }
        
        /* Utility Classes */
        .text-primary {
            color: var(--primary) !important;
        }
        
        .bg-primary-light {
            background-color: rgba(37, 99, 235, 0.08) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
    </style>
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
                            JD
                        </div>
                        <div class="user-info">
                            <div class="user-name">Carl James Duallo</div>
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
                    <div class="nav-item">
                        <a class="nav-link" data-section="schedule">
                            <i class="fas fa-calendar-alt nav-icon"></i>
                            <span>Schedule</span>
                            <span class="nav-badge">New</span>
                        </a>
                    </div>
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
                    <div class="nav-item">
                        <a class="nav-link" data-section="assignments">
                            <i class="fas fa-tasks nav-icon"></i>
                            <span>Assignments</span>
                            <span class="nav-badge">5</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="exams">
                            <i class="fas fa-file-alt nav-icon"></i>
                            <span>Exams</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
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
                </div>
                
                <div class="nav-section">
                    <div class="nav-label">Support</div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="help">
                            <i class="fas fa-headset nav-icon"></i>
                            <span>Help Center</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-section="settings">
                            <i class="fas fa-cog nav-icon"></i>
                            <span>Settings</span>
                        </a>
                    </div>
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
                        <h1>Welcome back, James!</h1>
                        <p>You have 2 upcoming assignments and 1 quiz this week. Your next class starts in 45 minutes.</p>
                        
                        <div class="welcome-stats">
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>3.78</h3>
                                    <p>Current GPA</p>
                                </div>
                            </div>
                            
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>94%</h3>
                                    <p>Attendance</p>
                                </div>
                            </div>
                            
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>15</h3>
                                    <p>Units Enrolled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Dashboard Grid -->
                <div class="dashboard-grid">
                    <!-- Today's Schedule -->
                    <div class="dashboard-card">
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
                    </div>
                    
                    <!-- Academic Progress -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-chart-bar"></i> Academic Progress</h5>
                            <a href="#" class="card-link">Details <i class="fas fa-chevron-right"></i></a>
                        </div>
                        
                        <div class="progress-chart">
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label">Web Development</span>
                                    <span class="progress-value">92%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 92%; background-color: var(--success);"></div>
                                </div>
                            </div>
                            
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label">Database Systems</span>
                                    <span class="progress-value">88%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 88%; background-color: var(--primary);"></div>
                                </div>
                            </div>
                            
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label">Data Structures</span>
                                    <span class="progress-value">85%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 85%; background-color: var(--warning);"></div>
                                </div>
                            </div>
                            
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label">Software Engineering</span>
                                    <span class="progress-value">90%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 90%; background-color: var(--accent);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="dashboard-grid">
                    <!-- Upcoming Assignments -->
                    <div class="dashboard-card">
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
                    </div>
                    
                    <!-- Campus Announcements -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-bullhorn"></i> Campus Announcements</h5>
                            <a href="#" class="card-link">View All <i class="fas fa-chevron-right"></i></a>
                        </div>
                        
                        <div class="announcements-list">
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
                </div>
                
                <!-- Quick Links -->
                <div class="dashboard-card">
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
                </div>
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
                            <div class="detail-item">
                                <div class="detail-label">Academic Advisor</div>
                                <div class="detail-value">Prof. Maria Santos</div>
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
                
                <div class="courses-grid">
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
                </div>
            </section>

            <!-- Grades Content -->
            <section id="grades-content" class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Grades & Transcript</h2>
                    <button class="btn-primary">Download Transcript</button>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-chart-line"></i> Current Semester Grades</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="grade-table">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Prelim</th>
                                    <th>Midterm</th>
                                    <th>Final</th>
                                    <th>Overall</th>
                                    <th>Equivalent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Web Development</td>
                                    <td>Prof. Garcia</td>
                                    <td>92%</td>
                                    <td>95%</td>
                                    <td>89%</td>
                                    <td>92%</td>
                                    <td><span class="grade-badge grade-A">A</span></td>
                                </tr>
                                <tr>
                                    <td>Database Systems</td>
                                    <td>Prof. Santos</td>
                                    <td>85%</td>
                                    <td>90%</td>
                                    <td>89%</td>
                                    <td>88%</td>
                                    <td><span class="grade-badge grade-B">B+</span></td>
                                </tr>
                                <tr>
                                    <td>Software Engineering</td>
                                    <td>Prof. Reyes</td>
                                    <td>88%</td>
                                    <td>92%</td>
                                    <td>90%</td>
                                    <td>90%</span></td>
                                    <td><span class="grade-badge grade-A">A-</span></td>
                                </tr>
                                <tr>
                                    <td>Data Structures</td>
                                    <td>Prof. Cruz</td>
                                    <td>82%</td>
                                    <td>85%</td>
                                    <td>88%</td>
                                    <td>85%</td>
                                    <td><span class="grade-badge grade-B">B</span></td>
                                </tr>
                                <tr>
                                    <td>Networking</td>
                                    <td>Prof. Lim</td>
                                    <td>87%</td>
                                    <td>84%</td>
                                    <td>86%</td>
                                    <td>86%</td>
                                    <td><span class="grade-badge grade-B">B+</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-calculator"></i> GPA Summary</h5>
                    </div>
                    <div class="dashboard-grid" style="margin-bottom: 0;">
                        <div>
                            <div class="welcome-stat">
                                <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>3.78</h3>
                                    <p>Current Semester GPA</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="welcome-stat">
                                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>3.65</h3>
                                    <p>Cumulative GPA</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="welcome-stat">
                                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--accent);">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>Dean's Lister</h3>
                                    <p>Academic Standing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <script>
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        // Update welcome message based on time of day
        function updateWelcomeMessage() {
            const hour = new Date().getHours();
            const welcomeElement = document.querySelector('.welcome-content h1');
            const name = "James";
            
            if (hour < 12) {
                welcomeElement.textContent = `Good morning, ${name}!`;
            } else if (hour < 18) {
                welcomeElement.textContent = `Good afternoon, ${name}!`;
            } else {
                welcomeElement.textContent = `Good evening, ${name}!`;
            }
        }
        
        // Update class status based on current time
        function updateClassStatus() {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinutes = now.getMinutes();
            const currentTime = currentHour * 60 + currentMinutes;
            
            const classTimes = [8*60, 10.5*60, 13*60, 15.5*60]; // 8:00, 10:30, 1:00, 3:30
            const statusElements = document.querySelectorAll('.class-status');
            
            statusElements.forEach((status, index) => {
                const classTime = classTimes[index];
                
                if (currentTime >= classTime && currentTime < classTime + 90) {
                    // Class is ongoing (1.5-hour classes)
                    status.textContent = "Ongoing";
                    status.className = "class-status status-current";
                } else if (currentTime < classTime && index === 0) {
                    // First class hasn't started yet
                    status.textContent = "Upcoming";
                    status.className = "class-status status-upcoming";
                } else if (currentTime < classTime) {
                    // Future class
                    status.textContent = "Upcoming";
                    status.className = "class-status status-upcoming";
                } else {
                    // Class already passed
                    status.textContent = "Completed";
                    status.className = "class-status status-upcoming";
                }
            });
        }
        
        // Update assignment deadlines
        function updateAssignmentDeadlines() {
            const now = new Date();
            const assignments = document.querySelectorAll('.assignment-deadline');
            
            assignments.forEach((deadline, index) => {
                const dueDate = new Date();
                
                // Simulate different due dates
                if (index === 0) {
                    dueDate.setDate(now.getDate() + 1); // Tomorrow
                    deadline.textContent = "Due Tomorrow";
                } else if (index === 1) {
                    dueDate.setDate(now.getDate() + 4); // 4 days from now
                    deadline.textContent = `Due ${dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`;
                } else {
                    dueDate.setDate(now.getDate() + 7); // 7 days from now
                    deadline.textContent = `Due ${dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`;
                }
            });
        }
        
        // Sidebar navigation functionality
        function setupSidebarNavigation() {
            const navLinks = document.querySelectorAll('.nav-link');
            const contentSections = document.querySelectorAll('.content-section');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the target section from data attribute
                    const targetSection = this.getAttribute('data-section');
                    
                    // Remove active class from all nav links
                    navLinks.forEach(item => item.classList.remove('active'));
                    
                    // Add active class to clicked nav link
                    this.classList.add('active');
                    
                    // Hide all content sections
                    contentSections.forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    // Show the target content section
                    const targetElement = document.getElementById(`${targetSection}-content`);
                    if (targetElement) {
                        targetElement.classList.add('active');
                    }
                    
                    // Update page title based on selected section
                    updatePageTitle(targetSection);
                    
                    // On mobile, close sidebar after clicking a link
                    if (window.innerWidth <= 1200) {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    }
                });
            });
        }
        
        // Update page title based on selected section
        function updatePageTitle(section) {
            const sectionTitles = {
                'dashboard': 'Dashboard',
                'profile': 'My Profile',
                'schedule': 'Schedule',
                'courses': 'Courses',
                'grades': 'Grades',
                'assignments': 'Assignments',
                'exams': 'Exams',
                'elibrary': 'E-Library',
                'finances': 'Finances',
                'clubs': 'Student Clubs',
                'help': 'Help Center',
                'settings': 'Settings'
            };
            
            const title = sectionTitles[section] || 'EVSU Student Dashboard';
            document.querySelector('.brand-text h4').textContent = `EVSU - ${title}`;
        }
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            updateWelcomeMessage();
            updateClassStatus();
            updateAssignmentDeadlines();
            setupSidebarNavigation();
            
            // Update class status every minute
            setInterval(updateClassStatus, 60000);
            
            // Notification button click
            document.querySelector('.notification-btn').addEventListener('click', function() {
                alert('You have 3 new notifications:\n- New grade posted for Web Development\n- Assignment due tomorrow\n- Campus event registration reminder');
            });
            
            // Quick links interaction
            const quickLinks = document.querySelectorAll('.quick-link');
            quickLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const linkName = this.querySelector('.quick-link-label').textContent;
                    alert(`Navigating to: ${linkName}`);
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-box input');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    alert(`Searching for: "${this.value}"`);
                    this.value = '';
                }
            });
            
            // Add click handlers for buttons in other sections
            document.querySelectorAll('.btn-primary, .btn-outline').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Prevent navigation for demo purposes
                    if (!this.getAttribute('href')) {
                        e.preventDefault();
                        const buttonText = this.textContent.trim();
                        alert(`Action: ${buttonText}`);
                    }
                });
            });
        });
    </script>
</body>
</html>
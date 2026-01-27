<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="bingbot" content="noarchive">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5"> -->
    <meta name="application-title" content="EnrollSys">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#101126">
    <meta name="msapplication-navbutton-color" content="#101126">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>enrollsys evsu</title>
    <link rel="website icon" href="{{ asset('img/evsu-logo.png') }}">
    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
     <link href="{{ asset('style/bootstrap.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
     <link href="{{ asset('style/google-fonts.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Login/Registration Card -->
    <div class="container-custom">
        <div class="glass-card d-flex">
            <!-- Left Panel: Brand & Information -->
            <div class="left-panel col-lg-6">
                <div class="logo-area">
                    <div class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span>EVSU-Ormoc City Campus</span>
                    </div>
                    <p class="tagline">Your gateway to academic excellence and seamless learning</p>
                </div>
                
                <div class="panel-content">
                    <h1 class="panel-title">Elevate Your Academic Journey</h1>
                    <p class="panel-description">Access course materials, track assignments, collaborate with peers, and monitor your academic progress all in one place.</p>
                    
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Course Library</h4>
                                <p>Access all your course materials in one organized place</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Progress Tracking</h4>
                                <p>Monitor your academic performance with detailed analytics</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Collaboration</h4>
                                <p>Connect with classmates and instructors easily</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Smart Schedule</h4>
                                <p>Never miss a deadline with our intelligent calendar</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats">
                        <div class="d-flex gap-5">
                            <div>
                                <h3 class="mb-1">50K+</h3>
                                <p class="opacity-80">Active Students</p>
                            </div>
                            <div>
                                <h3 class="mb-1">200+</h3>
                                <p class="opacity-80">Courses Available</p>
                            </div>
                            <div>
                                <h3 class="mb-1">24/7</h3>
                                <p class="opacity-80">Support Available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Panel: Forms -->
            <div class="right-panel col-lg-6">
                <div class="form-header">
                    <h2>Welcome to EVSU Ormoc</h2>
                    <p>Sign in to your account or create a new one to get started</p>
                </div>
                
                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    <button class="tab-button active" data-target="loginForm">Sign In</button>
                    <button class="tab-button" data-target="registerForm">Create Account</button>
                    <div class="tab-indicator" style="width: 50%; left: 6px;"></div>
                </div>
                
                <!-- Alert Messages -->
                <div class="alert alert-error" id="loginAlert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="loginAlertText">Invalid email or password. Please try again.</span>
                </div>
                
                <div class="alert alert-success" id="registerAlert">
                    <i class="fas fa-check-circle"></i>
                    <span id="registerAlertText">Registration successful! You can now sign in.</span>
                </div>
                
                <!-- Forms Container -->
                <div class="form-container">
                    <!-- Login Form -->
                    <div class="form-wrapper active" id="loginForm">
                        <form id="loginFormElement">
                            <div class="form-group">
                                <label class="form-label" for="loginEmail">Email Address</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" class="form-control" id="loginEmail" placeholder="student@evsu.edu.ph" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="loginPassword">Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control" id="loginPassword" placeholder="Enter your password" required>
                                    <button type="button" class="password-toggle" id="loginPasswordToggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex-between mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Remember me</label>
                                </div>
                                <a href="#" class="forgot-link">Forgot password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mb-3" id="loginButton">
                                <div class="spinner"></div>
                                <span class="btn-text">Sign In to Dashboard</span>
                            </button>
                            
                            <div class="divider">
                                <span>Or continue with</span>
                            </div>
                            
                            <button type="button" class="btn btn-google mb-3">
                                <i class="fab fa-google"></i>
                                <span>Google</span>
                            </button>
                            
                            <div class="text-center mt-4">
                                <p class="text-muted">Don't have an account? <a href="#" class="forgot-link switch-tab" data-target="registerForm">Create one now</a></p>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Registration Form -->
                    <div class="form-wrapper" id="registerForm">
                        <form id="registerFormElement">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="firstName">First Name</label>
                                    <div class="input-group">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" class="form-control" id="firstName" placeholder="John" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="lastName">Last Name</label>
                                    <div class="input-group">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="registerEmail">Email Address</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" class="form-control" id="registerEmail" placeholder="student@university.edu" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="studentId">Student ID</label>
                                <div class="input-group">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" class="form-control" id="studentId" placeholder="STU-2023-001" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="registerPassword">Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control" id="registerPassword" placeholder="Create a strong password" required>
                                    <button type="button" class="password-toggle" id="registerPasswordToggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="progress-bar mt-2">
                                    <div class="progress-fill" id="passwordStrength" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">Use at least 8 characters with a mix of uppercase, lowercase, numbers, and symbols</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="confirmPassword">Confirm Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control" id="confirmPassword" placeholder="Re-enter your password" required>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="termsAgreement" required>
                                <label class="form-check-label" for="termsAgreement">
                                    I agree to the <a href="#" class="forgot-link">Terms of Service</a> and <a href="#" class="forgot-link">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mb-3" id="registerButton">
                                <div class="spinner"></div>
                                <span class="btn-text">Create Account</span>
                            </button>
                            
                            <div class="divider">
                                <span>Or sign up with</span>
                            </div>
                            
                            <button type="button" class="btn btn-google mb-3">
                                <i class="fab fa-google"></i>
                                <span>Google</span>
                            </button>
                            
                            <div class="text-center mt-4">
                                <p class="text-muted">Already have an account? <a href="#" class="forgot-link switch-tab" data-target="loginForm">Sign in here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Help Center</a>
                    <a href="#">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dashboard (Hidden Initially) -->
    <div class="dashboard-container hidden" id="dashboardContainer">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">JD</div>
                <div class="user-details">
                    <h3 id="dashboardUserName">John Doe</h3>
                    <p id="dashboardUserEmail">john.doe@university.edu • Computer Science</p>
                </div>
            </div>
            
            <div class="header-actions">
                <div class="notification-badge">
                    <i class="fas fa-bell"></i>
                    <div class="badge-count">3</div>
                </div>
                <button class="btn btn-primary" id="logoutButton">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </button>
            </div>
        </div>
        
        <!-- Dashboard Stats -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="card-count">6</div>
                </div>
                <h3 class="card-title">Active Courses</h3>
                <p class="card-description">You're currently enrolled in 6 courses this semester</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 75%"></div>
                </div>
                <p class="text-muted mt-2">Completion: 75%</p>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon purple">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="card-count">3</div>
                </div>
                <h3 class="card-title">Pending Assignments</h3>
                <p class="card-description">You have 3 assignments due in the next 7 days</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 40%"></div>
                </div>
                <p class="text-muted mt-2">2 overdue, 1 upcoming</p>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon pink">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-count">88%</div>
                </div>
                <h3 class="card-title">Average Grade</h3>
                <p class="card-description">Your current academic performance across all courses</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 88%"></div>
                </div>
                <p class="text-muted mt-2">+2% from last semester</p>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon green">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-count">14</div>
                </div>
                <h3 class="card-title">Upcoming Events</h3>
                <p class="card-description">Lectures, exams, and deadlines in the next 30 days</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 30%"></div>
                </div>
                <p class="text-muted mt-2">Next: Math Exam in 3 days</p>
            </div>
            
            <!-- Recent Activity -->
            <div class="dashboard-card activity-card">
                <h3 class="card-title mb-4">Recent Activity</h3>
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon blue">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Submitted Computer Science Assignment</h4>
                            <p>Data Structures and Algorithms - Assignment 3</p>
                        </div>
                        <div class="activity-time">2 hours ago</div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Grade posted for Mathematics</h4>
                            <p>Calculus II - Midterm Exam: 94%</p>
                        </div>
                        <div class="activity-time">Yesterday</div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon purple">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="activity-content">
                            <h4>New announcement from Physics professor</h4>
                            <p>Lab schedule changes for next week</p>
                        </div>
                        <div class="activity-time">2 days ago</div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon blue">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Attended online lecture</h4>
                            <p>Database Management Systems - Lecture 12</p>
                        </div>
                        <div class="activity-time">3 days ago</div>
                    </li>
                </ul>
            </div>
            
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <h3 class="card-title mb-4">Quick Actions</h3>
                <div class="d-grid gap-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-upload"></i>
                        <span>Submit Assignment</span>
                    </button>
                    <button class="btn btn-google">
                        <i class="fas fa-calendar-plus"></i>
                        <span>View Calendar</span>
                    </button>
                    <button class="btn btn-google">
                        <i class="fas fa-comments"></i>
                        <span>Class Discussions</span>
                    </button>
                    <button class="btn btn-google">
                        <i class="fas fa-download"></i>
                        <span>Download Materials</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const authCard = document.querySelector('.container-custom');
            const dashboardContainer = document.getElementById('dashboardContainer');
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabIndicator = document.querySelector('.tab-indicator');
            const formWrappers = document.querySelectorAll('.form-wrapper');
            const switchTabLinks = document.querySelectorAll('.switch-tab');
            const loginFormElement = document.getElementById('loginFormElement');
            const registerFormElement = document.getElementById('registerFormElement');
            const loginButton = document.getElementById('loginButton');
            const registerButton = document.getElementById('registerButton');
            const logoutButton = document.getElementById('logoutButton');
            const loginAlert = document.getElementById('loginAlert');
            const registerAlert = document.getElementById('registerAlert');
            const loginPasswordToggle = document.getElementById('loginPasswordToggle');
            const registerPasswordToggle = document.getElementById('registerPasswordToggle');
            const passwordStrength = document.getElementById('passwordStrength');
            const registerPassword = document.getElementById('registerPassword');
            const userAvatar = document.getElementById('userAvatar');
            const dashboardUserName = document.getElementById('dashboardUserName');
            const dashboardUserEmail = document.getElementById('dashboardUserEmail');
            
            // Tab Switching
            tabButtons.forEach((button, index) => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    
                    // Update active tab button
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Move tab indicator
                    const buttonWidth = this.offsetWidth;
                    const buttonLeft = this.offsetLeft;
                    tabIndicator.style.width = `${buttonWidth}px`;
                    tabIndicator.style.left = `${buttonLeft}px`;
                    
                    // Show target form
                    formWrappers.forEach(wrapper => {
                        wrapper.classList.remove('active');
                        if (wrapper.id === target) {
                            wrapper.classList.add('active');
                        }
                    });
                    
                    // Hide alerts
                    hideAlerts();
                });
            });
            
            // Switch tab links
            switchTabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    
                    // Find and click the corresponding tab button
                    tabButtons.forEach(button => {
                        if (button.getAttribute('data-target') === target) {
                            button.click();
                        }
                    });
                });
            });
            
            // Password visibility toggle
            loginPasswordToggle.addEventListener('click', function() {
                const passwordInput = document.getElementById('loginPassword');
                const icon = this.querySelector('i');
                togglePasswordVisibility(passwordInput, icon);
            });
            
            registerPasswordToggle.addEventListener('click', function() {
                const passwordInput = document.getElementById('registerPassword');
                const icon = this.querySelector('i');
                togglePasswordVisibility(passwordInput, icon);
            });
            
            // Password strength indicator
            registerPassword.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                passwordStrength.style.width = `${strength}%`;
                
                // Update color based on strength
                if (strength < 30) {
                    passwordStrength.style.background = '#EF4444';
                } else if (strength < 70) {
                    passwordStrength.style.background = '#F59E0B';
                } else {
                    passwordStrength.style.background = 'linear-gradient(to right, var(--primary), var(--secondary))';
                }
            });
            
            // Login form submission
            loginFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('loginEmail').value;
                const password = document.getElementById('loginPassword').value;
                
                // Validation
                if (!validateEmail(email)) {
                    showAlert(loginAlert, 'Please enter a valid email address', 'error');
                    return;
                }
                
                if (password.length < 6) {
                    showAlert(loginAlert, 'Password must be at least 6 characters', 'error');
                    return;
                }
                
                // Simulate login process
                simulateLogin(email, password);
            });
            
            // Registration form submission
            registerFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const firstName = document.getElementById('firstName').value;
                const lastName = document.getElementById('lastName').value;
                const email = document.getElementById('registerEmail').value;
                const studentId = document.getElementById('studentId').value;
                const password = document.getElementById('registerPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                // Validation
                if (!firstName || !lastName) {
                    showAlert(registerAlert, 'Please enter your full name', 'error');
                    return;
                }
                
                if (!validateEmail(email)) {
                    showAlert(registerAlert, 'Please enter a valid email address', 'error');
                    return;
                }
                
                if (!studentId) {
                    showAlert(registerAlert, 'Please enter your student ID', 'error');
                    return;
                }
                
                if (password.length < 8) {
                    showAlert(registerAlert, 'Password must be at least 8 characters', 'error');
                    return;
                }
                
                if (password !== confirmPassword) {
                    showAlert(registerAlert, 'Passwords do not match', 'error');
                    return;
                }
                
                if (!document.getElementById('termsAgreement').checked) {
                    showAlert(registerAlert, 'You must agree to the terms and conditions', 'error');
                    return;
                }
                
                // Simulate registration process
                simulateRegistration(firstName, lastName, email, studentId, password);
            });
            
            // Logout
            logoutButton.addEventListener('click', function() {
                // Switch to auth card
                authCard.classList.remove('hidden');
                dashboardContainer.classList.add('hidden');
                
                // Clear login form
                document.getElementById('loginEmail').value = '';
                document.getElementById('loginPassword').value = '';
                document.getElementById('rememberMe').checked = false;
                
                // Show success message
                showAlert(loginAlert, 'You have been successfully signed out', 'success');
            });
            
            // Helper Functions
            function togglePasswordVisibility(passwordInput, icon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                
                // Length
                if (password.length >= 8) strength += 25;
                if (password.length >= 12) strength += 10;
                
                // Complexity
                if (/[A-Z]/.test(password)) strength += 20;
                if (/[0-9]/.test(password)) strength += 20;
                if (/[^A-Za-z0-9]/.test(password)) strength += 20;
                
                // Variety
                const charTypes = [/[A-Z]/, /[a-z]/, /[0-9]/, /[^A-Za-z0-9]/];
                const typeCount = charTypes.filter(regex => regex.test(password)).length;
                strength += (typeCount - 1) * 5;
                
                return Math.min(strength, 100);
            }
            
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function showAlert(alertElement, message, type) {
                // Hide all alerts first
                hideAlerts();
                
                // Configure alert
                alertElement.className = `alert alert-${type}`;
                alertElement.querySelector('span').textContent = message;
                alertElement.style.display = 'flex';
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    alertElement.style.display = 'none';
                }, 5000);
            }
            
            function hideAlerts() {
                loginAlert.style.display = 'none';
                registerAlert.style.display = 'none';
            }
            
            function simulateLogin(email, password) {
                // Show loading state
                loginButton.classList.add('loading');
                
                // Simulate API call
                setTimeout(() => {
                    // For demo, accept any login
                    const firstName = email.split('@')[0].split('.')[0] || 'Student';
                    const lastName = email.split('@')[0].split('.')[1] || 'User';
                    
                    const userData = {
                        firstName: firstName,
                        lastName: lastName,
                        email: email,
                        studentId: 'STU-' + new Date().getFullYear() + '-' + Math.floor(Math.random() * 1000),
                        major: ['Computer Science', 'Mathematics', 'Physics', 'Engineering'][Math.floor(Math.random() * 4)]
                    };
                    
                    // Store user data
                    localStorage.setItem('scholarSyncUser', JSON.stringify(userData));
                    
                    // Update dashboard
                    updateDashboard(userData);
                    
                    // Remove loading state
                    loginButton.classList.remove('loading');
                    
                    // Switch to dashboard
                    authCard.classList.add('hidden');
                    dashboardContainer.classList.remove('hidden');
                }, 1500);
            }
            
            function simulateRegistration(firstName, lastName, email, studentId, password) {
                // Show loading state
                registerButton.classList.add('loading');
                
                // Simulate API call
                setTimeout(() => {
                    const userData = {
                        firstName: firstName,
                        lastName: lastName,
                        email: email,
                        studentId: studentId,
                        major: ['Computer Science', 'Mathematics', 'Physics', 'Engineering'][Math.floor(Math.random() * 4)]
                    };
                    
                    // Store user data
                    localStorage.setItem('scholarSyncUser', JSON.stringify(userData));
                    
                    // Remove loading state
                    registerButton.classList.remove('loading');
                    
                    // Show success message
                    showAlert(registerAlert, 'Registration successful! You can now sign in.', 'success');
                    
                    // Clear form
                    registerFormElement.reset();
                    passwordStrength.style.width = '0%';
                    
                    // Switch to login form after delay
                    setTimeout(() => {
                        tabButtons.forEach(button => {
                            if (button.getAttribute('data-target') === 'loginForm') {
                                button.click();
                            }
                        });
                        
                        // Pre-fill email
                        document.getElementById('loginEmail').value = email;
                    }, 1500);
                }, 1500);
            }
            
            function updateDashboard(userData) {
                // Update user name
                const fullName = `${userData.firstName} ${userData.lastName}`;
                dashboardUserName.textContent = fullName;
                dashboardUserEmail.textContent = `${userData.email} • ${userData.major}`;
                
                // Update avatar initials
                const initials = (userData.firstName.charAt(0) + userData.lastName.charAt(0)).toUpperCase();
                userAvatar.textContent = initials;
                
                // Update avatar color based on name
                const colors = [
                    'linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%)',
                    'linear-gradient(135deg, var(--secondary) 0%, #7C3AED 100%)',
                    'linear-gradient(135deg, var(--accent) 0%, #DB2777 100%)',
                    'linear-gradient(135deg, var(--success) 0%, #059669 100%)'
                ];
                
                const colorIndex = userData.firstName.length % colors.length;
                userAvatar.style.background = colors[colorIndex];
            }
            
            // Check if user is already logged in
            const storedUser = localStorage.getItem('scholarSyncUser');
            if (storedUser) {
                const userData = JSON.parse(storedUser);
                updateDashboard(userData);
                authCard.classList.add('hidden');
                dashboardContainer.classList.remove('hidden');
            }
            
            // Initialize social login buttons
            document.querySelectorAll('.btn-google').forEach(btn => {
                btn.addEventListener('click', function() {
                    alert('In a real application, this would redirect to Google OAuth authentication.');
                });
            });
            
            // Initialize "Forgot password" link
            document.querySelector('.forgot-link').addEventListener('click', function(e) {
                e.preventDefault();
                const email = document.getElementById('loginEmail').value || 'your email';
                alert(`Password reset link would be sent to ${email} in a real application.`);
            });
        });
    </script>
</body>
</html>
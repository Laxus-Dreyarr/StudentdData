
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
            // const name = document.querySelector('.user-name').textContent;
            const name = $(".user-name2").val();
            
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

        async function logout(e) {
            e.preventDefault();
            
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch('/exe/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                document.cookie.split(";").forEach(function(c) {
                    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
                });
        
                // Clear localStorage and sessionStorage
                localStorage.clear();
                sessionStorage.clear();
                
                
                // Redirect regardless of response (logout should always redirect)
                window.location.href = '/';
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = '/';
            }
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
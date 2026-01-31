
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


        function handleEnrollment() {
            // Initialize variables
            let selectedSubjects = new Map();
            const studentId = document.querySelector('meta[name="student-id"]')?.content || 
                            document.getElementById('studentId')?.value;
            
            // Show modal if student hasn't enrolled yet
            function showEnrollmentModal() {
                const enrollmentModal = new bootstrap.Modal(document.getElementById('enrollmentModal'));
                enrollmentModal.show();
            }
            
            // Handle subject selection
            function handleSubjectSelection() {
                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('subject-checkbox')) {
                        const subjectId = e.target.dataset.subjectId;
                        const subjectRow = e.target.closest('.subject-row');
                        const gradeSelect = subjectRow.querySelector('.grade-select');
                        const undoBtn = subjectRow.querySelector('.undo-btn');
                        
                        if (e.target.checked) {
                            // Enable grade selection
                            gradeSelect.disabled = false;
                            subjectRow.classList.add('selected');
                            undoBtn.style.display = 'block';
                            
                            // Auto-select first grade option
                            if (gradeSelect.value === '') {
                                gradeSelect.value = '3.00'; // Default grade
                                updateSelectedSubject(subjectId, gradeSelect.value);
                            }
                        } else {
                            // Disable grade selection and remove selection
                            gradeSelect.disabled = true;
                            gradeSelect.value = '';
                            subjectRow.classList.remove('selected');
                            undoBtn.style.display = 'none';
                            removeSelectedSubject(subjectId);
                        }
                        
                        updateSummary();
                    }
                });
            }
            
            // Handle grade selection
            function handleGradeSelection() {
                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('grade-select') && e.target.value) {
                        const subjectId = e.target.dataset.subjectId;
                        updateSelectedSubject(subjectId, e.target.value);
                        updateSummary();
                    }
                });
            }
            
            // Handle undo button
            function handleUndoButton() {
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.undo-btn')) {
                        const subjectId = e.target.closest('.undo-btn').dataset.subjectId;
                        const checkbox = document.querySelector(`.subject-checkbox[data-subject-id="${subjectId}"]`);
                        
                        if (checkbox) {
                            checkbox.checked = false;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }
            
            // Update selected subject in map
            function updateSelectedSubject(subjectId, grade) {
                const subjectRow = document.querySelector(`.subject-row[data-subject-id="${subjectId}"]`);
                const subjectCode = subjectRow?.querySelector('td:nth-child(2)')?.textContent;
                const subjectName = subjectRow?.querySelector('td:nth-child(3)')?.textContent;
                const units = parseInt(subjectRow?.querySelector('.subject-checkbox')?.dataset.units) || 3;
                
                selectedSubjects.set(subjectId, {
                    id: subjectId,
                    code: subjectCode,
                    name: subjectName,
                    grade: grade,
                    units: units,
                    numericGrade: convertGradeToNumeric(grade)
                });
            }
            
            // Remove selected subject from map
            function removeSelectedSubject(subjectId) {
                selectedSubjects.delete(subjectId);
            }
            
            // Convert letter grade to numeric
            function convertGradeToNumeric(grade) {
                const gradeMap = {
                    '1.00': 1.00, '1.25': 1.25, '1.50': 1.50, '1.75': 1.75,
                    '2.00': 2.00, '2.25': 2.25, '2.50': 2.50, '2.75': 2.75,
                    '3.00': 3.00, '4.00': 4.00, '5.00': 5.00,
                    'INC': 0, 'DRP': 0, 'PASS': 1.00, 'FAIL': 5.00
                };
                return gradeMap[grade] || 0;
            }
            
            // Update summary display
            function updateSummary() {
                const selectedSubjectsList = document.getElementById('selectedSubjectsList');
                const totalSubjectsCount = document.getElementById('totalSubjectsCount');
                const gwaPreview = document.getElementById('gwaPreview');
                const submitBtn = document.getElementById('submitEnrollment');
                
                // Update count
                totalSubjectsCount.textContent = selectedSubjects.size;
                
                // Update list
                if (selectedSubjects.size === 0) {
                    selectedSubjectsList.innerHTML = '<p class="text-muted">No subjects selected yet.</p>';
                    gwaPreview.textContent = '0.00';
                    submitBtn.disabled = true;
                    return;
                }
                
                // Build subjects list
                let listHTML = '';
                let totalGradePoints = 0;
                let totalUnits = 0;
                
                selectedSubjects.forEach(subject => {
                    listHTML += `
                        <div class="subject-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${subject.code}</strong>: ${subject.name}
                                <span class="badge bg-secondary ms-2">${subject.units} units</span>
                            </div>
                            <div>
                                <span class="badge ${getGradeBadgeClass(subject.grade)}">
                                    Grade: ${subject.grade}
                                </span>
                            </div>
                        </div>
                    `;
                    
                    // Calculate for GWA
                    totalGradePoints += (subject.numericGrade * subject.units);
                    totalUnits += subject.units;
                });
                
                selectedSubjectsList.innerHTML = listHTML;
                
                // Calculate and display GWA
                const gwa = totalUnits > 0 ? totalGradePoints / totalUnits : 0;
                gwaPreview.textContent = gwa.toFixed(2);
                
                // Enable submit button if at least one subject selected
                submitBtn.disabled = selectedSubjects.size === 0;
            }
            
            // Get badge class based on grade
            function getGradeBadgeClass(grade) {
                const numericGrade = convertGradeToNumeric(grade);
                if (numericGrade <= 1.75) return 'bg-success';
                if (numericGrade <= 2.75) return 'bg-warning text-dark';
                return 'bg-danger';
            }
            
            // Handle form submission
            function handleSubmission() {
                document.getElementById('submitEnrollment').addEventListener('click', async function() {
                    if (selectedSubjects.size === 0) {
                        alert('Please select at least one subject.');
                        return;
                    }
                    
                    // Confirm submission
                    if (!confirm(`You are about to submit ${selectedSubjects.size} subjects. This action cannot be undone. Proceed?`)) {
                        return;
                    }
                    
                    const submitBtn = this;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                    
                    try {
                        // Prepare data
                        const enrollmentData = Array.from(selectedSubjects.values()).map(subject => ({
                            subject_id: subject.id,
                            grade: subject.grade
                        }));
                        
                        // Send request
                        const response = await fetch('/student/enroll-subjects', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                student_id: studentId,
                                enrolled_subjects: enrollmentData
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Show success message
                            alert(`Successfully enrolled ${result.enrollment_count} subjects! Your GWA is ${result.gwa}.`);
                            
                            // Close modal and reload page
                            const modal = bootstrap.Modal.getInstance(document.getElementById('enrollmentModal'));
                            modal.hide();
                            
                            // Reload page to show updated dashboard
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            throw new Error(result.message || 'Submission failed');
                        }
                        
                    } catch (error) {
                        alert('Error: ' + error.message);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            }
            
            // Initialize all event listeners
            function init() {
                // Check if modal should be shown
                if (document.getElementById('enrollmentModal')) {
                    setTimeout(showEnrollmentModal, 1000); // Show after 1 second
                    
                    // Initialize event listeners
                    handleSubjectSelection();
                    handleGradeSelection();
                    handleUndoButton();
                    handleSubmission();
                }
            }
            
            // Public methods
            return {
                init: init,
                getSelectedSubjects: () => selectedSubjects,
                calculateGWA: () => {
                    let totalGradePoints = 0;
                    let totalUnits = 0;
                    
                    selectedSubjects.forEach(subject => {
                        totalGradePoints += (subject.numericGrade * subject.units);
                        totalUnits += subject.units;
                    });
                    
                    return totalUnits > 0 ? totalGradePoints / totalUnits : 0;
                }
            };
        }
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            updateWelcomeMessage();
            updateClassStatus();
            updateAssignmentDeadlines();
            setupSidebarNavigation();
            
            // Update class status every minute
            setInterval(updateClassStatus, 60000);

            const enrollment = handleEnrollment();
            enrollment.init();
            
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
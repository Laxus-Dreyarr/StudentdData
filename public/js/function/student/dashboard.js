
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

        // 
        function handleEnrollment() {
            // Initialize variables
            let selectedSubjects = new Map();
            const studentId = document.querySelector('meta[name="student-id"]')?.content || 
                            document.getElementById('studentId')?.value;
            
            // Show modal if student hasn't enrolled yet
            function showEnrollmentModal() {
                const enrollmentModal = new bootstrap.Modal(document.getElementById('enrollmentModal'));
                enrollmentModal.show();
                
                // Initialize search functionality
                initSearch();
                initFilters();
            }
            
            // Initialize search functionality - FIXED
            function initSearch() {
                const searchInput = document.getElementById('subjectSearch');
                const clearSearchBtn = document.getElementById('clearSearch');
                
                if (!searchInput) return;
                
                // Search as user types - FIXED: Use debounce for better performance
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase().trim();
                        filterSubjects(searchTerm);
                    }, 300);
                });
                
                // Clear search button - FIXED
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    filterSubjects('');
                    searchInput.focus();
                });
            }
            
            // Initialize filter checkboxes - FIXED
            function initFilters() {
                const showSelectedOnly = document.getElementById('showSelectedOnly');
                const hideCompleted = document.getElementById('hideCompleted');
                
                if (showSelectedOnly) {
                    showSelectedOnly.addEventListener('change', function() {
                        filterSubjects(document.getElementById('subjectSearch').value);
                    });
                }
                
                if (hideCompleted) {
                    hideCompleted.addEventListener('change', function() {
                        filterSubjects(document.getElementById('subjectSearch').value);
                    });
                }
            }
            
            // Filter subjects based on search term and filters - FIXED
            function filterSubjects(searchTerm) {
                const showSelectedOnly = document.getElementById('showSelectedOnly')?.checked || false;
                const hideCompleted = document.getElementById('hideCompleted')?.checked || false;
                
                document.querySelectorAll('.subject-row').forEach(row => {
                    const code = row.dataset.code || '';
                    const name = row.dataset.name || '';
                    const subjectId = row.dataset.subjectId;
                    const isSelected = selectedSubjects.has(subjectId);
                    
                    // Convert to lowercase for case-insensitive search - FIXED
                    const codeLower = code.toLowerCase();
                    const nameLower = name.toLowerCase();
                    
                    // Check if row matches search term - FIXED
                    const matchesSearch = searchTerm === '' || 
                        codeLower.includes(searchTerm) || 
                        nameLower.includes(searchTerm);
                    
                    // Check if row matches filters - FIXED: Don't hide selected subjects
                    let matchesFilters = true;
                    
                    if (showSelectedOnly && !isSelected) {
                        matchesFilters = false;
                    }
                    
                    if (hideCompleted && isSelected) {
                        matchesFilters = false;
                    }
                    
                    // Show or hide row - FIXED: Only hide if doesn't match search/filters
                    if (matchesSearch && matchesFilters) {
                        row.classList.remove('hidden');
                        // Only highlight if there's a search term
                        if (searchTerm !== '') {
                            row.classList.add('highlight');
                        } else {
                            row.classList.remove('highlight');
                        }
                    } else {
                        row.classList.add('hidden');
                        row.classList.remove('highlight');
                    }
                });
                
                // Update year level and semester visibility - FIXED
                updateSectionVisibility();
            }
            
            // Update visibility of year level and semester sections - FIXED
            function updateSectionVisibility() {
                document.querySelectorAll('.year-level-section').forEach(yearSection => {
                    let yearHasVisible = false;
                    const semesterSections = yearSection.querySelectorAll('.semester-section');
                    
                    semesterSections.forEach(semesterSection => {
                        const visibleRows = semesterSection.querySelectorAll('.subject-row:not(.hidden)');
                        if (visibleRows.length === 0) {
                            semesterSection.style.display = 'none';
                        } else {
                            semesterSection.style.display = 'block';
                            yearHasVisible = true;
                        }
                    });
                    
                    // Show/hide year level
                    yearSection.style.display = yearHasVisible ? 'block' : 'none';
                });
            }
            
            // Handle subject selection - FIXED: Removed filterSubjects call
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
                            
                            // Auto-select default grade (3.0 as passing)
                            if (gradeSelect.value === '') {
                                gradeSelect.value = '3.0'; // Default passing grade
                                updateSelectedSubject(subjectId, gradeSelect.value);
                            } else {
                                // If grade already selected, update it
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
                        // REMOVED: filterSubjects(document.getElementById('subjectSearch').value);
                    }
                });
            }
            
            // Handle grade selection - FIXED: Removed filterSubjects call
            function handleGradeSelection() {
                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('grade-select')) {
                        const subjectId = e.target.dataset.subjectId;
                        const checkbox = document.querySelector(`.subject-checkbox[data-subject-id="${subjectId}"]`);
                        
                        // Only update if subject is checked
                        if (checkbox && checkbox.checked && e.target.value) {
                            updateSelectedSubject(subjectId, e.target.value);
                            updateSummary();
                        }
                    }
                });
            }
            
            // Handle undo button - FIXED
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
            
            // Update selected subject in map - FIXED
            function updateSelectedSubject(subjectId, grade) {
                const subjectRow = document.querySelector(`.subject-row[data-subject-id="${subjectId}"]`);
                if (!subjectRow) return;
                
                const subjectCode = subjectRow.querySelector('.subject-code')?.textContent;
                const subjectName = subjectRow.querySelector('.subject-name')?.textContent;
                const units = parseInt(subjectRow.querySelector('.subject-checkbox')?.dataset.units) || 3;
                
                selectedSubjects.set(subjectId, {
                    id: subjectId,
                    code: subjectCode,
                    name: subjectName,
                    grade: grade,
                    units: units,
                    numericGrade: convertGradeToNumeric(grade)
                });
            }
            
            // Remove selected subject from map - FIXED
            function removeSelectedSubject(subjectId) {
                selectedSubjects.delete(subjectId);
            }
            
            // Convert letter grade to numeric based on EVSU system - FIXED
            function convertGradeToNumeric(grade) {
                const gradeMap = {
                    // Passing Grades (1.0 to 3.0)
                    '1.0': 1.0, '1.1': 1.1, '1.2': 1.2, '1.3': 1.3, '1.4': 1.4,
                    '1.5': 1.5, '1.6': 1.6, '1.7': 1.7, '1.8': 1.8, '1.9': 1.9,
                    '2.0': 2.0, '2.1': 2.1, '2.2': 2.2, '2.3': 2.3, '2.4': 2.4,
                    '2.5': 2.5, '2.6': 2.6, '2.7': 2.7, '2.8': 2.8, '2.9': 2.9,
                    '3.0': 3.0,
                    
                    // Failing and Special Grades
                    '4.0': 4.0, '5.0': 5.0,
                    'INC': 0.0, 'DRP': 0.0,
                    'PASS': 1.0, 'FAIL': 5.0
                };
                return gradeMap[grade] || 0;
            }
            
            // Get grade category for styling - FIXED
            function getGradeCategory(numericGrade) {
                if (numericGrade >= 1.0 && numericGrade <= 1.5) return 'excellent';
                if (numericGrade >= 1.6 && numericGrade <= 2.0) return 'good';
                if (numericGrade >= 2.1 && numericGrade <= 2.5) return 'fair';
                if (numericGrade >= 2.6 && numericGrade <= 3.0) return 'passing';
                if (numericGrade >= 4.0 && numericGrade <= 5.0) return 'failing';
                return 'special'; // For INC, DRP, etc.
            }
            
            // Get badge class based on grade - FIXED
            function getGradeBadgeClass(grade) {
                const numericGrade = convertGradeToNumeric(grade);
                const category = getGradeCategory(numericGrade);
                
                switch(category) {
                    case 'excellent': return 'bg-success';
                    case 'good': return 'bg-success';
                    case 'fair': return 'bg-warning text-dark';
                    case 'passing': return 'bg-warning text-dark';
                    case 'failing': return 'bg-danger';
                    default: return 'bg-secondary';
                }
            }
            
            // Update summary display - FIXED
            function updateSummary() {
                const selectedSubjectsList = document.getElementById('selectedSubjectsList');
                const totalSubjectsCount = document.getElementById('totalSubjectsCount');
                const totalUnitsCount = document.getElementById('totalUnitsCount');
                const gwaPreview = document.getElementById('gwaPreview');
                const submitBtn = document.getElementById('submitEnrollment');
                
                if (!selectedSubjectsList || !totalSubjectsCount || !totalUnitsCount || !gwaPreview || !submitBtn) {
                    console.error('Required summary elements not found');
                    return;
                }
                
                // Update counts
                totalSubjectsCount.textContent = selectedSubjects.size;
                
                // Update list and calculate totals
                if (selectedSubjects.size === 0) {
                    selectedSubjectsList.innerHTML = '<p class="text-muted">No subjects selected yet.</p>';
                    totalUnitsCount.textContent = '0';
                    gwaPreview.textContent = '0.00';
                    submitBtn.disabled = true;
                    updateGradeDistribution([]);
                    return;
                }
                
                // Build subjects list and calculate totals
                let listHTML = '';
                let totalGradePoints = 0;
                let totalUnits = 0;
                let gradeStats = [];
                
                selectedSubjects.forEach(subject => {
                    listHTML += `
                        <div class="subject-item d-flex justify-content-between align-items-center mb-2 p-2">
                            <div>
                                <strong>${subject.code}</strong>: ${subject.name}
                                <span class="badge bg-light text-dark border ms-2">${subject.units} units</span>
                            </div>
                            <div>
                                <span class="badge ${getGradeBadgeClass(subject.grade)} grade-badge">
                                    ${subject.grade}
                                </span>
                                <small class="text-muted ms-2">${(subject.numericGrade * subject.units).toFixed(2)} pts</small>
                            </div>
                        </div>
                    `;
                    
                    // Calculate for GWA
                    totalGradePoints += (subject.numericGrade * subject.units);
                    totalUnits += subject.units;
                    
                    // Collect grade stats for distribution
                    gradeStats.push({
                        grade: subject.numericGrade,
                        units: subject.units,
                        category: getGradeCategory(subject.numericGrade)
                    });
                });
                
                selectedSubjectsList.innerHTML = listHTML;
                totalUnitsCount.textContent = totalUnits;
                
                // Calculate and display GWA
                const gwa = totalUnits > 0 ? totalGradePoints / totalUnits : 0;
                gwaPreview.textContent = gwa.toFixed(2);
                
                // Update grade distribution
                updateGradeDistribution(gradeStats);
                
                // Enable submit button if at least one subject selected
                submitBtn.disabled = selectedSubjects.size === 0;
            }
            
            // Update grade distribution display - FIXED
            function updateGradeDistribution(gradeStats) {
                const distribution = {
                    excellent: 0,
                    good: 0,
                    fair: 0,
                    passing: 0,
                    failing: 0,
                    special: 0
                };
                
                // Count subjects in each category
                gradeStats.forEach(stat => {
                    if (distribution[stat.category] !== undefined) {
                        distribution[stat.category]++;
                    }
                });
                
                const total = gradeStats.length;
                
                // Update progress bars if they exist
                const passingBar = document.getElementById('passingGradeBar');
                const conditionalBar = document.getElementById('conditionalGradeBar');
                const failingBar = document.getElementById('failingGradeBar');
                
                if (passingBar && conditionalBar && failingBar && total > 0) {
                    const passingPercent = (distribution.excellent / total) * 100;
                    const conditionalPercent = ((distribution.good + distribution.fair + distribution.passing) / total) * 100;
                    const failingPercent = (distribution.failing / total) * 100;
                    
                    passingBar.style.width = `${passingPercent}%`;
                    conditionalBar.style.width = `${conditionalPercent}%`;
                    failingBar.style.width = `${failingPercent}%`;
                    
                    // Add text to bars if they have significant width
                    if (distribution.excellent > 0) {
                        passingBar.textContent = `${distribution.excellent}`;
                    } else {
                        passingBar.textContent = '';
                    }
                    
                    if ((distribution.good + distribution.fair + distribution.passing) > 0) {
                        conditionalBar.textContent = `${distribution.good + distribution.fair + distribution.passing}`;
                    } else {
                        conditionalBar.textContent = '';
                    }
                    
                    if (distribution.failing > 0) {
                        failingBar.textContent = `${distribution.failing}`;
                    } else {
                        failingBar.textContent = '';
                    }
                }
            }
            
            // Calculate GWA - FIXED
            function calculateGWA() {
                let totalGradePoints = 0;
                let totalUnits = 0;
                
                selectedSubjects.forEach(subject => {
                    totalGradePoints += (subject.numericGrade * subject.units);
                    totalUnits += subject.units;
                });
                
                return totalUnits > 0 ? totalGradePoints / totalUnits : 0;
            }
            
            // Handle form submission - FIXED
            function handleSubmission() {
                const submitBtn = document.getElementById('submitEnrollment');
                if (!submitBtn) return;
                
                submitBtn.addEventListener('click', async function() {
                    if (selectedSubjects.size === 0) {
                        alert('Please select at least one subject.');
                        return;
                    }
                    
                    // Confirm submission
                    const confirmMessage = `
        You are about to submit ${selectedSubjects.size} subjects.

        This action cannot be undone. Proceed with submission?
                    `.trim();
                    
                    if (!confirm(confirmMessage)) {
                        return;
                    }
                    
                    const originalText = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                    
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
                            const successMessage = `
        ✅ Successfully enrolled ${result.enrollment_count} subjects!

        • Final GWA: ${result.gwa}
        • Status: Officially Enrolled

        The page will refresh in a moment...
                            `.trim();
                            
                            alert(successMessage);
                            
                            // Close modal and reload page
                            const modal = bootstrap.Modal.getInstance(document.getElementById('enrollmentModal'));
                            if (modal) modal.hide();
                            
                            // Reload page to show updated dashboard
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            throw new Error(result.message || 'Submission failed');
                        }
                        
                    } catch (error) {
                        alert('Error: ' + error.message);
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }
                });
            }
            
            // Initialize all event listeners - FIXED
            function init() {
                // Check if modal should be shown
                if (document.getElementById('enrollmentModal')) {
                    // Don't auto-show immediately, let user see dashboard first
                    setTimeout(showEnrollmentModal, 1500);
                    
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
                calculateGWA: calculateGWA
            };
        }
        // 
        
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
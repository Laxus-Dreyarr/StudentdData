
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
            
            // Grade categories for distribution
            const gradeCategories = {
                'excellent': { min: 1.0, max: 1.5, color: '#28a745', label: '1.0-1.5' },
                'good': { min: 1.6, max: 2.0, color: '#20c997', label: '1.6-2.0' },
                'fair': { min: 2.1, max: 2.5, color: '#ffc107', label: '2.1-2.5' },
                'passing': { min: 2.6, max: 3.0, color: '#fd7e14', label: '2.6-3.0' },
                'failing': { min: 4.0, max: 5.0, color: '#dc3545', label: '4.0-5.0' },
                'special': { color: '#6c757d', label: 'Special' }
            };
            
            // Show modal if student hasn't enrolled yet
            function showEnrollmentModal() {
                const enrollmentModal = new bootstrap.Modal(document.getElementById('enrollmentModal'));
                enrollmentModal.show();
                
                // Initialize search functionality
                initSearch();
                initFilters();
            }
            
            // Initialize search functionality
            function initSearch() {
                const searchInput = document.getElementById('subjectSearch');
                const clearSearchBtn = document.getElementById('clearSearch');
                
                if (!searchInput) return;
                
                // Search as user types
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    filterSubjects(searchTerm);
                });
                
                // Clear search button
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    filterSubjects('');
                    searchInput.focus();
                });
                
                // Keyboard shortcuts
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        filterSubjects('');
                    }
                    if (e.key === 'Enter') {
                        // Focus on first matching subject
                        const firstVisible = document.querySelector('.subject-row:not(.hidden)');
                        if (firstVisible) {
                            const checkbox = firstVisible.querySelector('.subject-checkbox');
                            checkbox.focus();
                        }
                    }
                });
            }
            
            // Initialize filter checkboxes
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
            
            // Filter subjects based on search term and filters
            function filterSubjects(searchTerm) {
                const showSelectedOnly = document.getElementById('showSelectedOnly')?.checked || false;
                const hideCompleted = document.getElementById('hideCompleted')?.checked || false;
                
                document.querySelectorAll('.subject-row').forEach(row => {
                    const code = row.dataset.code || '';
                    const name = row.dataset.name || '';
                    const subjectId = row.dataset.subjectId;
                    const isSelected = selectedSubjects.has(subjectId);
                    
                    // Check if row matches search term
                    const matchesSearch = searchTerm === '' || 
                        code.includes(searchTerm) || 
                        name.includes(searchTerm);
                    
                    // Check if row matches filters
                    let matchesFilters = true;
                    
                    if (showSelectedOnly && !isSelected) {
                        matchesFilters = false;
                    }
                    
                    if (hideCompleted && isSelected) {
                        matchesFilters = false;
                    }
                    
                    // Show or hide row
                    if (matchesSearch && matchesFilters) {
                        row.classList.remove('hidden');
                        row.classList.add('highlight', searchTerm !== '');
                    } else {
                        row.classList.add('hidden');
                        row.classList.remove('highlight');
                    }
                });
                
                // Update year level and semester visibility
                updateSectionVisibility();
            }
            
            // Update visibility of year level and semester sections
            function updateSectionVisibility() {
                document.querySelectorAll('.year-level-section').forEach(yearSection => {
                    const visibleRows = yearSection.querySelectorAll('.subject-row:not(.hidden)');
                    const semesterSections = yearSection.querySelectorAll('.semester-section');
                    
                    // Hide semester sections with no visible rows
                    semesterSections.forEach(semesterSection => {
                        const semesterVisibleRows = semesterSection.querySelectorAll('.subject-row:not(.hidden)');
                        if (semesterVisibleRows.length === 0) {
                            semesterSection.style.display = 'none';
                        } else {
                            semesterSection.style.display = 'block';
                        }
                    });
                    
                    // Hide year level if all semester sections are hidden
                    const visibleSemesters = yearSection.querySelectorAll('.semester-section[style="display: block"]');
                    if (visibleSemesters.length === 0) {
                        yearSection.style.display = 'none';
                    } else {
                        yearSection.style.display = 'block';
                    }
                });
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
                            
                            // Auto-select default grade (3.0 as passing)
                            if (gradeSelect.value === '') {
                                gradeSelect.value = '3.0'; // Default passing grade
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
                        filterSubjects(document.getElementById('subjectSearch').value);
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
                const subjectCode = subjectRow?.querySelector('.subject-code')?.textContent;
                const subjectName = subjectRow?.querySelector('.subject-name')?.textContent;
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
            
            // Convert letter grade to numeric based on EVSU system
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
            
            // Get grade category for styling
            function getGradeCategory(numericGrade) {
                if (numericGrade >= 1.0 && numericGrade <= 1.5) return 'excellent';
                if (numericGrade >= 1.6 && numericGrade <= 2.0) return 'good';
                if (numericGrade >= 2.1 && numericGrade <= 2.5) return 'fair';
                if (numericGrade >= 2.6 && numericGrade <= 3.0) return 'passing';
                if (numericGrade >= 4.0 && numericGrade <= 5.0) return 'failing';
                return 'special'; // For INC, DRP, etc.
            }
            
            // Get badge class based on grade
            function getGradeBadgeClass(grade) {
                const numericGrade = convertGradeToNumeric(grade);
                const category = getGradeCategory(numericGrade);
                
                switch(category) {
                    case 'excellent': return 'bg-success';
                    case 'good': return 'bg-success'; // Slightly lighter green could be used
                    case 'fair': return 'bg-warning text-dark';
                    case 'passing': return 'bg-warning text-dark';
                    case 'failing': return 'bg-danger';
                    default: return 'bg-secondary'; // For INC, DRP
                }
            }
            
            // Update summary display
            function updateSummary() {
                const selectedSubjectsList = document.getElementById('selectedSubjectsList');
                const totalSubjectsCount = document.getElementById('totalSubjectsCount');
                const totalUnitsCount = document.getElementById('totalUnitsCount');
                const gwaPreview = document.getElementById('gwaPreview');
                const submitBtn = document.getElementById('submitEnrollment');
                
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
                    const category = getGradeCategory(subject.numericGrade);
                    const categoryColor = gradeCategories[category]?.color || '#6c757d';
                    
                    listHTML += `
                        <div class="subject-item d-flex justify-content-between align-items-center mb-2 p-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 8px; height: 20px; background-color: ${categoryColor}; 
                                    margin-right: 10px; border-radius: 2px;"></div>
                                <div>
                                    <strong>${subject.code}</strong>: ${subject.name}
                                    <span class="badge bg-light text-dark border ms-2">${subject.units} units</span>
                                </div>
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
                        category: category
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
            
            // Update grade distribution display
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
                
                // Calculate percentages for progress bars
                const total = gradeStats.length;
                let passingCount = distribution.excellent + distribution.good + distribution.fair + distribution.passing;
                let failingCount = distribution.failing;
                let specialCount = distribution.special;
                
                // Update progress bars
                const passingBar = document.getElementById('passingGradeBar');
                const conditionalBar = document.getElementById('conditionalGradeBar');
                const failingBar = document.getElementById('failingGradeBar');
                
                if (passingBar && conditionalBar && failingBar) {
                    const passingPercent = total > 0 ? (distribution.excellent / total) * 100 : 0;
                    const conditionalPercent = total > 0 ? ((distribution.good + distribution.fair + distribution.passing) / total) * 100 : 0;
                    const failingPercent = total > 0 ? (distribution.failing / total) * 100 : 0;
                    
                    passingBar.style.width = `${passingPercent}%`;
                    passingBar.textContent = distribution.excellent > 0 ? `${distribution.excellent}` : '';
                    
                    conditionalBar.style.width = `${conditionalPercent}%`;
                    conditionalBar.textContent = (distribution.good + distribution.fair + distribution.passing) > 0 ? 
                        `${distribution.good + distribution.fair + distribution.passing}` : '';
                    
                    failingBar.style.width = `${failingPercent}%`;
                    failingBar.textContent = distribution.failing > 0 ? `${distribution.failing}` : '';
                }
                
                // Update distribution text
                const distributionElement = document.getElementById('gradeDistribution');
                if (distributionElement) {
                    let html = '<small class="text-muted">';
                    if (total === 0) {
                        html += 'No grades selected';
                    } else {
                        html += `Passing: ${passingCount} | Failing: ${failingCount} | Special: ${specialCount}`;
                    }
                    html += '</small>';
                    distributionElement.innerHTML = html;
                }
            }
            
            // Handle form submission
            function handleSubmission() {
                const submitBtn = document.getElementById('submitEnrollment');
                if (!submitBtn) return;
                
                submitBtn.addEventListener('click', async function() {
                    if (selectedSubjects.size === 0) {
                        alert('Please select at least one subject.');
                        return;
                    }
                    
                    // Calculate final GWA for confirmation
                    const finalGWA = calculateGWA();
                    
                    // Confirm submission with details
                    const confirmMessage = `
        You are about to submit ${selectedSubjects.size} subjects.

        • Total Units: ${document.getElementById('totalUnitsCount').textContent}
        • Preliminary GWA: ${finalGWA.toFixed(2)}

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
                                // student_id: studentId,
                                enrolled_subjects: enrollmentData
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Show success message with GWA
                            const successMessage = `
        ✅ Successfully enrolled ${result.enrollment_count} subjects!

        • Final GWA: ${result.gwa}
        • Status: Officially Enrolled

        The page will refresh in a moment...
                            `.trim();
                            
                            alert(successMessage);
                            
                            // Close modal and reload page
                            const modal = bootstrap.Modal.getInstance(document.getElementById('enrollmentModal'));
                            modal.hide();
                            
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
            
            // Calculate GWA
            function calculateGWA() {
                let totalGradePoints = 0;
                let totalUnits = 0;
                
                selectedSubjects.forEach(subject => {
                    totalGradePoints += (subject.numericGrade * subject.units);
                    totalUnits += subject.units;
                });
                
                return totalUnits > 0 ? totalGradePoints / totalUnits : 0;
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
                calculateGWA: calculateGWA
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

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

        // ---------------------------------
        // Add these functions to the handleEnrollment function

// Toggle between table and dropdown views
// Initialize view toggle
function initViewToggle() {
    const tableViewBtn = document.getElementById('tableViewBtn');
    const cardViewBtn = document.getElementById('cardViewBtn');
    const quickAddBtnToggle = document.getElementById('quickAddBtnToggle');
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    
    if (!tableViewBtn || !cardViewBtn) return;
    
    tableViewBtn.addEventListener('click', function() {
        tableViewBtn.classList.add('active');
        cardViewBtn.classList.remove('active');
        quickAddBtnToggle.classList.remove('active');
        tableView.style.display = 'block';
        cardView.style.display = 'none';
    });
    
    cardViewBtn.addEventListener('click', function() {
        cardViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
        quickAddBtnToggle.classList.remove('active');
        cardView.style.display = 'block';
        tableView.style.display = 'none';
    });
    
    quickAddBtnToggle.addEventListener('click', function() {
        // If we want a dedicated quick add view, implement it here
        // For now, just switch to card view which has quick add
        cardViewBtn.click();
    });
}

// Sync selections to table view
function syncSelectionsToTableView() {
    selectedSubjects.forEach((subject, subjectId) => {
        const checkbox = document.querySelector(`#subject_${subjectId}`);
        const gradeSelect = document.querySelector(`.grade-select[data-subject-id="${subjectId}"]`);
        const undoBtn = document.querySelector(`.undo-btn[data-subject-id="${subjectId}"]`);
        const row = checkbox?.closest('.subject-row');
        
        if (checkbox && gradeSelect && row) {
            checkbox.checked = true;
            gradeSelect.disabled = false;
            gradeSelect.value = subject.grade;
            row.classList.add('selected');
            if (undoBtn) undoBtn.style.display = 'block';
        }
    });
}

// Sync selections to dropdown view
function syncSelectionsToDropdownView() {
    selectedSubjects.forEach((subject, subjectId) => {
        const checkbox = document.querySelector(`#subject_dropdown_${subjectId}`);
        const gradeSelect = document.querySelector(`.grade-select-dropdown[data-subject-id="${subjectId}"]`);
        const undoBtn = document.querySelector(`.undo-btn-dropdown[data-subject-id="${subjectId}"]`);
        const card = checkbox?.closest('.subject-card');
        const gradeSelectionDiv = card?.querySelector('.grade-selection');
        
        if (checkbox && gradeSelect && card) {
            checkbox.checked = true;
            gradeSelect.value = subject.grade;
            card.classList.add('selected');
            if (gradeSelectionDiv) gradeSelectionDiv.style.display = 'block';
            if (undoBtn) undoBtn.style.display = 'block';
        }
    });
}

// Handle dropdown view selection
function handleDropdownSelection() {
    document.addEventListener('change', function(e) {
        // Handle dropdown view checkboxes
        if (e.target.classList.contains('subject-checkbox-dropdown')) {
            const subjectId = e.target.dataset.subjectId;
            const card = e.target.closest('.subject-card');
            const gradeSelectionDiv = card?.querySelector('.grade-selection');
            const gradeSelect = card?.querySelector('.grade-select-dropdown');
            const undoBtn = card?.querySelector('.undo-btn-dropdown');
            
            if (e.target.checked) {
                // Show grade selection
                card.classList.add('selected');
                if (gradeSelectionDiv) gradeSelectionDiv.style.display = 'block';
                if (undoBtn) undoBtn.style.display = 'block';
                
                // Auto-select default grade
                if (gradeSelect && gradeSelect.value === '') {
                    gradeSelect.value = '3.0';
                    updateSelectedSubject(subjectId, gradeSelect.value);
                } else if (gradeSelect && gradeSelect.value) {
                    updateSelectedSubject(subjectId, gradeSelect.value);
                }
            } else {
                // Hide grade selection and remove subject
                card.classList.remove('selected');
                if (gradeSelectionDiv) gradeSelectionDiv.style.display = 'none';
                if (undoBtn) undoBtn.style.display = 'none';
                if (gradeSelect) gradeSelect.value = '';
                removeSelectedSubject(subjectId);
            }
            
            updateSummary();
        }
        
        // Handle dropdown view grade selection
        if (e.target.classList.contains('grade-select-dropdown') && e.target.value) {
            const subjectId = e.target.dataset.subjectId;
            const checkbox = document.querySelector(`#subject_dropdown_${subjectId}`);
            
            if (checkbox && checkbox.checked) {
                updateSelectedSubject(subjectId, e.target.value);
                updateSummary();
            }
        }
    });
    
    // Handle dropdown view undo button
    document.addEventListener('click', function(e) {
        if (e.target.closest('.undo-btn-dropdown')) {
            const subjectId = e.target.closest('.undo-btn-dropdown').dataset.subjectId;
            const checkbox = document.querySelector(`#subject_dropdown_${subjectId}`);
            
            if (checkbox) {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            }
        }
    });
}

// Handle quick add functionality
function initQuickAdd() {
    const quickYearSelect = document.getElementById('quickYearSelect');
    const quickSemesterSelect = document.getElementById('quickSemesterSelect');
    const quickSubjectSelect = document.getElementById('quickSubjectSelect');
    const quickGradeSelect = document.getElementById('quickGradeSelect');
    const quickAddBtn = document.getElementById('quickAddBtn');
    
    if (!quickAddBtn) return;
    
    // Filter subjects based on year and semester selection
    if (quickYearSelect && quickSemesterSelect && quickSubjectSelect) {
        quickYearSelect.addEventListener('change', filterQuickSubjects);
        quickSemesterSelect.addEventListener('change', filterQuickSubjects);
    }
    
    quickAddBtn.addEventListener('click', function() {
        const subjectId = quickSubjectSelect.value;
        const grade = quickGradeSelect.value;
        
        if (!subjectId) {
            alert('Please select a subject first.');
            return;
        }
        
        if (!grade) {
            alert('Please select a grade.');
            return;
        }
        
        const option = quickSubjectSelect.options[quickSubjectSelect.selectedIndex];
        const subjectCode = option.text.split(' - ')[0];
        const units = option.dataset.units || 3;
        
        // Add subject to selected subjects
        if (!selectedSubjects.has(subjectId)) {
            selectedSubjects.set(subjectId, {
                id: subjectId,
                code: subjectCode,
                name: option.text.split(' - ')[1]?.split(' (')[0] || '',
                grade: grade,
                units: parseInt(units),
                numericGrade: convertGradeToNumeric(grade)
            });
            
            // Update UI in both views
            updateCheckboxInBothViews(subjectId, true, grade);
            updateSummary();
            
            // Reset quick add form
            quickSubjectSelect.value = '';
            quickGradeSelect.value = '';
            
            // Show success message
            showToast(`Added ${subjectCode} with grade ${grade}`);
        } else {
            alert('This subject is already selected.');
        }
    });
}

// Filter quick add subjects
function filterQuickSubjects() {
    const year = document.getElementById('quickYearSelect').value;
    const semester = document.getElementById('quickSemesterSelect').value;
    const subjectSelect = document.getElementById('quickSubjectSelect');
    
    if (!subjectSelect) return;
    
    // Store all options
    const allOptions = Array.from(subjectSelect.options);
    
    // Clear current options (except first)
    subjectSelect.innerHTML = '<option value="">-- Select a subject --</option>';
    
    // Filter and add options
    allOptions.forEach(option => {
        if (option.value === '') return;
        
        const optionYear = option.dataset.year;
        const optionSemester = option.dataset.semester;
        
        const matchesYear = !year || optionYear === year;
        const matchesSemester = !semester || optionSemester === semester;
        
        if (matchesYear && matchesSemester) {
            subjectSelect.appendChild(option);
        }
    });
}

// Update checkbox in both views
function updateCheckboxInBothViews(subjectId, checked, grade) {
    // Update table view
    const tableViewCheckbox = document.querySelector(`#subject_${subjectId}`);
    const tableViewGradeSelect = document.querySelector(`.grade-select[data-subject-id="${subjectId}"]`);
    const tableViewUndoBtn = document.querySelector(`.undo-btn[data-subject-id="${subjectId}"]`);
    const tableViewRow = tableViewCheckbox?.closest('.subject-row');
    
    if (tableViewCheckbox && tableViewRow) {
        tableViewCheckbox.checked = checked;
        
        if (checked) {
            tableViewRow.classList.add('selected');
            if (tableViewGradeSelect) {
                tableViewGradeSelect.disabled = false;
                tableViewGradeSelect.value = grade;
            }
            if (tableViewUndoBtn) tableViewUndoBtn.style.display = 'block';
        } else {
            tableViewRow.classList.remove('selected');
            if (tableViewGradeSelect) {
                tableViewGradeSelect.disabled = true;
                tableViewGradeSelect.value = '';
            }
            if (tableViewUndoBtn) tableViewUndoBtn.style.display = 'none';
        }
    }
    
    // Update dropdown view
    const dropdownViewCheckbox = document.querySelector(`#subject_dropdown_${subjectId}`);
    const dropdownViewGradeSelect = document.querySelector(`.grade-select-dropdown[data-subject-id="${subjectId}"]`);
    const dropdownViewUndoBtn = document.querySelector(`.undo-btn-dropdown[data-subject-id="${subjectId}"]`);
    const dropdownViewCard = dropdownViewCheckbox?.closest('.subject-card');
    const dropdownViewGradeDiv = dropdownViewCard?.querySelector('.grade-selection');
    
    if (dropdownViewCheckbox && dropdownViewCard) {
        dropdownViewCheckbox.checked = checked;
        
        if (checked) {
            dropdownViewCard.classList.add('selected');
            if (dropdownViewGradeDiv) dropdownViewGradeDiv.style.display = 'block';
            if (dropdownViewGradeSelect) dropdownViewGradeSelect.value = grade;
            if (dropdownViewUndoBtn) dropdownViewUndoBtn.style.display = 'block';
        } else {
            dropdownViewCard.classList.remove('selected');
            if (dropdownViewGradeDiv) dropdownViewGradeDiv.style.display = 'none';
            if (dropdownViewGradeSelect) dropdownViewGradeSelect.value = '';
            if (dropdownViewUndoBtn) dropdownViewUndoBtn.style.display = 'none';
        }
    }
}

// Toggle year/semester sections
function initSectionToggles() {
    // Year level toggle
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-year')) {
            const btn = e.target.closest('.toggle-year');
            const year = btn.dataset.year;
            const yearSection = btn.closest('.year-level-section');
            
            yearSection.classList.toggle('collapsed');
            btn.classList.toggle('collapsed');
        }
        
        // Semester toggle
        if (e.target.closest('.toggle-semester')) {
            const btn = e.target.closest('.toggle-semester');
            const semester = btn.dataset.semester;
            const semesterSection = btn.closest('.semester-section');
            
            semesterSection.classList.toggle('collapsed');
            btn.classList.toggle('collapsed');
        }
    });
    
    // Expand all button
    const expandAllCheckbox = document.getElementById('expandAll');
    if (expandAllCheckbox) {
        expandAllCheckbox.addEventListener('change', function() {
            const yearSections = document.querySelectorAll('.year-level-section');
            const semesterSections = document.querySelectorAll('.semester-section');
            
            if (this.checked) {
                yearSections.forEach(section => section.classList.remove('collapsed'));
                semesterSections.forEach(section => section.classList.remove('collapsed'));
                document.querySelectorAll('.toggle-year, .toggle-semester').forEach(btn => {
                    btn.classList.remove('collapsed');
                });
            } else {
                yearSections.forEach(section => section.classList.add('collapsed'));
                semesterSections.forEach(section => section.classList.add('collapsed'));
                document.querySelectorAll('.toggle-year, .toggle-semester').forEach(btn => {
                    btn.classList.add('collapsed');
                });
            }
        });
    }
    
    // Select all visible button
    const selectAllVisibleBtn = document.getElementById('selectAllVisible');
    if (selectAllVisibleBtn) {
        selectAllVisibleBtn.addEventListener('click', function() {
            const visibleSubjects = document.querySelectorAll('.subject-row:not(.hidden)');
            let addedCount = 0;
            
            visibleSubjects.forEach(row => {
                const subjectId = row.dataset.subjectId;
                const checkbox = row.querySelector('.subject-checkbox');
                
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change'));
                    addedCount++;
                }
            });
            
            if (addedCount > 0) {
                showToast(`Added ${addedCount} subjects`);
            }
        });
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        `;
        document.body.appendChild(toastContainer);
    }
    
    // Create toast
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `alert alert-${type} alert-dismissible fade show`;
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            document.getElementById(toastId).remove();
        }
    }, 3000);
}

        //------ Prevent modal from being closed accidentally -------
        function preventModalClosing() {
            const modal = document.getElementById('enrollmentModal');
            
            // Remove any existing event listeners that might close the modal
            if (modal) {
                // Prevent Bootstrap from adding close functionality
                modal.setAttribute('data-bs-backdrop', 'static');
                modal.setAttribute('data-bs-keyboard', 'false');
                
                // Remove any close buttons that might exist
                document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                    btn.style.display = 'none';
                });
                
                // Prevent closing by ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.classList.contains('show')) {
                        e.preventDefault();
                        e.stopPropagation();
                        showWarningMessage();
                        return false;
                    }
                });
                
                // Prevent clicking outside to close
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        e.preventDefault();
                        e.stopPropagation();
                        showWarningMessage();
                        return false;
                    }
                });
            }
        }

        // Show warning message if user tries to close
        function showWarningMessage() {
            // Create a warning alert
            const warningAlert = document.createElement('div');
            warningAlert.className = 'alert alert-warning alert-dismissible fade show position-fixed';
            warningAlert.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            `;
            warningAlert.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Attention:</strong> You must complete subject enrollment before proceeding.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(warningAlert);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (warningAlert.parentNode) {
                    warningAlert.remove();
                }
            }, 5000);
        }
        // -----------------

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
                
                // Initialize only for table view (default)
                initSearch();
                initFilters();
                initSectionToggles();
                initCardViewHandlers(); // For card view
                initQuickAdd();
                handleSubjectSelection(); // For table view
                handleGradeSelection(); // For table view
                handleUndoButton(); // For table view
                handleSubmission();
            }

            // Initialize card view handlers
            function initCardViewHandlers() {
                // Card view checkbox selection
                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('subject-checkbox-card')) {
                        const subjectId = e.target.dataset.subjectId;
                        const card = e.target.closest('.subject-card');
                        const gradeSelect = card?.querySelector('.grade-select-card');
                        const undoBtn = card?.querySelector('.undo-btn-card');
                        
                        if (e.target.checked) {
                            // Show grade selection
                            card.classList.add('selected');
                            if (gradeSelect) gradeSelect.style.display = 'block';
                            if (undoBtn) undoBtn.style.display = 'block';
                            
                            // Auto-select default grade
                            if (gradeSelect && gradeSelect.value === '') {
                                gradeSelect.value = '3.0';
                                updateSelectedSubject(subjectId, gradeSelect.value);
                            } else if (gradeSelect && gradeSelect.value) {
                                updateSelectedSubject(subjectId, gradeSelect.value);
                            }
                        } else {
                            // Hide grade selection and remove subject
                            card.classList.remove('selected');
                            if (gradeSelect) {
                                gradeSelect.style.display = 'none';
                                gradeSelect.value = '';
                            }
                            if (undoBtn) undoBtn.style.display = 'none';
                            removeSelectedSubject(subjectId);
                        }
                        
                        updateSummary();
                    }
                    
                    // Card view grade selection
                    if (e.target.classList.contains('grade-select-card') && e.target.value) {
                        const subjectId = e.target.dataset.subjectId;
                        const checkbox = document.querySelector(`#subject_card_${subjectId}`);
                        
                        if (checkbox && checkbox.checked) {
                            updateSelectedSubject(subjectId, e.target.value);
                            updateSummary();
                        }
                    }
                });
                
                // Card view undo button
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.undo-btn-card')) {
                        const subjectId = e.target.closest('.undo-btn-card').dataset.subjectId;
                        const checkbox = document.querySelector(`#subject_card_${subjectId}`);
                        
                        if (checkbox) {
                            checkbox.checked = false;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    }
                });
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
                                ✅ Successfully selected ${result.enrollment_count} subjects!

                                • Final GWA: ${result.gwa}

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
                    setTimeout(showEnrollmentModal, 1500);
                    
                    // Initialize view toggle
                    initViewToggle();
                }
            }
            
            // Public methods
            return {
                init: init,
                getSelectedSubjects: () => selectedSubjects,
                calculateGWA: calculateGWA
            };
        }


        // CourseManager Class
class CourseManager {
    constructor(studentData = {}) {
        this.currentAction = 'add';
        this.selectedSubjects = new Map();
        this.enrolledSubjects = studentData.enrolledSubjects || [];
        this.csrfToken = studentData.csrfToken || '';
        this.baseUrl = studentData.baseUrl || '';
        this.init();
    }

    init() {
        this.initEventListeners();
        this.loadEnrolledCourses();
    }

    initEventListeners() {
        // Add/Drop Course Button
        $(document).on('click', '#addDropCourseBtn', () => this.openAddDropModal());
        
        // Enroll Now Button (when no courses)
        $(document).on('click', '#enrollNowBtn', () => $('#enrollmentModal').modal('show'));
        
        // Edit Grade Buttons
        $(document).on('click', '.btn-edit-grade', (e) => {
            const subjectId = $(e.currentTarget).data('subject-id');
            this.toggleGradeEditForm(subjectId, true);
        });
        
        $(document).on('click', '.cancel-edit', (e) => {
            const subjectId = $(e.currentTarget).data('subject-id');
            this.toggleGradeEditForm(subjectId, false);
        });
        
        // Save Grade
        $(document).on('click', '.save-grade', (e) => {
            const subjectId = $(e.currentTarget).data('subject-id');
            this.saveGrade(subjectId);
        });
        
        // Add/Drop Modal Tab Changes
        $(document).on('shown.bs.tab', '#addDropTabs button', (e) => {
            const target = $(e.target).data('bs-target');
            this.switchTab(target);
        });
        
        // Submit Add/Drop Changes
        $(document).on('click', '#submitAddDrop', () => this.submitAddDropChanges());
    }

    openAddDropModal() {
        // Reset selections
        this.selectedSubjects.clear();
        this.updateSelectionSummary();
        
        // Load current enrolled courses for drop and update tabs
        this.loadEnrolledCoursesForModal();
        
        // Load available courses for add tab
        this.loadAvailableCourses();
        
        // Show modal
        $('#addDropModal').modal('show');
    }

    switchTab(tabId) {
        switch(tabId) {
            case '#add-tab-pane':
                this.currentAction = 'add';
                break;
            case '#drop-tab-pane':
                this.currentAction = 'drop';
                break;
            case '#update-tab-pane':
                this.currentAction = 'update';
                break;
        }
        $('#addDropAction').text(this.currentAction.charAt(0).toUpperCase() + this.currentAction.slice(1));
    }

    loadEnrolledCourses() {
        // This method is called during initialization
        // The data is already loaded from PHP via the hidden div
    }

    loadEnrolledCoursesForModal() {
        const dropContent = $('#enrolledCoursesList');
        const updateContent = $('#gradesUpdateList');
        
        dropContent.empty();
        updateContent.empty();
        
        if (this.enrolledSubjects.length === 0) {
            dropContent.html('<div class="alert alert-info">No enrolled courses to drop.</div>');
            updateContent.html('<div class="alert alert-info">No enrolled courses to update.</div>');
            return;
        }
        
        // Populate drop courses
        let dropHtml = '<div class="table-responsive"><table class="table table-hover"><thead><tr>' +
            '<th width="50">Select</th><th>Code</th><th>Subject</th><th>Units</th><th>Current Grade</th>' +
            '</tr></thead><tbody>';
        
        // Populate update grades
        let updateHtml = '<div class="table-responsive"><table class="table table-hover"><thead><tr>' +
            '<th>Code</th><th>Subject</th><th>Units</th><th>Current Grade</th><th>New Grade</th><th>Action</th>' +
            '</tr></thead><tbody>';
        
        this.enrolledSubjects.forEach(subject => {
            // Drop courses row
            dropHtml += `<tr>
                <td><input type="checkbox" class="form-check-input drop-checkbox" 
                    data-subject-id="${subject.subject_id}"
                    data-code="${subject.subject_code}"
                    data-name="${subject.subject_name}"
                    data-units="${subject.units}"></td>
                <td>${subject.subject_code}</td>
                <td>${subject.subject_name}</td>
                <td>${subject.units}</td>
                <td>${subject.grade || 'No Grade'}</td>
            </tr>`;
            
            // Update grades row
            updateHtml += `<tr>
                <td>${subject.subject_code}</td>
                <td>${subject.subject_name}</td>
                <td>${subject.units}</td>
                <td>${subject.grade || 'No Grade'}</td>
                <td>
                    <select class="form-select form-select-sm update-grade-select" 
                        data-subject-id="${subject.subject_id}">
                        <option value="">-- Select --</option>
                        ${this.generateGradeOptions(subject.grade)}
                    </select>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary update-grade-btn" 
                        data-subject-id="${subject.subject_id}">
                        <i class="fas fa-save"></i> Update
                    </button>
                </td>
            </tr>`;
        });
        
        dropHtml += '</tbody></table></div>';
        updateHtml += '</tbody></table></div>';
        
        dropContent.html(dropHtml);
        updateContent.html(updateHtml);
        
        // Add event listeners for drop checkboxes
        $(document).off('change', '.drop-checkbox').on('change', '.drop-checkbox', (e) => this.handleDropSelection(e));
        
        // Add event listeners for update buttons
        $(document).off('click', '.update-grade-btn').on('click', '.update-grade-btn', (e) => this.handleGradeUpdateInModal(e));
    }

    loadAvailableCourses() {
        const content = $('#addCoursesContent');
        
        // Show loading
        content.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading available courses...</p></div>');
        
        // Fetch available courses via AJAX
        $.ajax({
            url: '/student/available-subjects',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            },
            success: (response) => {
                this.renderAvailableCourses(response.availableSubjects);
            },
            error: (xhr) => {
                content.html('<div class="alert alert-danger">Failed to load available courses. Please try again.</div>');
            }
        });
    }


    renderAvailableCourses(availableSubjects) {
        let html = '';
        
        if (!availableSubjects || Object.keys(availableSubjects).length === 0) {
            html = '<div class="alert alert-info">No available courses to add at this time.</div>';
        } else {
            // Search bar
            html = '<div class="mb-3">' +
                    '<input type="text" class="form-control" id="courseSearch" placeholder="Search by course code or name...">' +
                    '<small class="text-muted">Type to filter courses</small>' +
                '</div>';
            
            // Control buttons
            html += '<div class="mb-3 d-flex gap-2">' +
                    '<button class="btn btn-sm btn-outline-secondary" id="expandAllYears">' +
                        '<i class="fas fa-expand"></i> Expand All' +
                    '</button>' +
                    '<button class="btn btn-sm btn-outline-secondary" id="collapseAllYears">' +
                        '<i class="fas fa-compress"></i> Collapse All' +
                    '</button>' +
                    '</div>';
            
            // Year Level Accordion
            html += '<div class="accordion" id="yearLevelAccordion">';
            
            let yearIndex = 1;
            for (const [yearLevel, semesters] of Object.entries(availableSubjects)) {
                const yearId = `year-${yearIndex}`;
                const yearCollapseId = `collapse-${yearIndex}`;
                
                html += `<div class="accordion-item">
                    <h2 class="accordion-header" id="${yearId}">
                        <button class="accordion-button ${yearIndex === 1 ? '' : 'collapsed'}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#${yearCollapseId}" 
                                aria-expanded="${yearIndex === 1 ? 'true' : 'false'}" 
                                aria-controls="${yearCollapseId}">
                            <i class="fas fa-graduation-cap me-2"></i>
                            <strong>${yearLevel}</strong>
                            <span class="badge bg-secondary ms-2">${Object.keys(semesters).length} semesters</span>
                        </button>
                    </h2>
                    <div id="${yearCollapseId}" class="accordion-collapse collapse ${yearIndex === 1 ? 'show' : ''}" 
                        aria-labelledby="${yearId}" data-bs-parent="#yearLevelAccordion">
                        <div class="accordion-body p-3">`;
                
                let semesterIndex = 1;
                for (const [semester, subjects] of Object.entries(semesters)) {
                    const semesterId = `semester-${yearIndex}-${semesterIndex}`;
                    const semesterCollapseId = `collapse-semester-${yearIndex}-${semesterIndex}`;
                    
                    html += `<div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none p-0" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#${semesterCollapseId}"
                                        aria-expanded="true" aria-controls="${semesterCollapseId}">
                                    <i class="fas fa-chevron-down me-2"></i>
                                    ${semester}
                                    <span class="badge bg-info ms-2">${subjects.length} subjects</span>
                                </button>
                            </h6>
                        </div>
                        <div id="${semesterCollapseId}" class="collapse show">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 semester-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">Add</th>
                                                <th>Code</th>
                                                <th>Subject Name</th>
                                                <th width="80">Units</th>
                                                <th width="150">Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                    
                    subjects.forEach(subject => {
                        html += `<tr class="subject-row" data-code="${subject.code.toLowerCase()}" 
                                            data-name="${subject.name.toLowerCase()}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input add-checkbox" 
                                    data-subject-id="${subject.id}"
                                    data-code="${subject.code}"
                                    data-name="${subject.name}"
                                    data-units="${subject.units}"
                                    style="cursor: pointer;">
                            </td>
                            <td class="subject-code">
                                <span class="badge bg-primary">${subject.code}</span>
                            </td>
                            <td class="subject-name">${subject.name}</td>
                            <td class="text-center">${subject.units}</td>
                            <td>
                                <select class="form-select form-select-sm add-grade-select" 
                                    data-subject-id="${subject.id}" disabled
                                    style="min-width: 100px;">
                                    <option value="">-- Select --</option>
                                    ${this.generateGradeOptions()}
                                </select>
                            </td>
                        </tr>`;
                    });
                    
                    html += `          </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    
                    semesterIndex++;
                }
                
                html += `    </div>
                    </div>
                </div>`;
                
                yearIndex++;
            }
            
            html += '</div>'; // Close accordion
            
            // Add event listeners after HTML is rendered
            setTimeout(() => {
                this.setupAddCoursesEvents();
            }, 100);
        }
        
        $('#addCoursesContent').html(html);
    }

    handleAddSelection(e) {
        const checkbox = $(e.currentTarget);
        const subjectId = checkbox.data('subject-id');
        const gradeSelect = $(`.add-grade-select[data-subject-id="${subjectId}"]`);
        
        if (checkbox.is(':checked')) {
            // Enable the grade select when checkbox is checked
            gradeSelect.prop('disabled', false);
            
            // Focus on the grade select to prompt user to select a grade
            gradeSelect.focus();
        } else {
            // Unchecked - remove from selection, disable grade select and reset value
            this.selectedSubjects.delete(subjectId);
            gradeSelect.prop('disabled', true).val('');
            
            // Also update the UI if this was previously selected
            const removeBtn = $(`.remove-selection[data-subject-id="${subjectId}"]`);
            if (removeBtn.length) {
                removeBtn.closest('li').remove();
                this.updateSelectionSummary();
            }
        }
        
        // Listen for grade selection change
        gradeSelect.off('change').on('change', () => {
            const grade = gradeSelect.val();
            if (grade) {
                // Add to selected subjects when grade is selected
                this.selectedSubjects.set(subjectId, {
                    action: 'add',
                    subject_id: subjectId,
                    code: checkbox.data('code'),
                    name: checkbox.data('name'),
                    units: checkbox.data('units'),
                    grade: grade
                });
            } else {
                // Remove if grade is cleared
                this.selectedSubjects.delete(subjectId);
            }
            this.updateSelectionSummary();
        });
        
        // If there's already a grade selected, add it immediately
        if (checkbox.is(':checked') && gradeSelect.val()) {
            this.selectedSubjects.set(subjectId, {
                action: 'add',
                subject_id: subjectId,
                code: checkbox.data('code'),
                name: checkbox.data('name'),
                units: checkbox.data('units'),
                grade: gradeSelect.val()
            });
            this.updateSelectionSummary();
        }
    }

    setupAddCoursesEvents() {
        const self = this;
        
        // Search functionality
        $('#courseSearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            
            if (searchTerm === '') {
                // Show all rows and expand all sections
                $('.subject-row').show();
                $('.collapse').collapse('show');
                return;
            }
            
            // Hide all rows first
            $('.subject-row').hide();
            
            // Filter rows that match search
            $('.subject-row').each(function() {
                const code = $(this).data('code');
                const name = $(this).data('name');
                
                if (code.includes(searchTerm) || name.includes(searchTerm)) {
                    $(this).show();
                    
                    // Show parent semester and year
                    const semesterCard = $(this).closest('.card');
                    const semesterCollapse = semesterCard.find('.collapse');
                    const yearCollapse = semesterCard.closest('.accordion-item').find('.accordion-collapse');
                    
                    semesterCollapse.collapse('show');
                    yearCollapse.collapse('show');
                }
            });
            
            // Hide empty semesters
            $('.semester-table').each(function() {
                const visibleRows = $(this).find('.subject-row:visible').length;
                const semesterCard = $(this).closest('.card');
                
                if (visibleRows === 0) {
                    semesterCard.hide();
                } else {
                    semesterCard.show();
                }
            });
            
            // Hide empty years
            $('.accordion-item').each(function() {
                const visibleCards = $(this).find('.card:visible').length;
                
                if (visibleCards === 0) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
        
        // Expand All Years button
        $('#expandAllYears').on('click', function() {
            $('#yearLevelAccordion .accordion-collapse').collapse('show');
            $('.card .collapse').collapse('show');
        });
        
        // Collapse All Years button
        $('#collapseAllYears').on('click', function() {
            $('#yearLevelAccordion .accordion-collapse').collapse('hide');
            $('.card .collapse').collapse('hide');
        });
        
        // Checkbox event listeners
        $('.add-checkbox').on('change', function(e) {
            const checkbox = $(this);
            const subjectId = checkbox.data('subject-id');
            const gradeSelect = $(`.add-grade-select[data-subject-id="${subjectId}"]`);
            
            // Enable/disable grade select based on checkbox
            gradeSelect.prop('disabled', !checkbox.is(':checked'));
            
            if (!checkbox.is(':checked')) {
                // Clear grade and remove from selection if unchecked
                gradeSelect.val('');
                self.selectedSubjects.delete(subjectId);
            } else if (gradeSelect.val()) {
                // If checkbox is checked AND grade is already selected, add to selection
                self.selectedSubjects.set(subjectId, {
                    action: 'add',
                    subject_id: subjectId,
                    code: checkbox.data('code'),
                    name: checkbox.data('name'),
                    units: checkbox.data('units'),
                    grade: gradeSelect.val()
                });
            }
            
            // Update grade change event
            gradeSelect.off('change').on('change', function() {
                const grade = $(this).val();
                if (grade) {
                    self.selectedSubjects.set(subjectId, {
                        action: 'add',
                        subject_id: subjectId,
                        code: checkbox.data('code'),
                        name: checkbox.data('name'),
                        units: checkbox.data('units'),
                        grade: grade
                    });
                } else {
                    self.selectedSubjects.delete(subjectId);
                }
                self.updateSelectionSummary();
            });
            
            self.updateSelectionSummary();
        });
        
        // Grade select event listeners
        $('.add-grade-select').on('change', function() {
            const gradeSelect = $(this);
            const subjectId = gradeSelect.data('subject-id');
            const checkbox = $(`.add-checkbox[data-subject-id="${subjectId}"]`);
            
            if (gradeSelect.val()) {
                // Automatically check the checkbox when grade is selected
                checkbox.prop('checked', true);
                
                self.selectedSubjects.set(subjectId, {
                    action: 'add',
                    subject_id: subjectId,
                    code: checkbox.data('code'),
                    name: checkbox.data('name'),
                    units: checkbox.data('units'),
                    grade: gradeSelect.val()
                });
            } else if (checkbox.is(':checked')) {
                // If grade is cleared but checkbox is checked, remove from selection
                self.selectedSubjects.delete(subjectId);
            }
            
            self.updateSelectionSummary();
        });
    }

    handleDropSelection(e) {
        const checkbox = $(e.currentTarget);
        const subjectId = checkbox.data('subject-id');
        
        if (checkbox.is(':checked')) {
            this.selectedSubjects.set(subjectId, {
                action: 'drop',
                subject_id: subjectId,
                code: checkbox.data('code'),
                name: checkbox.data('name'),
                units: checkbox.data('units')
            });
        } else {
            this.selectedSubjects.delete(subjectId);
        }
        
        this.updateSelectionSummary();
    }

    handleGradeUpdateInModal(e) {
        const button = $(e.currentTarget);
        const subjectId = button.data('subject-id');
        const gradeSelect = $(`.update-grade-select[data-subject-id="${subjectId}"]`);
        const grade = gradeSelect.val();
        
        if (!grade) {
            Swal.fire({
                icon: 'warning',
                title: 'Grade Required',
                text: 'Please select a grade.',
            });
            return;
        }
        
        this.selectedSubjects.set(subjectId, {
            action: 'update',
            subject_id: subjectId,
            grade: grade
        });
        
        button.html('<i class="fas fa-check"></i> Updated');
        button.removeClass('btn-primary').addClass('btn-success');
        button.prop('disabled', true);
        
        this.updateSelectionSummary();
    }

    updateSelectionSummary() {
        const summaryList = $('#selectedAddDropList');
        const totalCount = $('#addDropTotalCount');
        const totalUnits = $('#addDropTotalUnits');
        const submitBtn = $('#submitAddDrop');
        
        if (this.selectedSubjects.size === 0) {
            summaryList.html('<p class="text-muted mb-0">No courses selected for action.</p>');
            totalCount.text('0');
            totalUnits.text('0');
            submitBtn.prop('disabled', true);
            return;
        }
        
        let html = '<ul class="list-group list-group-flush">';
        let totalUnitsCount = 0;
        
        this.selectedSubjects.forEach((subject, subjectId) => {
            const actionBadge = subject.action === 'add' ? 
                '<span class="badge bg-success">Add</span>' : 
                subject.action === 'drop' ? 
                '<span class="badge bg-danger">Drop</span>' : 
                '<span class="badge bg-warning">Update</span>';
            
            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    ${actionBadge}
                    <strong>${subject.code || ''}</strong> - ${subject.name || 'Grade Update'}
                    ${subject.units ? `(${subject.units} units)` : ''}
                    ${subject.grade ? `<span class="text-muted ms-2">Grade: ${subject.grade}</span>` : ''}
                </div>
                <button class="btn btn-sm btn-outline-danger remove-selection" 
                    data-subject-id="${subjectId}">
                    <i class="fas fa-times"></i>
                </button>
            </li>`;
            
            totalUnitsCount += parseFloat(subject.units) || 0;
        });
        
        html += '</ul>';
        
        summaryList.html(html);
        totalCount.text(this.selectedSubjects.size);
        totalUnits.text(totalUnitsCount);
        submitBtn.prop('disabled', false);
        
        // Add remove selection event listeners
        $(document).off('click', '.remove-selection').on('click', '.remove-selection', (e) => this.removeSelection(e));
    }

    removeSelection(e) {
        const button = $(e.currentTarget);
        const subjectId = button.data('subject-id');
        
        this.selectedSubjects.delete(subjectId);
        
        // Reset UI elements
        $(`.add-checkbox[data-subject-id="${subjectId}"]`).prop('checked', false);
        $(`.add-grade-select[data-subject-id="${subjectId}"]`).prop('disabled', false).val('');
        
        $(`.drop-checkbox[data-subject-id="${subjectId}"]`).prop('checked', false);
        
        $(`.update-grade-btn[data-subject-id="${subjectId}"]`)
            .html('<i class="fas fa-save"></i> Update')
            .removeClass('btn-success').addClass('btn-primary')
            .prop('disabled', false);
        
        this.updateSelectionSummary();
    }

    submitAddDropChanges() {
        if (this.selectedSubjects.size === 0) {
            return;
        }
        
        const changes = Array.from(this.selectedSubjects.values());
        
        Swal.fire({
            title: 'Confirm Changes',
            html: `You are about to submit ${changes.length} change(s).<br>
                This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit changes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.processAddDropChanges(changes);
            }
        });
    }


    processAddDropChanges(changes) {
        $.ajax({
            url: this.baseUrl + '/student/submit-add-drop',
            type: 'POST',
            data: {
                _token: this.csrfToken,
                changes: changes
            },
            success: (response) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Your changes have been submitted successfully.',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    $('#addDropModal').modal('hide');
                    location.reload();
                });
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to submit changes.',
                });
            }
        });
    }

    toggleGradeEditForm(subjectId, show) {
        $(`#gradeForm${subjectId}`).toggle(show);
        $(`.btn-edit-grade[data-subject-id="${subjectId}"]`).toggle(!show);
    }

    saveGrade(subjectId) {
        const grade = $(`#gradeSelect${subjectId}`).val();
        
        if (!grade) {
            Swal.fire({
                icon: 'warning',
                title: 'Grade Required',
                text: 'Please select a grade.',
            });
            return;
        }
        
        $.ajax({
            url: this.baseUrl + '/student/update-grade',
            type: 'POST',
            data: {
                _token: this.csrfToken,
                subject_id: subjectId,
                grade: grade
            },
            success: (response) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Grade updated successfully.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Update the displayed grade
                    const gradeElement = $(`.btn-edit-grade[data-subject-id="${subjectId}"]`)
                        .closest('.grade-actions')
                        .find('.grade-value');
                    gradeElement.text(grade);
                    gradeElement.removeClass('grade-pass grade-fail')
                        .addClass(grade >= 3.0 ? 'grade-fail' : 'grade-pass');
                    
                    this.toggleGradeEditForm(subjectId, false);
                    
                    // Update the local data
                    const subjectIndex = this.enrolledSubjects.findIndex(s => s.subject_id == subjectId);
                    if (subjectIndex !== -1) {
                        this.enrolledSubjects[subjectIndex].grade = grade;
                    }
                });
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to update grade.',
                });
            }
        });
    }

    generateGradeOptions(currentGrade = '') {
        let options = '';
        
        // Generate grades from 1.0 to 3.0
        for (let i = 1.0; i <= 3.0; i += 0.1) {
            const grade = i.toFixed(1);
            const selected = grade === currentGrade ? 'selected' : '';
            options += `<option value="${grade}" ${selected}>${grade}</option>`;
        }
        
        // Add special grades
        const specialGrades = ['4.0', '5.0', 'INC', 'DRP', 'PASS', 'FAIL'];
        specialGrades.forEach(grade => {
            const selected = grade === currentGrade ? 'selected' : '';
            options += `<option value="${grade}" ${selected}>${grade}</option>`;
        });
        
        return options;
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

            const enrollment = handleEnrollment();
            enrollment.init();
            
            // Notification button click
            document.querySelector('.notification-btn').addEventListener('click', function() {
                alert('You have 3 new notifications:\n- New grade posted for Web Development\n- Assignment due tomorrow\n- Campus event registration reminder');
            });

            // Initialize CourseManager with data from STUDENT_DATA global variable
            if (typeof window.STUDENT_DATA !== 'undefined') {
                window.courseManager = new CourseManager(window.STUDENT_DATA.enrolledSubjects);
            }
            
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
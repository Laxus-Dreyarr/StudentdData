// public/js/function/student/scholastic-warnings.js

function initializeScholasticWarnings() {
    // Check if user is a student
    if (!window.currentUser || window.currentUser.user_type !== 'student') {
        return;
    }

    // Create warning container in dashboard
    const warningContainer = createWarningContainer();
    
    // Load warning data
    loadWarningData();
    
    // Set up periodic checks (every 5 minutes)
    setInterval(loadWarningData, 5 * 60 * 1000);
    
    // Set up event listeners for acknowledgment
    setupWarningEventListeners();
}

function createWarningContainer() {
    const dashboardMain = document.querySelector('.dashboard-main') || document.querySelector('main');
    if (!dashboardMain) return null;
    
    const container = document.createElement('div');
    container.id = 'scholastic-warnings-container';
    container.className = 'scholastic-warnings';
    container.innerHTML = `
        <div class="warnings-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Academic Warnings & Notices</h3>
            <button id="refresh-warnings" class="btn-refresh" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div id="warnings-content">
            <div class="loading-warnings">
                <i class="fas fa-spinner fa-spin"></i> Loading academic status...
            </div>
        </div>
    `;
    
    dashboardMain.insertBefore(container, dashboardMain.firstChild);
    return container;
}

function loadWarningData() {
    const contentDiv = document.getElementById('warnings-content');
    if (!contentDiv) return;
    
    contentDiv.innerHTML = `
        <div class="loading-warnings">
            <i class="fas fa-spinner fa-spin"></i> Checking academic status...
        </div>
    `;
    
    fetch('/student/academic-status', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        displayWarningData(data);
        updateBadgeCount(data);
    })
    .catch(error => {
        console.error('Error loading warning data:', error);
        contentDiv.innerHTML = `
            <div class="error-warning">
                <i class="fas fa-exclamation-circle"></i>
                <p>Unable to load academic status. Please try again later.</p>
            </div>
        `;
    });
}

function displayWarningData(data) {
    const contentDiv = document.getElementById('warnings-content');
    if (!contentDiv) return;
    
    let html = '';
    
    // Active warnings
    if (data.active_warnings && data.active_warnings.length > 0) {
        html += `
            <div class="warnings-section active-warnings">
                <h4><i class="fas fa-exclamation-circle text-danger"></i> Active Warnings</h4>
                ${data.active_warnings.map(warning => `
                    <div class="warning-item warning-${warning.severity || 'medium'}">
                        <div class="warning-header">
                            <span class="warning-type">${warning.warning_type}</span>
                            <span class="warning-date">Issued: ${warning.issued_date}</span>
                        </div>
                        <div class="warning-reason">${warning.reason}</div>
                        ${warning.expiry_date ? `
                            <div class="warning-expiry">
                                <i class="far fa-clock"></i> Valid until: ${warning.expiry_date}
                            </div>
                        ` : ''}
                        ${warning.actions ? `
                            <div class="warning-actions">
                                ${warning.actions.map(action => `
                                    <button class="btn-action btn-${action.type}" 
                                            data-warning-id="${warning.id}" 
                                            data-action="${action.type}">
                                        ${action.label}
                                    </button>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `).join('')}
            </div>
        `;
    } else {
        html += `
            <div class="no-warnings">
                <i class="fas fa-check-circle text-success"></i>
                <p>No active academic warnings.</p>
            </div>
        `;
    }
    
    // Probation status
    if (data.probation_status && data.probation_status.status === 'Active') {
        const probation = data.probation_status;
        html += `
            <div class="warnings-section probation-warning">
                <h4><i class="fas fa-user-graduate text-warning"></i> Probation Status</h4>
                <div class="probation-details">
                    <p><strong>Status:</strong> <span class="probation-active">Active</span></p>
                    <p><strong>Started:</strong> ${probation.start_date}</p>
                    ${probation.end_date ? `
                        <p><strong>Ends:</strong> ${probation.end_date}</p>
                    ` : ''}
                    ${probation.credit_limit ? `
                        <p><strong>Credit Limit:</strong> ${probation.credit_limit} units</p>
                    ` : ''}
                    <p><strong>Reason:</strong> ${probation.reason}</p>
                </div>
                <div class="probation-actions">
                    <button class="btn btn-outline-primary" id="view-probation-terms">
                        <i class="fas fa-file-contract"></i> View Terms
                    </button>
                    <button class="btn btn-outline-success" id="request-probation-review">
                        <i class="fas fa-handshake"></i> Request Review
                    </button>
                </div>
            </div>
        `;
    }
    
    // Incomplete grades
    if (data.incomplete_grades && data.incomplete_grades.length > 0) {
        html += `
            <div class="warnings-section incomplete-grades">
                <h4><i class="fas fa-hourglass-half text-info"></i> Incomplete Grades</h4>
                ${data.incomplete_grades.map(inc => `
                    <div class="incomplete-item">
                        <div class="incomplete-header">
                            <span class="subject-code">${inc.subject_code}</span>
                            <span class="incomplete-status ${inc.status.toLowerCase()}">${inc.status}</span>
                        </div>
                        <div class="incomplete-details">
                            <p><strong>Issued:</strong> ${inc.date_issued}</p>
                            <p><strong>Deadline:</strong> ${inc.completion_deadline}</p>
                            ${inc.days_remaining ? `
                                <p><strong>Days Remaining:</strong> 
                                    <span class="days-remaining ${inc.days_remaining < 30 ? 'text-danger' : 'text-warning'}">
                                        ${inc.days_remaining} days
                                    </span>
                                </p>
                            ` : ''}
                        </div>
                        <div class="incomplete-actions">
                            <button class="btn btn-sm btn-outline-primary submit-completion" 
                                    data-incomplete-id="${inc.id}">
                                <i class="fas fa-upload"></i> Submit Requirements
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    // Academic summary
    if (data.academic_summary) {
        const summary = data.academic_summary;
        html += `
            <div class="warnings-section academic-summary">
                <h4><i class="fas fa-chart-line text-primary"></i> Academic Summary</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value ${summary.failed_subjects > 0 ? 'text-danger' : 'text-success'}">
                            ${summary.failed_subjects}
                        </div>
                        <div class="stat-label">Failed Subjects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value ${summary.incomplete_grades > 0 ? 'text-warning' : 'text-success'}">
                            ${summary.incomplete_grades}
                        </div>
                        <div class="stat-label">Incomplete Grades</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value ${summary.average_grade > 2.5 ? 'text-danger' : 'text-success'}">
                            ${summary.average_grade.toFixed(2)}
                        </div>
                        <div class="stat-label">Average Grade</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value ${summary.warning_count > 0 ? 'text-danger' : 'text-success'}">
                            ${summary.warning_count}
                        </div>
                        <div class="stat-label">Active Warnings</div>
                    </div>
                </div>
            </div>
        `;
    }
    
    contentDiv.innerHTML = html;
}

function updateBadgeCount(data) {
    // Update badge in navigation if exists
    const warningCount = data.active_warnings?.length || 0;
    const badgeElement = document.querySelector('.nav-warning-badge') || createBadgeElement();
    
    if (badgeElement) {
        badgeElement.textContent = warningCount;
        badgeElement.style.display = warningCount > 0 ? 'inline-block' : 'none';
    }
}

function createBadgeElement() {
    const navLinks = document.querySelectorAll('.nav-link');
    const dashboardLink = Array.from(navLinks).find(link => 
        link.textContent.includes('Dashboard') || link.href.includes('dashboard')
    );
    
    if (dashboardLink) {
        const badge = document.createElement('span');
        badge.className = 'badge bg-danger nav-warning-badge';
        badge.style.display = 'none';
        dashboardLink.appendChild(badge);
        return badge;
    }
    return null;
}

function setupWarningEventListeners() {
    // Refresh button
    document.addEventListener('click', function(e) {
        if (e.target.closest('#refresh-warnings')) {
            loadWarningData();
        }
        
        // Acknowledge warning
        if (e.target.closest('.btn-acknowledge')) {
            const button = e.target.closest('.btn-acknowledge');
            const warningId = button.dataset.warningId;
            acknowledgeWarning(warningId);
        }
        
        // View probation terms
        if (e.target.closest('#view-probation-terms')) {
            showProbationTerms();
        }
        
        // Submit completion for incomplete grade
        if (e.target.closest('.submit-completion')) {
            const button = e.target.closest('.submit-completion');
            const incompleteId = button.dataset.incompleteId;
            showCompletionModal(incompleteId);
        }
    });
}

function acknowledgeWarning(warningId) {
    fetch(`/student/warnings/${warningId}/acknowledge`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Warning acknowledged successfully', 'success');
            loadWarningData(); // Refresh data
        }
    })
    .catch(error => {
        console.error('Error acknowledging warning:', error);
        showNotification('Failed to acknowledge warning', 'error');
    });
}

function showProbationTerms() {
    const modalHtml = `
        <div class="modal fade" id="probationTermsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-contract"></i> Probation Terms & Conditions
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="terms-content">
                            <h6>Based on Section 67.1 of University Handbook</h6>
                            <ol>
                                <li>Your credit load for the succeeding semester is limited</li>
                                <li>You must pass all subjects in the next semester to have probation lifted</li>
                                <li>You are required to meet with the guidance counselor</li>
                                <li>You must write a letter of recommitment</li>
                                <li>Additional academic monitoring will be implemented</li>
                                <li>Failure to comply may result in dismissal from the program</li>
                            </ol>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> These terms are based on the 2017 Revised University Students' Handbook, Section 67.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="acceptProbationTerms">
                            <i class="fas fa-check"></i> I Understand
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to body and show it
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('probationTermsModal'));
    modal.show();
    
    // Clean up on close
    document.getElementById('probationTermsModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function showNotification(message, type = 'info') {
    // Create toast notification
    const toastHtml = `
        <div class="toast align-items-center text-bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Remove toast after hide
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}

function checkImmediateWarningsAfterLogin() {
    // This should be called after successful login
    fetch('/student/check-delinquency', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.warnings_issued && data.warnings_issued.length > 0) {
            // Show immediate notification
            showNotification(`You have ${data.warnings_issued.length} new academic warning(s)`, 'warning');
        }
    })
    .catch(error => {
        console.error('Error checking immediate warnings:', error);
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initializeScholasticWarnings();
});
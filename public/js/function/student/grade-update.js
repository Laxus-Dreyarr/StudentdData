document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag (add <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // ========== SHOW EDIT FORM ==========
    document.querySelectorAll('.btn-update-grade').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const enrolledId = this.dataset.enrolledId;
            const row = this.closest('tr');
            const gradeCell = row.querySelector('.course-grade');
            const gradeDisplay = gradeCell.querySelector('.grade-display');
            const editForm = gradeCell.querySelector('.grade-edit-form');

            // Hide grade display, show edit form
            gradeDisplay.style.display = 'none';
            editForm.style.display = 'block';
        });
    });

    // ========== CANCEL EDIT ==========
    document.querySelectorAll('.btn-cancel-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const enrolledId = this.dataset.enrolledId;
            const row = this.closest('tr');
            const gradeCell = row.querySelector('.course-grade');
            const gradeDisplay = gradeCell.querySelector('.grade-display');
            const editForm = gradeCell.querySelector('.grade-edit-form');

            // Show grade display, hide edit form
            gradeDisplay.style.display = 'inline';  // or 'block' depending on your CSS
            editForm.style.display = 'none';
        });
    });

    // ========== SAVE GRADE (AJAX) ==========
    document.querySelectorAll('.btn-save-grade').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const enrolledId = this.dataset.enrolledId;
            const row = this.closest('tr');
            const gradeCell = row.querySelector('.course-grade');
            const select = gradeCell.querySelector('.grade-select-edit');
            const newGrade = select.value;

            if (!newGrade) {
                alert('Please select a grade.');
                return;
            }

            // Disable button to prevent double submission
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            // Send PUT request to update grade
            fetch(`/student/grade/${enrolledId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ grade: newGrade })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                // Update the grade display span
                const gradeDisplay = gradeCell.querySelector('.grade-display');
                gradeDisplay.textContent = newGrade;

                // Update pass/fail class
                gradeDisplay.classList.remove('grade-pass', 'grade-fail');
                if (isNumericGrade(newGrade) && parseFloat(newGrade) >= 3.0) {
                    gradeDisplay.classList.add('grade-fail');
                } else {
                    gradeDisplay.classList.add('grade-pass');
                }

                // Hide edit form, show grade display
                gradeDisplay.style.display = 'inline';
                gradeCell.querySelector('.grade-edit-form').style.display = 'none';

                // Show success message (optional)
                showToast('Grade updated successfully!', 'success');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to update grade. Please try again.');
            })
            .finally(() => {
                // Restore button
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });

    // Helper: check if a grade is numeric (e.g., "1.5", "4.0")
    function isNumericGrade(grade) {
        return !isNaN(parseFloat(grade)) && isFinite(grade);
    }

    // Optional toast notification (implement your own or use a library)
    function showToast(message, type = 'info') {
        // Simple alert for demo – replace with your preferred UI
        alert(message);
    }
});

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

    // Preload the background image for better performance
    const registerBgImage = new Image();
    registerBgImage.src = 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80';

    // Get the left panel element
    const leftPanel = document.querySelector('.left-panel');

    // Tab Switching
    // In the tab button click event handler:
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

            // Toggle register mode background
            if (target === 'registerForm') {
                leftPanel.classList.add('register-mode');
            } else {
                leftPanel.classList.remove('register-mode');
            }
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
        const passwordInput2 = document.getElementById('confirmPassword');
        const icon = this.querySelector('i');
        togglePasswordVisibility(passwordInput, icon);
        togglePasswordVisibility(passwordInput2, icon);
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
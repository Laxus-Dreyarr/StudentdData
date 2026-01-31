class AuthHandler {
    constructor() {
        this.loginForm = document.getElementById('loginFormElement');
        this.loginButton = document.getElementById('loginButton');
        this.loginPasswordToggle = document.getElementById('loginPasswordToggle');
        
        if (this.loginForm) {
            this.initLoginForm();
        }
        
        if (this.loginPasswordToggle) {
            this.initPasswordToggle();
        }
    }

    initLoginForm() {
        this.loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.getElementById('rememberMe').checked;
            
            await this.handleLogin(email, password, remember);
        });
    }

    initPasswordToggle() {
        this.loginPasswordToggle.addEventListener('click', () => {
            const passwordInput = document.getElementById('loginPassword');
            const icon = this.loginPasswordToggle.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    async handleLogin(email, password, remember) {
        // Show loading state
        this.setLoading(true);
        
        try {
            const response = await fetch('/exe/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success message
                this.showMessage('success', data.message);
                window.location.href = '/student-dashboard';
                
            } else {
                window.location.href = '/';
                // Show error message
                this.showMessage('error', data.message || 'Login failed');
                this.setLoading(false);
            }
        } catch (error) {
            window.location.href = '/';
            console.error('Login error:', error);
            this.showMessage('error', 'Network error. Please try again.');
            this.setLoading(false);
        }
    }

    setLoading(isLoading) {
        if (this.loginButton) {
            const loginButton = document.getElementById('loginButton');
            const spinner = this.loginButton.querySelector('.spinner');
            const buttonText = this.loginButton.querySelector('.btn-text');
            
            if (isLoading) {
                this.loginButton.disabled = true;
                spinner.style.display = 'inline-block';
                buttonText.textContent = 'Signing in...';
            } else {
                this.loginButton.disabled = false;
                spinner.style.display = 'none';
                // buttonText.style.visibility = 'visible'; 
                buttonText.textContent = 'Sign In to Dashboard';
            }
        }
    }

    showMessage(type, message) {
        // Remove any existing messages
        const existingMessages = document.querySelectorAll('.alert-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create new message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert-message alert-${type}`;
        messageDiv.innerHTML = `
            <div class="alert-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="alert-close">&times;</button>
        `;
        
        // Insert before form
        this.loginForm.parentNode.insertBefore(messageDiv, this.loginForm);
        
        // Add close functionality
        messageDiv.querySelector('.alert-close').addEventListener('click', () => {
            messageDiv.remove();
        });
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AuthHandler();
});
<?php
use Illuminate\Support\Facades\Cache;
if (!session('registration_email')) {
    return redirect()->route('/');
}

$email = session('registration_email');
$registerData = $email ? Cache::get('registration_' . $email) : null;

$otp = $registerData['otp'] ?? null;
$password = $registerData['password'];
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration Verification - {{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Figtree', sans-serif;
        }
        
        .verification-container {
            max-width: 500px;
            width: 100%;
            padding: 40px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .verification-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            background-size: 200% 100%;
        }
        
        .verification-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .verification-title {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .verification-subtitle {
            color: #718096;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .student-info {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border: 2px solid #e2e8f0;
        }
        
        .student-info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .student-info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #4a5568;
            font-weight: 600;
        }
        
        .info-value {
            color: #2d3748;
            font-weight: 500;
        }
        
        .code-inputs-container {
            position: relative;
            margin: 30px 0;
        }
        
        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        
        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 3px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            transition: all 0.3s ease;
            color: #2d3748;
        }
        
        .code-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
            outline: none;
            transform: translateY(-2px);
        }
        
        .code-input.filled {
            border-color: #48bb78;
            background-color: #f0fff4;
        }
        
        .code-input.error {
            border-color: #f56565;
            background-color: #fff5f5;
            animation: shake 0.5s;
        }
        
        .code-input.locked {
            border-color: #cbd5e0;
            background-color: #f7fafc;
            color: #a0aec0;
            cursor: not-allowed;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .timer-container {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            position: relative;
        }
        
        .timer-label {
            display: block;
            color: #718096;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        #timer {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        
        .timer-warning {
            color: #ed8936;
        }
        
        .timer-expired {
            color: #f56565;
        }
        
        .timer-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 0 0 10px 10px;
            width: 100%;
            transform-origin: left;
        }
        
        .attempts-container {
            text-align: center;
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border-radius: 10px;
            border: 2px solid #fc8181;
            display: none;
        }
        
        .attempts-warning {
            background: linear-gradient(135deg, #feebc8 0%, #fbd38d 100%);
            border-color: #ed8936;
        }
        
        .attempts-locked {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            border-color: #f56565;
        }
        
        .attempts-text {
            color: #c53030;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .attempts-warning .attempts-text {
            color: #9c4221;
        }
        
        .attempts-locked .attempts-text {
            color: #c53030;
        }
        
        .attempts-count {
            font-weight: bold;
            font-size: 18px;
        }
        
        .btn-verify {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-verify:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-verify.verifying {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }
        
        .btn-verify.locked {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .message-container {
            margin: 20px 0;
        }
        
        .error-message {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #c53030;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            border-left: 5px solid #f56565;
            animation: slideIn 0.3s ease;
        }
        
        .success-message {
            background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
            color: #22543d;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            border-left: 5px solid #48bb78;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .resend-section {
            text-align: center;
            margin-top: 25px;
        }
        
        #resendLink {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 24px;
            background: #ebf8ff;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        #resendLink:hover:not(:disabled) {
            background: #bee3f8;
            transform: translateY(-1px);
        }
        
        #resendLink:disabled {
            color: #a0aec0;
            background: #edf2f7;
            cursor: not-allowed;
            transform: none;
        }
        
        .back-link {
            text-align: center;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        
        .back-link a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            color: #2d3748;
            background: #f7fafc;
            text-decoration: none;
        }
        
        .auto-verify-indicator {
            text-align: center;
            color: #718096;
            font-size: 14px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .auto-verify-indicator::before {
            content: '⚡';
            font-size: 16px;
        }
        
        .verification-success {
            text-align: center;
            padding: 30px;
            display: none;
        }
        
        .success-icon {
            font-size: 64px;
            color: #48bb78;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .redirecting-text {
            color: #718096;
            font-size: 14px;
            margin-top: 15px;
        }
        
        .lockout-container {
            text-align: center;
            padding: 30px;
            display: none;
        }
        
        .lockout-icon {
            font-size: 64px;
            color: #f56565;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .lockout-timer {
            font-size: 24px;
            font-weight: bold;
            color: #f56565;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
            padding: 15px;
            background: #fff5f5;
            border-radius: 10px;
            border: 2px solid #fed7d7;
        }
        
        .email-display {
            background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
            border-radius: 10px;
            padding: 12px 20px;
            margin: 15px 0;
            text-align: center;
            font-weight: 600;
            color: #2c5282;
            border: 2px solid #90cdf4;
        }

        @media (max-width: 480px) {
            .verification-container {
                padding: 30px 20px;
            }
            
            .code-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }
            
            .btn-verify {
                padding: 15px 20px;
                font-size: 16px;
            }
            
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-header">
            <h1 class="verification-title">Student Registration Verification</h1>
            <p class="verification-subtitle">Please enter the 6-digit verification code sent to your email</p>
        </div>
        
        <!-- Student Information Display -->
        @if(isset($registerData))
        <div class="student-info">
            <h3 style="color: #2d3748; margin-bottom: 15px; font-size: 18px;">Registration Details:</h3>
            <div class="student-info-item">
                <span class="info-label">Student ID:</span>
                <span class="info-value">{{ $registerData['studentId'] }}</span>
            </div>
            <div class="student-info-item">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $registerData['firstName'] }} {{ $registerData['middlename'] ?? '' }} {{ $registerData['lastName'] }}</span>
            </div>
            <div class="student-info-item">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $registerData['email'] }}</span>
            </div>
            <div class="student-info-item">
                <span class="info-label">Curriculum:</span>
                <span class="info-value">{{ $registerData['curriculum'] }}</span>
            </div>
        </div>
        @endif
        
        <div class="email-display">
            Code sent to: {{ $email }}
        </div>
        
        <div class="message-container">
            <div class="error-message" id="errorMessage"></div>
            <div class="success-message" id="successMessage"></div>
        </div>
        
        <!-- Attempts warning container -->
        <div class="attempts-container" id="attemptsContainer" style="display: none;">
            <p class="attempts-text">
                <span id="attemptsIcon">⚠️</span>
                <span>Attempts remaining: <span class="attempts-count" id="attemptsCount">3</span>/3</span>
            </p>
        </div>
        
        <!-- Verification Form -->
        <div id="verificationFormContainer">
            <form id="verificationForm" class="verification-form" action="{{ route('verify-student-otp') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div class="code-inputs-container">
                    <div class="code-inputs">
                        <input type="text" class="code-input" maxlength="1" data-index="1" autofocus>
                        <input type="text" class="code-input" maxlength="1" data-index="2">
                        <input type="text" class="code-input" maxlength="1" data-index="3">
                        <input type="text" class="code-input" maxlength="1" data-index="4">
                        <input type="text" class="code-input" maxlength="1" data-index="5">
                        <input type="text" class="code-input" maxlength="1" data-index="6">
                        <input type="hidden" name="otp" id="otpCode">
                    </div>
                    <div class="auto-verify-indicator">Auto-verifies when complete</div>
                </div>
                
                <div class="timer-container">
                    <span class="timer-label">Code expires in</span>
                    <div id="timer">10:00</div>
                    <div class="timer-progress" id="timerProgress"></div>
                </div>
                
                <button type="submit" class="btn-verify" id="verifyBtn">
                    <span>Verify & Complete Registration</span>
                    <div class="loading-spinner" id="loadingSpinner"></div>
                </button>
            </form>
            
            <!-- <div class="resend-section">
                <a href="#" id="resendLink" disabled>
                    <span>Resend Verification Code</span>
                </a>
            </div> -->
        </div>
        
        <!-- Lockout Screen -->
        <div class="lockout-container" id="lockoutContainer">
            <div class="lockout-icon">🔒</div>
            <h2>Too Many Attempts</h2>
            <p>You have exceeded the maximum number of verification attempts.</p>
            <div class="lockout-timer" id="lockoutTimer">15:00</div>
            <p>Please wait before trying again or contact support.</p>
            <!-- <div class="resend-section">
                <a href="#" id="lockoutResendLink">
                    <span>Request New Code</span>
                </a>
            </div> -->
        </div>
        
        <!-- Success Screen -->
        <div class="verification-success" id="verificationSuccess">
            <div class="success-icon">✓</div>
            <h2>Verification Successful!</h2>
            <p>Your student account is being created...</p>
            <div class="redirecting-text" id="redirectCountdown">Redirecting in 3 seconds</div>
        </div>
        
        <div class="back-link">
            <a href="/">&larr; Back to Home</a>
        </div>
    </div>

    <script>
        // Initialize attempt counter from cache data
        const initialAttempts = <?php echo json_encode($registerData['attempts'] ?? 0); ?>;
        const maxAttempts = 3;
        const lockoutDuration = 900; // 15 minutes in seconds
        const otpDuration = 600; // 10 minutes in seconds (matching your cache)

         // Clear previous localStorage entries for this email when page loads
        localStorage.removeItem('student_verification_attempts_' + <?php echo json_encode($email); ?>);
        localStorage.removeItem('student_verification_timer_' + <?php echo json_encode($email); ?>);


        class AttemptManager {
            constructor() {
                this.storageKey = 'student_verification_attempts_' + <?php echo json_encode($email); ?>;
                this.attemptsContainer = document.getElementById('attemptsContainer');
                this.attemptsCountElement = document.getElementById('attemptsCount');
                this.attemptsIcon = document.getElementById('attemptsIcon');
                this.loadAttempts();
            }

            loadAttempts() {
                const data = localStorage.getItem(this.storageKey);
                if (data) {
                    const { attempts, timestamp, lockedUntil } = JSON.parse(data);
                    const now = Math.floor(Date.now() / 1000);
                    
                    // Check if lockout period has passed
                    if (lockedUntil && now < lockedUntil) {
                        this.attempts = maxAttempts;
                        this.lockedUntil = lockedUntil;
                        this.isLockedOut = true;
                        return;
                    }
                    
                    // Check if attempts should reset (more than 15 minutes since last attempt)
                    if (timestamp && now - timestamp > 900) {
                        this.attempts = initialAttempts;
                    } else {
                        this.attempts = attempts;
                    }
                } else {
                    this.attempts = initialAttempts;
                }
                this.isLockedOut = this.attempts >= maxAttempts;
                this.updateDisplay();
            }

            incrementAttempt() {
                this.attempts++;
                this.saveAttempts();
                this.updateDisplay();
                
                if (this.attempts >= maxAttempts) {
                    this.isLockedOut = true;
                    this.startLockout();
                }
                
                return this.attempts;
            }
            

            startLockout() {
                const lockedUntil = Math.floor(Date.now() / 1000) + lockoutDuration;
                this.lockedUntil = lockedUntil;
                this.saveAttempts();
            }

            resetAttempts() {
                this.attempts = initialAttempts;
                this.isLockedOut = false;
                this.lockedUntil = null;
                localStorage.removeItem(this.storageKey);
                this.updateDisplay();
            }

            saveAttempts() {
                const data = {
                    attempts: this.attempts,
                    timestamp: Math.floor(Date.now() / 1000),
                    lockedUntil: this.lockedUntil || null
                };
                localStorage.setItem(this.storageKey, JSON.stringify(data));
            }

            updateDisplay() {
                const remainingAttempts = maxAttempts - this.attempts;
                
                if (this.attempts > 0 && !this.isLockedOut) {
                    this.attemptsContainer.style.display = 'block';
                    this.attemptsCountElement.textContent = remainingAttempts;
                    
                    // Update styling based on attempts
                    if (remainingAttempts === 1) {
                        this.attemptsContainer.className = 'attempts-container attempts-locked';
                        this.attemptsIcon.textContent = '🔴';
                    } else if (remainingAttempts === 2) {
                        this.attemptsContainer.className = 'attempts-container attempts-warning';
                        this.attemptsIcon.textContent = '🟡';
                    } else {
                        this.attemptsContainer.className = 'attempts-container';
                        this.attemptsIcon.textContent = '⚠️';
                    }
                } else {
                    this.attemptsContainer.style.display = 'none';
                }
            }

            isLocked() {
                if (!this.isLockedOut) return false;
                
                if (this.lockedUntil) {
                    const now = Math.floor(Date.now() / 1000);
                    if (now >= this.lockedUntil) {
                        this.resetAttempts();
                        return false;
                    }
                    return true;
                }
                return this.isLockedOut;
            }

            getRemainingLockoutTime() {
                if (!this.lockedUntil) return 0;
                const now = Math.floor(Date.now() / 1000);
                return Math.max(0, this.lockedUntil - now);
            }
        }

        class PersistentTimer {
            constructor() {
                this.storageKey = 'student_verification_timer_{{ $email }}';
                this.timerElement = document.getElementById('timer');
                this.timerProgress = document.getElementById('timerProgress');
                this.verifyBtn = document.getElementById('verifyBtn');
                this.resendLink = document.getElementById('resendLink');
                this.codeInputs = document.querySelectorAll('.code-input');
                this.interval = null;
                this.remainingTime = 0;
                this.initialize();
            }

            initialize() {
                localStorage.removeItem(this.storageKey);
                const savedTime = localStorage.getItem(this.storageKey);
                const now = Math.floor(Date.now() / 1000);
                
                if (savedTime) {
                    const elapsed = now - parseInt(savedTime);
                    this.remainingTime = Math.max(0, otpDuration - elapsed);
                    
                    if (this.remainingTime > 0) {
                        this.startTimer();
                        this.updateInputsState(false);
                    } else {
                        this.onTimerComplete();
                    }
                } else {
                    this.remainingTime = otpDuration;
                    localStorage.setItem(this.storageKey, now.toString());
                    this.startTimer();
                }
            }

            startTimer() {
                this.updateDisplay();
                
                this.interval = setInterval(() => {
                    this.remainingTime--;
                    this.updateDisplay();
                    
                    if (this.remainingTime <= 0) {
                        this.onTimerComplete();
                    }
                    
                    // Auto-save progress every 5 seconds
                    if (this.remainingTime % 5 === 0) {
                        const startTime = Math.floor(Date.now() / 1000) - (otpDuration - this.remainingTime);
                        localStorage.setItem(this.storageKey, startTime.toString());
                    }
                }, 1000);
            }

            updateDisplay() {
                const minutes = Math.floor(this.remainingTime / 60);
                const seconds = this.remainingTime % 60;
                const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                this.timerElement.textContent = timeString;
                
                // Update progress bar
                const progressPercentage = (this.remainingTime / otpDuration) * 100;
                this.timerProgress.style.transform = `scaleX(${progressPercentage / 100})`;
                
                // Visual warnings
                if (this.remainingTime < 60 && this.remainingTime > 30) {
                    this.timerElement.classList.add('timer-warning');
                    this.timerElement.classList.remove('timer-expired');
                } else if (this.remainingTime <= 30) {
                    this.timerElement.classList.remove('timer-warning');
                    this.timerElement.classList.add('timer-expired');
                } else {
                    this.timerElement.classList.remove('timer-warning', 'timer-expired');
                }
            }

            onTimerComplete() {
                clearInterval(this.interval);
                this.timerElement.textContent = '00:00';
                this.timerProgress.style.transform = 'scaleX(0)';
                this.timerElement.classList.add('timer-expired');
                this.verifyBtn.disabled = true;
                this.resendLink.disabled = false;
                this.updateInputsState(true);
                localStorage.removeItem(this.storageKey);
                showError('Verification code has expired. Please request a new one.');
                
                // Clear server cache and session when timer expires
                this.clearCacheOnServer();
            }

            async clearCacheOnServer() {
                try {
                    // Call server to clear cache when timer expires
                    await fetch('/api/clear-lockout-cache', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            email: '<?php echo $email; ?>'
                        })
                    });
                } catch (error) {
                    console.error('Failed to clear cache:', error);
                }
            }

            resetTimer() {
                clearInterval(this.interval);
                localStorage.removeItem(this.storageKey);
                this.remainingTime = otpDuration;
                const now = Math.floor(Date.now() / 1000);
                localStorage.setItem(this.storageKey, now.toString());
                this.verifyBtn.disabled = false;
                this.resendLink.disabled = true;
                this.updateInputsState(false);
                this.timerElement.classList.remove('timer-expired', 'timer-warning');
                this.startTimer();
            }

            updateInputsState(disabled) {
                this.codeInputs.forEach(input => {
                    input.disabled = disabled;
                    if (disabled) {
                        input.classList.add('locked');
                        input.style.cursor = 'not-allowed';
                    } else {
                        input.classList.remove('locked');
                        input.style.cursor = '';
                    }
                });
            }

            isExpired() {
                return this.remainingTime <= 0;
            }
        }

        class LockoutManager {
            constructor() {
                this.lockoutContainer = document.getElementById('lockoutContainer');
                this.lockoutTimer = document.getElementById('lockoutTimer');
                this.verificationFormContainer = document.getElementById('verificationFormContainer');
                this.lockoutInterval = null;
            }

            showLockout(remainingSeconds) {
                this.verificationFormContainer.style.display = 'none';
                this.lockoutContainer.style.display = 'block';
                this.startLockoutTimer(remainingSeconds);
                
                // Make an API call to clear cache on server
                this.clearCacheOnServer();
            }

            async clearCacheOnServer() {
                try {
                    // Call server to clear cache when locked out
                    await fetch('/api/clear-lockout-cache', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            email: '<?php echo $email; ?>'
                        })
                    });
                } catch (error) {
                    console.error('Failed to clear cache:', error);
                }
            }

            hideLockout() {
                this.lockoutContainer.style.display = 'none';
                this.verificationFormContainer.style.display = 'block';
                if (this.lockoutInterval) {
                    clearInterval(this.lockoutInterval);
                }
            }

            startLockoutTimer(remainingSeconds) {
                this.updateLockoutDisplay(remainingSeconds);
                
                this.lockoutInterval = setInterval(() => {
                    remainingSeconds--;
                    this.updateLockoutDisplay(remainingSeconds);
                    
                    if (remainingSeconds <= 0) {
                        clearInterval(this.lockoutInterval);
                        this.hideLockout();
                        attemptManager.resetAttempts();
                        showSuccess('You can now try again.');
                    }
                }, 1000);
            }

            updateLockoutDisplay(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                this.lockoutTimer.textContent = 
                    `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
        }

        // Global instances
        let verificationTimer;
        let attemptManager;
        let lockoutManager;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            verificationTimer = new PersistentTimer();
            attemptManager = new AttemptManager();
            lockoutManager = new LockoutManager();
            
            // Check for active lockout
            if (attemptManager.isLocked()) {
                const remainingTime = attemptManager.getRemainingLockoutTime();
                lockoutManager.showLockout(remainingTime);
                return;
            }
            
            const codeInputs = document.querySelectorAll('.code-input');
            const otpInput = document.getElementById('otpCode');
            const verificationForm = document.getElementById('verificationForm');
            const verificationFormContainer = document.getElementById('verificationFormContainer');
            const verificationSuccess = document.getElementById('verificationSuccess');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const verifyBtn = document.getElementById('verifyBtn');
            const redirectCountdown = document.getElementById('redirectCountdown');
            const lockoutResendLink = document.getElementById('lockoutResendLink');

            // Auto-focus and input handling
            codeInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value.replace(/\D/g, '');
                    e.target.value = value ? value[0] : '';
                    
                    if (value) {
                        e.target.classList.add('filled');
                        e.target.classList.remove('error');
                        
                        if (index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                        }
                        
                        updateOtpInput();
                        
                        // Auto-verify when all 6 digits are filled
                        if (otpInput.value.length === 6) {
                            setTimeout(() => verifyOtp(), 300);
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }
                });
                
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        codeInputs[index - 1].focus();
                        codeInputs[index - 1].value = '';
                        codeInputs[index - 1].classList.remove('filled');
                        updateOtpInput();
                    }
                });
                
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                    
                    if (pasteData.length === 6) {
                        pasteData.split('').forEach((char, idx) => {
                            if (codeInputs[idx]) {
                                codeInputs[idx].value = char;
                                codeInputs[idx].classList.add('filled');
                                codeInputs[idx].classList.remove('error');
                            }
                        });
                        
                        updateOtpInput();
                        codeInputs[5].focus();
                        
                        setTimeout(() => verifyOtp(), 300);
                    }
                });
                
                input.addEventListener('focus', (e) => {
                    e.target.select();
                });
            });

            function updateOtpInput() {
                const code = Array.from(codeInputs).map(input => input.value).join('');
                otpInput.value = code;
                return code;
            }

            // Form submission
            verificationForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await verifyOtp();
            });

            // Resend code functionality
            document.getElementById('resendLink').addEventListener('click', async (e) => {
                e.preventDefault();
                if (verificationTimer.isExpired()) {
                    await resendOtp();
                }
            });

            // Lockout resend functionality
            lockoutResendLink.addEventListener('click', async (e) => {
                e.preventDefault();
                await resendOtp(true);
            });

            async function verifyOtp() {
                // Check if locked out
                if (attemptManager.isLocked()) {
                    const remainingTime = attemptManager.getRemainingLockoutTime();
                    lockoutManager.showLockout(remainingTime);
                    showError('Please wait before trying again.');
                    return;
                }

                if (verificationTimer.isExpired()) {
                    showError('Verification code has expired. Please request a new one.');
                    return;
                }

                const otp = otpInput.value;
                if (otp.length !== 6) {
                    showError('Please enter all 6 digits');
                    codeInputs.forEach(input => {
                        if (!input.value) {
                            input.classList.add('error');
                        }
                    });
                    return;
                }

                // Show loading state
                verifyBtn.disabled = true;
                verifyBtn.classList.add('verifying');
                loadingSpinner.style.display = 'block';
                verifyBtn.querySelector('span').textContent = 'Verifying...';
                
                hideMessages();
                codeInputs.forEach(input => input.classList.remove('error'));

                try {
                    const formData = new FormData(verificationForm);
                    
                    const response = await fetch(verificationForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Clear all storage on success
                        localStorage.removeItem('student_verification_timer_{{ $email }}');
                        localStorage.removeItem('student_verification_attempts_{{ $email }}');
                        
                        // Show success state
                        verificationFormContainer.style.display = 'none';
                        verificationSuccess.style.display = 'block';
                        
                        // Start redirect countdown
                        let countdown = 3;
                        const countdownInterval = setInterval(() => {
                            redirectCountdown.textContent = `Redirecting in ${countdown} second${countdown !== 1 ? 's' : ''}`;
                            countdown--;
                            
                            if (countdown < 0) {
                                clearInterval(countdownInterval);
                                window.location.href = data.redirect || '/student/dashboard';
                            }
                        }, 1000);
                        
                        setTimeout(() => {
                            window.location.href = data.redirect || '/student/dashboard';
                        }, 3000);
                    } else {
                        // Increment attempt counter
                        const currentAttempts = attemptManager.incrementAttempt();
                        
                        if (currentAttempts >= maxAttempts) {
                            // Lock out user
                            showError('Too many attempts. Please wait 15 minutes before trying again.');
                            const remainingTime = attemptManager.getRemainingLockoutTime();
                            lockoutManager.showLockout(remainingTime);
                            verifyBtn.classList.remove('verifying');
                            verifyBtn.classList.add('locked');
                            verifyBtn.querySelector('span').textContent = 'Locked';
                        } else {
                            showError(data.message || `Invalid OTP. ${maxAttempts - currentAttempts} attempt${maxAttempts - currentAttempts !== 1 ? 's' : ''} remaining.`);
                            
                            // Visual feedback for wrong code
                            codeInputs.forEach(input => input.classList.add('error'));
                            
                            // Clear inputs after error
                            setTimeout(() => {
                                codeInputs.forEach(input => {
                                    input.value = '';
                                    input.classList.remove('filled', 'error');
                                });
                                otpInput.value = '';
                                codeInputs[0].focus();
                            }, 1500);
                        }
                    }
                } catch (error) {
                    showError('Network error. Please check your connection and try again.');
                } finally {
                    // Reset button state
                    verifyBtn.disabled = false;
                    verifyBtn.classList.remove('verifying');
                    loadingSpinner.style.display = 'none';
                    verifyBtn.querySelector('span').textContent = 'Verify & Complete Registration';
                }
            }

            async function resendOtp(force = false) {
                // Check if locked out (unless forcing)
                if (!force && attemptManager.isLocked()) {
                    const remainingTime = attemptManager.getRemainingLockoutTime();
                    lockoutManager.showLockout(remainingTime);
                    showError('Please wait before requesting a new code.');
                    return;
                }

                try {
                    const response = await fetch('{{ route("resend-student-otp") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: '{{ $email }}',
                            reset_attempts: true
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Reset attempts and timer
                        attemptManager.resetAttempts();
                        verificationTimer.resetTimer();
                        
                        // Clear all inputs
                        codeInputs.forEach(input => {
                            input.value = '';
                            input.classList.remove('filled', 'error', 'locked');
                        });
                        otpInput.value = '';
                        codeInputs[0].focus();
                        
                        // If coming from lockout, hide lockout screen
                        lockoutManager.hideLockout();
                    } else {
                        showError(data.message || 'Failed to resend OTP');
                    }
                } catch (error) {
                    showError('Failed to resend OTP. Please try again.');
                }
            }

            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                successMessage.style.display = 'none';
                
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }

            function showSuccess(message) {
                successMessage.textContent = message;
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';
                
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000);
            }

            function hideMessages() {
                errorMessage.style.display = 'none';
                successMessage.style.display = 'none';
            }
        });
    </script>
</body>
</html>
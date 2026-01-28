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
    <title>Verification Code - Student Data</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        .verification-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(50, 50, 93, 0.1), 0 10px 20px rgba(0, 0, 0, 0.08);
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
            background: linear-gradient(90deg, #3490dc, #6574cd, #3490dc);
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .verification-title {
            color: #2d3748;
            text-align: center;
            margin-bottom: 15px;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #3490dc 0%, #6574cd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .verification-subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 35px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .verification-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .code-inputs-container {
            position: relative;
        }
        
        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 25px 0;
        }
        
        .code-input {
            width: 55px;
            height: 65px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            border: 3px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            transition: all 0.3s ease;
            color: #2d3748;
        }
        
        .code-input:focus {
            border-color: #3490dc;
            box-shadow: 0 0 0 4px rgba(52, 144, 220, 0.2);
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
            color: #3490dc;
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
            background: linear-gradient(90deg, #3490dc, #6574cd);
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
        
        .resend-section {
            text-align: center;
            margin-top: 25px;
        }
        
        #resendLink {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3490dc;
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
        
        .btn-verify {
            background: linear-gradient(135deg, #3490dc 0%, #6574cd 100%);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(52, 144, 220, 0.3);
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

        @media (max-width: 480px) {
            .verification-container {
                width: 100%;
                padding: 30px 20px;
            }
            
            .code-input {
                width: 40px;
                height: 50px;
                font-size: 24px;
            }

            #timer {
                font-size: 28px;

            }

            .btn-verify {
                padding: 15px 25px;
                font-size: 16px;


            }


        }

    </style>
</head>
<body>
    <div class="verification-container">
        <h1 class="verification-title">Verification Required</h1>
        <p class="verification-subtitle">Enter the 6-digit code sent to your email/phone.<br>Auto-verifies when complete.</p>
        
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
        
        <!-- Verification Form (shown when not locked out) -->
        <div id="verificationFormContainer">
            <form id="verificationForm" class="verification-form">
                @csrf
                <div class="code-inputs-container">
                    <div class="code-inputs">
                        <input type="text" class="code-input" maxlength="1" data-index="1" autofocus>
                        <input type="text" class="code-input" maxlength="1" data-index="2">
                        <input type="text" class="code-input" maxlength="1" data-index="3">
                        <input type="text" class="code-input" maxlength="1" data-index="4">
                        <input type="text" class="code-input" maxlength="1" data-index="5">
                        <input type="text" class="code-input" maxlength="1" data-index="6">
                        <input type="hidden" name="verification_code" id="verificationCode">
                    </div>
                    <div class="auto-verify-indicator">Auto-verifies when complete</div>
                </div>
                
                <div class="timer-container">
                    <span class="timer-label">Code expires in</span>
                    <div id="timer">01:00</div>
                    <div class="timer-progress" id="timerProgress"></div>
                </div>
                
                <button type="submit" class="btn-verify" id="verifyBtn">
                    <span>Verify Code</span>
                    <div class="loading-spinner" id="loadingSpinner"></div>
                </button>
            </form>
    
        </div>
        
        <!-- Lockout Screen (shown when attempts exceeded) -->
        <div class="lockout-container" id="lockoutContainer">
            <div class="lockout-icon">🔒</div>
            <h2>Too Many Attempts</h2>
            <p>You have exceeded the maximum number of verification attempts.</p>
            <div class="lockout-timer" id="lockoutTimer">15:00</div>
            <p>Please wait before trying again or contact support.</p>
            <div class="resend-section">
                <a href="#" id="lockoutResendLink">
                    <span>Request New Code</span>
                </a>
            </div>
        </div>
        
        <!-- Success Screen -->
        <div class="verification-success" id="verificationSuccess">
            <div class="success-icon">✓</div>
            <h2>Verification Successful!</h2>
            <p>You are being redirected...</p>
            <div class="redirecting-text" id="redirectCountdown">Redirecting in 3 seconds</div>
        </div>
        
        <div class="back-link">
            <a href="/">&larr; Back to Home</a>
        </div>
    </div>

    <script>
        class AttemptManager {
            constructor(maxAttempts = 3, lockoutTime = 900, storageKey = 'verification_attempts') {
                this.maxAttempts = maxAttempts;
                this.lockoutTime = lockoutTime; // 15 minutes in seconds
                this.storageKey = storageKey;
                this.attemptsContainer = document.getElementById('attemptsContainer');
                this.attemptsCountElement = document.getElementById('attemptsCount');
                this.attemptsIcon = document.getElementById('attemptsIcon');
                this.loadAttempts();
            }

            loadAttempts() {
                const data = localStorage.getItem(this.storageKey);
                if (data) {
                    const { attempts, timestamp } = JSON.parse(data);
                    const now = Math.floor(Date.now() / 1000);
                    const timeElapsed = now - timestamp;
                    
                    if (timeElapsed > this.lockoutTime) {
                        // Reset attempts after lockout period
                        this.resetAttempts();
                        this.isLockedOut = false;
                    } else {
                        this.attempts = attempts;
                        this.isLockedOut = this.attempts >= this.maxAttempts;
                    }
                } else {
                    this.attempts = 0;
                    this.isLockedOut = false;
                }
                this.updateDisplay();
            }

            incrementAttempt() {
                this.attempts++;
                this.saveAttempts();
                this.updateDisplay();
                
                if (this.attempts >= this.maxAttempts) {
                    this.isLockedOut = true;
                    this.startLockoutTimer();
                }
                
                return this.attempts;
            }

            resetAttempts() {
                this.attempts = 0;
                this.isLockedOut = false;
                localStorage.removeItem(this.storageKey);
                this.updateDisplay();
            }

            saveAttempts() {
                const data = {
                    attempts: this.attempts,
                    timestamp: Math.floor(Date.now() / 1000)
                };
                localStorage.setItem(this.storageKey, JSON.stringify(data));
            }

            updateDisplay() {
                const remainingAttempts = this.maxAttempts - this.attempts;
                
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

            startLockoutTimer() {
                const lockoutEnd = Math.floor(Date.now() / 1000) + this.lockoutTime;
                localStorage.setItem('lockout_end', lockoutEnd);
            }

            checkLockout() {
                const lockoutEnd = localStorage.getItem('lockout_end');
                if (!lockoutEnd) return false;

                const now = Math.floor(Date.now() / 1000);
                if (now < lockoutEnd) {
                    this.isLockedOut = true;
                    return true;
                } else {
                    localStorage.removeItem('lockout_end');
                    this.resetAttempts();
                    return false;
                }
            }

            getRemainingLockoutTime() {
                const lockoutEnd = localStorage.getItem('lockout_end');
                if (!lockoutEnd) return 0;

                const now = Math.floor(Date.now() / 1000);
                return Math.max(0, lockoutEnd - now);
            }
        }

        class PersistentTimer {
            constructor(duration = 60, storageKey = 'verification_timer') {
                this.duration = duration;
                this.storageKey = storageKey;
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
                const savedTime = localStorage.getItem(this.storageKey);
                const now = Math.floor(Date.now() / 1000);
                
                if (savedTime) {
                    const elapsed = now - parseInt(savedTime);
                    this.remainingTime = Math.max(0, this.duration - elapsed);
                    
                    if (this.remainingTime > 0) {
                        this.startTimer();
                        this.updateInputsState(false);
                    } else {
                        this.onTimerComplete();
                    }
                } else {
                    this.remainingTime = this.duration;
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
                    
                    // Auto-save progress every 3 seconds
                    if (this.remainingTime % 3 === 0) {
                        const startTime = Math.floor(Date.now() / 1000) - (this.duration - this.remainingTime);
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
                const progressPercentage = (this.remainingTime / this.duration) * 100;
                this.timerProgress.style.transform = `scaleX(${progressPercentage / 100})`;
                
                // Visual warnings
                if (this.remainingTime < 30 && this.remainingTime > 10) {
                    this.timerElement.classList.add('timer-warning');
                    this.timerElement.classList.remove('timer-expired');
                } else if (this.remainingTime <= 10) {
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
            }

            resetTimer() {
                clearInterval(this.interval);
                localStorage.removeItem(this.storageKey);
                this.remainingTime = this.duration;
                const now = Math.floor(Date.now() / 1000);
                localStorage.setItem(this.storageKey, now.toString());
                this.verifyBtn.disabled = false;
                this.resendLink.disabled = true;
                this.updateInputsState(false);
                this.timerElement.classList.remove('timer-expired', 'timer-warning');
                this.startTimer();
                showSuccess('New verification code sent!');
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
            constructor(lockoutDuration = 900) { // 15 minutes
                this.lockoutDuration = lockoutDuration;
                this.lockoutContainer = document.getElementById('lockoutContainer');
                this.lockoutTimer = document.getElementById('lockoutTimer');
                this.verificationFormContainer = document.getElementById('verificationFormContainer');
                this.lockoutInterval = null;
            }

            checkAndShowLockout() {
                const remainingTime = attemptManager.getRemainingLockoutTime();
                
                if (remainingTime > 0) {
                    this.showLockout(remainingTime);
                    return true;
                }
                
                this.hideLockout();
                return false;
            }

            showLockout(remainingSeconds) {
                this.verificationFormContainer.style.display = 'none';
                this.lockoutContainer.style.display = 'block';
                this.startLockoutTimer(remainingSeconds);
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
                        showSuccess('You can now try again. A new code has been sent.');
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
            verificationTimer = new PersistentTimer(60);
            attemptManager = new AttemptManager(3, 900); // 3 attempts, 15min lockout
            lockoutManager = new LockoutManager();
            
            // Check for active lockout
            if (lockoutManager.checkAndShowLockout()) {
                return; // Stop initialization if locked out
            }
            
            const codeInputs = document.querySelectorAll('.code-input');
            const hiddenInput = document.getElementById('verificationCode');
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
                        
                        updateHiddenInput();
                        
                        // Auto-verify when all 6 digits are filled
                        if (hiddenInput.value.length === 6) {
                            setTimeout(() => verifyCode(), 300);
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
                        updateHiddenInput();
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
                        
                        updateHiddenInput();
                        codeInputs[5].focus();
                        
                        setTimeout(() => verifyCode(), 300);
                    }
                });
                
                input.addEventListener('focus', (e) => {
                    e.target.select();
                });
            });

            function updateHiddenInput() {
                const code = Array.from(codeInputs).map(input => input.value).join('');
                hiddenInput.value = code;
                return code;
            }

            // Form submission
            verificationForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await verifyCode();
            });

            // Resend code functionality
            document.getElementById('resendLink').addEventListener('click', async (e) => {
                e.preventDefault();
                if (verificationTimer.isExpired()) {
                    await resendCode();
                }
            });

            // Lockout resend functionality
            lockoutResendLink.addEventListener('click', async (e) => {
                e.preventDefault();
                await resendCode(true); // Force resend even in lockout
            });

            async function verifyCode() {
                // Check if locked out
                if (lockoutManager.checkAndShowLockout()) {
                    showError('Please wait before trying again.');
                    return;
                }

                if (verificationTimer.isExpired()) {
                    showError('Verification code has expired. Please request a new one.');
                    return;
                }

                const code = hiddenInput.value;
                if (code.length !== 6) {
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
                    const response = await fetch('/api/verify-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ 
                            code: code,
                            attempts: attemptManager.attempts + 1,
                            timestamp: Math.floor(Date.now() / 1000)
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Clear all storage on success
                        localStorage.removeItem('verification_timer');
                        localStorage.removeItem('verification_attempts');
                        localStorage.removeItem('lockout_end');
                        
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
                                window.location.href = data.redirect || '/dashboard';
                            }
                        }, 1000);
                        
                        setTimeout(() => {
                            window.location.href = data.redirect || '/dashboard';
                        }, 3000);
                    } else {
                        // Increment attempt counter
                        const currentAttempts = attemptManager.incrementAttempt();
                        
                        if (currentAttempts >= attemptManager.maxAttempts) {
                            // Lock out user
                            showError('Too many attempts. Please wait 15 minutes before trying again.');
                            lockoutManager.checkAndShowLockout();
                            verifyBtn.classList.remove('verifying');
                            verifyBtn.classList.add('locked');
                            verifyBtn.querySelector('span').textContent = 'Locked';
                        } else {
                            showError(data.message || `Invalid code. ${attemptManager.maxAttempts - currentAttempts} attempt${attemptManager.maxAttempts - currentAttempts !== 1 ? 's' : ''} remaining.`);
                            
                            // Visual feedback for wrong code
                            codeInputs.forEach(input => input.classList.add('error'));
                            
                            // Clear inputs after error
                            setTimeout(() => {
                                codeInputs.forEach(input => {
                                    input.value = '';
                                    input.classList.remove('filled', 'error');
                                });
                                hiddenInput.value = '';
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
                    verifyBtn.querySelector('span').textContent = 'Verify Code';
                }
            }

            async function resendCode(force = false) {
                // Check if locked out (unless forcing)
                if (!force && lockoutManager.checkAndShowLockout()) {
                    showError('Please wait before requesting a new code.');
                    return;
                }

                try {
                    const response = await fetch('/api/resend-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
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
                        hiddenInput.value = '';
                        codeInputs[0].focus();
                        
                        // If coming from lockout, hide lockout screen
                        lockoutManager.hideLockout();
                    } else {
                        showError(data.message || 'Failed to resend code');
                    }
                } catch (error) {
                    showError('Failed to resend code. Please try again.');
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
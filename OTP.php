?php
session_start();

// Define the expiration time (5 minutes)
$EXPIRY_SECONDS = 300; 

// This block handles AJAX requests from the JavaScript.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure we are responding with JSON for AJAX calls
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    // --- OTP GENERATION LOGIC ---
    if (isset($data['action']) && $data['action'] === 'generate') {
        // 1. Generate new 6-digit OTP
        $newOTP = strval(mt_rand(100000, 999999));
        
        // 2. Store the OTP and the current timestamp in the session
        $_SESSION['otp'] = $newOTP;
        $_SESSION['otp_timestamp'] = time();

        // Respond to client (Note: NEVER send the real OTP in a production response)
        echo json_encode([
            'success' => true,
            'message' => 'OTP generated successfully.',
            'debug_otp' => $newOTP // For console logging only during development
        ]);
        exit;
    }

    // --- OTP VERIFICATION LOGIC ---
    if (isset($data['action']) && $data['action'] === 'verify' && isset($data['code'])) {
        $inputCode = trim($data['code']);
        $storedOTP = $_SESSION['otp'] ?? null;
        $otpTimestamp = $_SESSION['otp_timestamp'] ?? 0;
        $currentTime = time();

        if (empty($storedOTP) || empty($otpTimestamp)) {
            echo json_encode(['success' => false, 'message' => 'No active OTP found. Please request a new one.']);
            exit;
        }

        // 1. Check for expiration
        if (($currentTime - $otpTimestamp) > $EXPIRY_SECONDS) {
            // Clear expired session data
            unset($_SESSION['otp']);
            unset($_SESSION['otp_timestamp']);
            echo json_encode(['success' => false, 'message' => 'Verification failed: The OTP has expired. Please request a new one.']);
            exit;
        }

        // 2. Check for match
        if ($inputCode === $storedOTP) {
            // Success: Clear OTP after successful use
            unset($_SESSION['otp']);
            unset($_SESSION['otp_timestamp']);
            echo json_encode(['success' => true, 'message' => 'Success! Access granted.']);
            exit;
        } else {
            // Mismatch
            echo json_encode(['success' => false, 'message' => 'Verification failed: The entered code is incorrect.']);
            exit;
        }
    }
}
// If not an AJAX POST request, the PHP simply falls through to the HTML below.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure OTP Verification (PHP Integrated)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            /* Dark background with subtle radial gradient for depth */
            background: radial-gradient(circle at 50% 50%, #2c3e50 0%, #1a202c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-brand-green { background-color: #00553A; }
        .text-brand-yellow { color: #FFD700; }
        .card-shadow { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); }
        .card-bg-gradient { background: linear-gradient(145deg, #006642, #00442e); }

        /* --- HEADER GRAPHICS --- */
        .text-glow {
            text-shadow: 0 0 8px rgba(255, 215, 0, 0.7); /* Subtle yellow glow */
        }
        .gradient-divider {
            height: 3px;
            width: 100%;
            background: linear-gradient(to right, #FFD700, #00553A); /* Gold to green gradient */
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 1.5px;
        }

        /* Enhanced OTP Input Styling */
        .otp-input-style {
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.1em; /* Give a slight initial spread */
        }
        .otp-input-style:focus {
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.5), 0 0 15px rgba(255, 215, 0, 0.7); /* Stronger glow */
            border-color: #FFD700; /* Yellow border on focus */
            letter-spacing: 0.3em; /* More pronounced letter spacing on focus/active */
        }
        .otp-input-style::placeholder {
            color: #ccc;
            opacity: 0.7;
        }

        /* Verification Section Transition (for when it becomes active) */
        .verification-active {
            opacity: 1 !important;
            pointer-events: auto !important;
            transform: translateY(0) !important;
            filter: blur(0) !important;
        }
        /* Initial state for the verification section when inactive */
        #verification-section {
            opacity: 0.5;
            pointer-events: none;
            transform: translateY(20px); /* Start slightly lower */
            filter: blur(2px); /* Start slightly blurry */
            transition: all 0.5s ease-out; /* Smooth transition */
        }

        /* Keyframe for pulse animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse-slow {
            animation: pulse 3s infinite;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#00553A',
                        'brand-yellow': '#FFD700',
                    }
                }
            }
        }
    </script>
</head>
<body>

<div id="main-container" class="flex items-center justify-center min-h-screen w-full p-4">

    <!-- OTP Content Card (Centralized, max-w-md) --><div id="otp-card" class="w-full max-w-md card-bg-gradient card-shadow rounded-xl p-6 sm:p-10 flex flex-col transition-all duration-300">
        
        <!-- Header & Branding --><div class="mb-10 text-white">
            <div class="mb-4">
                <h1 class="text-4xl font-extrabold text-brand-yellow tracking-wider text-glow">SECURE ACCESS</h1>
                <p class="text-sm opacity-85 mt-1">One-Time Password Verification</p>
            </div>
            
            <!-- Gradient Divider -->
            <div class="gradient-divider"></div>
            
            <p class="text-xs text-gray-300 pt-3">
                This simulator demonstrates the full client-side OTP flow.
            </p>
        </div>

        <!-- OTP Generation Section --><div id="generation-section" class="space-y-4 p-5 bg-gray-50 rounded-lg shadow-inner mb-6">
            <h2 class="text-xl font-bold text-brand-green">1. Generate Code</h2>
            <input type="email" id="contactInput" value="user@example.com"
                   class="w-full p-3 border-2 border-gray-300 rounded-lg focus:border-green-500 text-gray-700 transition duration-200"
                   placeholder="Enter Phone Number or Email">
            <button onclick="sendOTP()"
                    class="w-full bg-brand-green text-yellow-400 p-3 rounded-lg font-bold shadow-lg hover:bg-green-700 active:shadow-xl transform hover:-translate-y-0.5 transition duration-150">
                SEND OTP
            </button>
        </div>

        <!-- OTP Verification Section (Initially dimmed and slightly blurred) --><div id="verification-section" class="space-y-4 p-5 bg-gray-50 rounded-lg shadow-inner">
            <h2 class="text-xl font-bold text-brand-green">2. Verify Code</h2>
            <input type="text" id="otpInput" maxlength="6"
                   class="w-full p-3 text-center text-3xl tracking-widest border-2 border-green-300 rounded-lg text-gray-800 font-mono otp-input-style"
                   placeholder="000000" inputmode="numeric">
            
            <button onclick="verifyOTP()"
                    class="w-full bg-yellow-400 text-brand-green p-3 rounded-lg font-bold shadow-lg hover:bg-yellow-500 active:shadow-xl transform hover:-translate-y-0.5 transition duration-150">
                VERIFY NOW
            </button>
            <button onclick="resetApp()"
                    class="w-full bg-gray-300 text-gray-700 p-2 mt-2 rounded-lg text-sm hover:bg-gray-400 transition duration-150">
                Start New Session
            </button>
        </div>

        <!-- Message Area (styled for dark background) --><div id="messageArea" class="mt-6 p-4 rounded-lg text-center font-medium text-white transition-all duration-300">
            <!-- Messages will appear here --></div>

    </div>
</div>

<script>
    // --------------------------------------------------------------------------------
    // JAVASCRIPT LOGIC (NOW TALKING TO PHP BACKEND)
    // --------------------------------------------------------------------------------

    const EXPIRY_SECONDS = 300; // 5 minutes, used for display only
    const messageArea = document.getElementById('messageArea');
    const verificationSection = document.getElementById('verification-section');

    /**
     * Helper function to display messages to the user.
     * @param {string} msg - The message content.
     * @param {string} type - 'success', 'error', 'info', 'warning'.
     * @param {boolean} isOTPGeneration - Flag to apply special styling for OTP Sent message.
     */
    function displayMessage(msg, type, isOTPGeneration = false) {
        let classes = '';
        let icon = '';
        let secondaryInfo = '';
        const contact = document.getElementById('contactInput').value.trim();

        if (type === 'success') {
            classes = 'bg-green-700 text-white border border-yellow-400';
            icon = '✅';
        } else if (type === 'error') {
            classes = 'bg-red-700 text-white border border-red-500';
            icon = '❌';
        } else if (type === 'warning') {
            classes = 'bg-yellow-700 text-gray-900 border border-yellow-500';
            icon = '⚠️';
        } else if (isOTPGeneration) {
            // Special styling for OTP Generation (OTP Sent)
            classes = 'bg-blue-900 text-white border border-yellow-400';
            icon = '📧'; // Envelope icon
            const minutes = EXPIRY_SECONDS / 60;
            secondaryInfo = `
                <div class="mt-3 pt-3 border-t border-yellow-400/50 flex items-center justify-center space-x-2">
                    <!-- Timer Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-yellow-400 animate-pulse-slow">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="text-lg font-bold text-yellow-400">EXPIRES IN ${minutes} MINUTES</span>
                </div>
            `;
        } else {
            // Default info styling (e.g., reset app message)
            classes = 'bg-gray-700 text-white border border-gray-500';
            icon = 'ℹ️';
        }

        messageArea.className = `mt-6 p-4 rounded-lg text-center font-medium transition-all duration-300 ${classes}`;
        messageArea.innerHTML = `
            <p class="flex items-center justify-center gap-2">
                <span class="text-2xl">${icon}</span>
                <span>${msg}</span>
            </p>
            ${secondaryInfo}
        `;
    }

    /**
     * Sends a request to the PHP backend to generate and store a new OTP.
     */
    async function sendOTP() {
        const contact = document.getElementById('contactInput').value.trim();

        if (!contact) {
            displayMessage("Please enter a contact method (phone/email).", 'warning');
            return;
        }

        try {
            // Send request to the current PHP script
            const response = await fetch(window.location.href, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'generate', contact: contact })
            });

            const result = await response.json();
            
            if (result.success) {
                document.getElementById('otpInput').value = '';
                // Animate in the verification section
                verificationSection.classList.add('verification-active');
                
                displayMessage(
                    `A 6-digit code has been sent to <span class="text-brand-yellow font-semibold">${contact}</span>. Please check your messages.`,
                    'info',
                    true // Flag for special OTP Generation graphics
                );
                // Log debug OTP from the server response
                if (result.debug_otp) {
                    console.log(`[DEV ONLY] Server Generated OTP: ${result.debug_otp}`);
                }
            } else {
                displayMessage(result.message || "Failed to generate OTP.", 'error');
            }
        } catch (error) {
            console.error('Generation Error:', error);
            displayMessage("An unexpected error occurred during OTP generation.", 'error');
        }
    }

    /**
     * Sends a request to the PHP backend to verify the entered OTP.
     */
    async function verifyOTP() {
        const inputCode = document.getElementById('otpInput').value.trim();

        if (inputCode.length !== 6 || isNaN(inputCode)) {
            displayMessage("Please enter a valid 6-digit numeric code.", 'error');
            return;
        }

        try {
            // Send request to the current PHP script
            const response = await fetch(window.location.href, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'verify', code: inputCode })
            });

            const result = await response.json();

            if (result.success) {
                displayMessage(result.message, 'success');
                // Dim verification section again on success
                verificationSection.classList.remove('verification-active');
            } else {
                displayMessage(result.message, 'error');
                // If expiration error, dim verification section
                if (result.message.includes('expired')) {
                    verificationSection.classList.remove('verification-active');
                }
            }
        } catch (error) {
            console.error('Verification Error:', error);
            displayMessage("An unexpected error occurred during OTP verification.", 'error');
        }
    }

    /**
     * Resets the entire UI. (PHP handles session clearing on server-side only on success/expiration)
     */
    function resetApp() {
        document.getElementById('otpInput').value = '';
        displayMessage("Ready to generate a new OTP.", 'info', false);
        // Ensure verification section is dimmed/inactive initially
        verificationSection.classList.remove('verification-active');
    }

    // Initialize on load
    window.onload = resetApp;
</script>

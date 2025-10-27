<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";  // Replace with your DB username
$password = "";      // Replace with your DB password
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $barangay = trim($_POST['barangay']);
    $file_path = "";

    // Validate passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = basename($_FILES['file']['name']);
            $file_path = $upload_dir . $file_name;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                $error = "File upload failed.";
            }
        }

        if (!$error) {
            // Store password in plain text (INSECURE - for testing only!)
            $stmt = $conn->prepare("INSERT INTO account (email, password, barangay, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $password, $barangay, $file_path);
            if ($stmt->execute()) {
                $success = "Account created successfully! Please sign in.";
                header("Location: sign-in.php?success=1");
                exit();
            } else {
                $error = "Error creating account. Email may already exist.";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eBCsH System - Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <style>
        /* Make layout cover entire viewport - only design changes */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Quicksand', Arial, sans-serif;
            background: #163832;
        }

        /* full-viewport container */
        .container {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            border-radius: 0;
        }

        .left-panel {
            flex: 1;
            min-width: 320px;
            background: linear-gradient(135deg, #a3c3ad 0%, #22594b 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 40px;
            box-sizing: border-box;
        }

        .logo-img {
            width: 270px;
            max-width: 48%;
            border-radius: 130px;
            margin-bottom: 30px;
        }

        .welcome-text {
            color: #fff;
            font-size: 1.25rem;
            text-align: center;
            margin-top: 12px;
            line-height: 1.4;
        }

        .right-panel {
            flex: 1;
            min-width: 420px; /* increased minimum */
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center; /* center horizontally */
            justify-content: center; /* center vertically */
            padding: 56px; /* increased padding to match sign-up */
            box-sizing: border-box;
        }

        /* Tabs and form sizes aligned (unchanged widths but centered within right panel) */
        .tab-group {
            display: flex;
            width: 100%;
            max-width: 760px;
            background: #f5f6fa;
            border-radius: 18px;
            margin-bottom: 28px;
            overflow: hidden;
            height: 56px;
            align-items: center;
        }
        .tab-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            font-size: 1.15rem;
            color: #5d7c76;
            cursor: pointer;
            font-weight: 500;
            border-radius: 16px;
            height: 38px;
            margin: 0 8px;
        }
        .tab-btn.active {
            background: #fff;
            color: #22594b;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            margin: 0 8px;
            height: 38px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-title {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
            margin-top: 10px;
            text-align: left;
            width: 100%;
            max-width: 760px;
        }
        .form-subtitle {
            font-size: 1.05rem;
            color: #888;
            margin-bottom: 22px;
            text-align: left;
            width: 100%;
            max-width: 760px;
        }

        form {
            width: 100%;
            max-width: 760px;
            margin-left: auto;
            margin-right: auto;
        }

        .input-group {
            width: 100%;
            margin-bottom: 18px;
        }
        .input-label {
            font-size: 0.98rem;
            color: #222;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .input-box {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: none;
            background: #f5f6fa;
            font-size: 1rem;
            margin-bottom: 2px;
            box-sizing: border-box;
            outline: none;
            transition: box-shadow 0.2s;
        }
        #barangay.input-box {
            padding-left: 12px;
            padding-right: 12px;
        }
        .input-box:focus {
            box-shadow: 0 0 0 2px #dbeafe;
        
        }
        .input-eye-wrapper {
            position: relative;
            width: 100%;
            display: block;
        }
        .input-box {
            width: 100%;
            padding: 12px 44px 12px 44px; /* ensure space for left/right icons */
            border-radius: 8px;
            border: none;
            background: #f5f6fa;
            font-size: 1rem;
            margin-bottom: 2px;
            box-sizing: border-box;
            outline: none;
            transition: box-shadow 0.2s;
            display: block;
        }
        .input-box:focus {
            box-shadow: 0 0 0 2px #dbeafe;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            opacity: 0.7;
            display: block;
            pointer-events: none;
        }
        .eye-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            cursor: pointer;
            opacity: 0.85;
            display: block;
            background: transparent;
            border: 0;
            padding: 0;
            margin: 0;
        }

        /* NEW: grid layout to match requested design */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 24px;
            align-items: start;
        }
        .form-grid .full { grid-column: 1 / -1; }

        /* upload box */
        .upload-box {
            height: 110px;
            border-radius: 8px;
            background: #f5f6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed rgba(0,0,0,0.06);
        }
        .upload-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #888;
            background: #fff;
            cursor: pointer;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            gap: 8px;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
        }
        .checkbox-group label {
            font-size: 0.98rem;
            color: #222;
        }
        .submit-btn {
            width: 100%;
            padding: 12px 0;
            border-radius: 8px;
            border: none;
            background: linear-gradient(180deg,#163832,#194f43);
            color: #fff;
            font-size: 1.08rem;
            font-weight: 600;
            margin-top: 8px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #22242c;
        }

        /* bottom note */
        .bottom-note {
            width: 100%;
            max-width: 760px;
            text-align: center;
            margin-top: 12px;
            color: #666;
        }

        .error { color: red; font-size: 0.9rem; margin-bottom: 10px; }
        .success { color: green; font-size: 0.9rem; margin-bottom: 10px; }

        /* Responsive: stack panels vertically on small screens */
        @media (max-width: 980px) {
            .container { flex-direction: column; height: auto; }
            .left-panel, .right-panel { width:100%; padding:32px 24px; }
            .logo-img { max-width:180px; margin-bottom:18px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <img class="logo-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTDCuh4kIpAtR-QmjA1kTjE_8-HSd8LSt3Gw&s" alt="Robot">
            <div class="welcome-text">
                Welcome to eBCsH<br>Your friendly assistant is here to help!
            </div>
        </div>
        <div class="right-panel">
            <div class="tab-group">
                <button class="tab-btn" type="button" onclick="window.location.href='sign-in.php'">Sign In</button>
                <button class="tab-btn active" type="button" onclick="window.location.href='sign-up.php'">Sign Up</button>
            </div>
            <div class="form-title">Create Account</div>
            <div class="form-subtitle">Join us and get started today</div>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form id="signUpForm" method="POST" action="sign-up.php" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="full">
                        <div class="input-group">
                            <div class="input-label">Email Address</div>
                            <div class="input-eye-wrapper">
                                <img class="input-icon" src="https://img.icons8.com/ios-filled/50/000000/new-post.png" alt="Email Icon">
                                <input class="input-box" type="email" name="email" placeholder="ryan123@gmail.com" required>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="input-group">
                            <div class="input-label">Password</div>
                            <div class="input-eye-wrapper">
                                <img class="input-icon" src="https://cdn-icons-png.flaticon.com/128/345/345535.png" alt="Password Icon">
                                <input id="signUpPassword" class="input-box" type="password" name="password" placeholder="Create a password" required>
                                <img id="eyeSignUpPassword" class="eye-toggle" src="https://cdn-icons-png.flaticon.com/128/2767/2767146.png" alt="Show Password">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="input-group">
                            <div class="input-label">Barangay</div>
                            <div class="input-eye-wrapper">
                                <input id="barangay" class="input-box" type="text" name="barangay" placeholder="Address">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="input-group">
                            <div class="input-label">Confirm Password</div>
                            <div class="input-eye-wrapper">
                                <img class="input-icon" src="https://cdn-icons-png.flaticon.com/128/345/345535.png" alt="Password Icon">
                                <input id="confirmPassword" class="input-box" type="password" name="confirm_password" placeholder="Confirm your password" required>
                                <img id="eyeConfirmPassword" class="eye-toggle" src="https://cdn-icons-png.flaticon.com/128/2767/2767146.png" alt="Show Password">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="input-label">Upload Files</div>
                        <div class="upload-box" id="uploadBox">
                            <input id="fileInput" type="file" name="file" style="display:none" multiple>
                            <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">Browse File</button>
                        </div>
                    </div>

                </div>

                <div class="checkbox-group" style="margin-top:12px;">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the Terms and Conditions</label>
                </div>

                <button class="submit-btn" type="submit">Create Account</button>
            </form>

            <div class="bottom-note">Already have an account ? <a href="sign-in.php">Sign in instead</a></div>
        </div>
    </div>
    <script>
        // Password eye toggle logic (unchanged approach)
        function setupEyeToggle(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (!input || !eye) return;
            eye.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    eye.src = 'https://cdn-icons-png.flaticon.com/128/709/709612.png';
                } else {
                    input.type = 'password';
                    eye.src = 'https://cdn-icons-png.flaticon.com/128/2767/2767146.png';
                }
            });
        }
        setupEyeToggle('signUpPassword', 'eyeSignUpPassword');
        setupEyeToggle('confirmPassword', 'eyeConfirmPassword');

        // file preview button label
        (function(){
            const fileInput = document.getElementById('fileInput');
            const uploadBox = document.getElementById('uploadBox');
            const btn = uploadBox.querySelector('.upload-btn');
            fileInput.addEventListener('change', function(){
                if (!fileInput.files || fileInput.files.length === 0) {
                    btn.textContent = 'Browse File';
                    return;
                }
                const names = Array.from(fileInput.files).map(f => f.name).join(', ');
                btn.textContent = names.length > 36 ? names.slice(0,33) + '...' : names;
            });
        })();
    </script>
</body>
</html>

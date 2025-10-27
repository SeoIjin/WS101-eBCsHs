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

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, password FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $stored_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && $password === $stored_password) {
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        header("Location: homepage.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eBCsH System - Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <style>
        /* (Your existing CSS here - unchanged) */
        /* Full-page layout (unchanged) */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #14342f;
            font-family: 'Quicksand', Arial, sans-serif;
        }

        /* Make the container fill the entire viewport (unchanged) */
        .container {
            display: flex;
            width: 100vw;
            height: 100vh;
            background: transparent;
            border-radius: 0;
            overflow: hidden;
        }

        /* Left decorative panel (unchanged) */
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
        }
        .logo-img {
            width: 270px;        /* increased from 270px */
            max-width: 55%;      /* a bit larger on narrow screens */
            border-radius: 130px;
            margin-bottom: 30px; /* slightly more spacing */
        }
        .welcome-text {
            color: #fff;
            font-size: 1.25rem;
            text-align: center;
            margin-top: 12px;
            line-height: 1.4;
        }

        /* --- RIGHT SIDE STYLES (REPLACED) --- */
        /* only styles below edited to match sign-up sizing and adjust label/icon/input heights */
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

        .tab-group {
            display: flex;
            width: 100%;
            max-width: 760px; /* match sign-up */
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
            font-size: 1.08rem;
            color: #5d7c76;
            cursor: pointer;
            font-weight: 500;
            border-radius: 18px;
            height: 40px;
            margin: 0 8px;
        }
        .tab-btn.active {
            background: #fff;
            color: #22594b;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(34,89,75,0.18);
        }

        .form-title {
            width: 100%;
            max-width: 760px; /* match sign-up */
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.5rem; /* slightly larger title */
            font-weight: 700;
            color: #222;
            margin: 6px 0 6px;
            text-align: left;
        }
        .form-subtitle {
            width: 100%;
            max-width: 760px; /* match sign-up */
            font-size: 1.05rem;
            color: #888;
            margin-bottom: 18px;
            text-align: left;
        }

        .form-content {
            width: 100%;
            max-width: 760px; /* match sign-up */
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        /* input spacing, label height and icon sizes adjusted */
        .input-group { margin-bottom: 18px; width: 100%; }
        .input-label {
            font-size: 1rem;
            color:#222;
            margin-bottom:8px;        /* slightly taller gap under label */
            font-weight:600;         /* match sign-up emphasis */
            line-height:1.4;         /* increase label height/spacing */
        }

        .input-eye-wrapper { position: relative; width: 100%; }
        .input-box {
            width: 100%;
            padding: 14px 48px 14px 48px; /* increased top/bottom and left/right padding */
            border-radius: 10px;
            border: none;
            background: #f5f6fa;
            font-size: 1.05rem; /* slightly larger text */
            box-sizing: border-box;
            outline: none;
            color: #222;
            min-height:48px; /* ensure consistent height */
        }
        .input-box:focus { box-shadow: 0 0 0 2px #a3c3ad; }

        /* icon sizes and vertical alignment */
        .input-icon {
            position:absolute;
            left:16px;
            top:50%;
            transform:translateY(-50%);
            width:22px;
            height:22px;
            opacity:.85;
            pointer-events:none;
        }
        .eye-toggle {
            position:absolute;
            right:16px;
            top:50%;
            transform:translateY(-50%);
            width:22px;
            height:22px;
            cursor:pointer;
            opacity:.95;
            background:transparent;
            border:0;
            padding:0;
            margin:0;
        }

        .options-row { display:flex; justify-content:space-between; align-items:center; margin:12px 0 22px 0; width:100%; }
        .checkbox-group { display:flex; align-items:center; gap:8px; }
        .checkbox-group input[type="checkbox"] { margin-right:8px; accent-color:#22594b; }
        .forgot-link { color:#22594b; text-decoration:none; font-weight:600; }

        .submit-btn {
            width: 100%;
            padding: 14px 0;
            border-radius: 10px;
            border: none;
            background: linear-gradient(180deg,#163832,#194f43);
            color: #fff;
            font-size: 1.08rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(16,24,20,0.12);
            margin-bottom: 0;
        }
        .submit-btn:hover { background:#22594b; }

        .bottom-text { width:100%; max-width:760px; text-align:center; margin-top:18px; color:#666; }
        .bottom-text a { color:#22594b; font-weight:700; text-decoration:none; }

        .error { color: red; font-size: 0.9rem; margin-bottom: 10px; }

        /* Responsive: stack panels vertically on small screens (unchanged) */
        @media (max-width: 980px) {
            .container { flex-direction: column; height: auto; }
            .left-panel, .right-panel { width:100%; padding:32px 20px; }
            .logo-img { max-width:220px; width:220px; margin-bottom:20px; }
            .tab-group, .form-content, .form-title, .form-subtitle { max-width:100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel" aria-hidden>
            <img class="logo-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTDCuh4kIpAtR-QmjA1kTjE_8-HSd8LSt3Gw&s" alt="Logo">
            <div class="welcome-text">
                Welcome to eBCsH<br>Your friendly assistant is here to help!
            </div>
        </div>

        <div class="right-panel" role="main" aria-label="Sign in">
            <div class="tab-group" aria-hidden>
                <button class="tab-btn active" type="button" onclick="location.href='sign-in.php'">Sign In</button>
                <button class="tab-btn" type="button" onclick="location.href='sign-up.php'">Sign Up</button>
            </div>

            <div class="form-title">Welcome Back!</div>
            <div class="form-subtitle">Sign in to access your account</div>

            <div class="form-content">
                <?php if ($error): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <form id="signInForm" method="POST" action="sign-in.php">
                    <div class="input-group">
                        <div class="input-label">Email Address</div>
                        <div class="input-eye-wrapper">
                            <img class="input-icon" src="https://img.icons8.com/ios-filled/50/000000/new-post.png" alt="">
                            <input class="input-box" type="email" name="email" placeholder="ryan123@gmail.com" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-label">Password</div>
                        <div class="input-eye-wrapper">
                            <img class="input-icon" src="https://cdn-icons-png.flaticon.com/128/345/345535.png" alt="">
                            <input id="signInPassword" class="input-box" type="password" name="password" placeholder="Enter your password" required>
                            <img id="eyeSignInPassword" class="eye-toggle" src="https://cdn-icons-png.flaticon.com/128/2767/2767146.png" alt="toggle">
                        </div>
                    </div>

                    <div class="options-row">
                        <div class="checkbox-group">
                            <input type="checkbox" id="rememberMe">
                            <label for="rememberMe">Remember me</label>
                        </div>
                        <a class="forgot-link" href="createnewpass.php">Forgot Password?</a>
                    </div>

                    <button class="submit-btn" type="submit">Sign In</button>
                </form>

                <div class="bottom-text">Don't have account? <a href="sign-up.php">Create one now</a></div>
            </div>
        </div>
    </div>

    <script>
        // Password eye toggle logic
        (function setupEye() {
            const input = document.getElementById('signInPassword');
            const eye = document.getElementById('eyeSignInPassword');
            if (!input || !eye) return;
            const openIcon = 'https://cdn-icons-png.flaticon.com/128/709/709612.png';
            const closedIcon = 'https://cdn-icons-png.flaticon.com/128/2767/2767146.png';
            eye.addEventListener('click', function () {
                const showing = input.type === 'password';
                input.type = showing ? 'text' : 'password';
                eye.src = showing ? openIcon : closedIcon;
            });
        })();
    </script>
</body>
</html>
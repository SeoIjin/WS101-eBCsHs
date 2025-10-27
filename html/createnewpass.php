<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create New Password</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #d7f3d7;
            display: flex; /* Center horizontally & vertically */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 380px;
            text-align: center;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 20px;
            font-weight: 600;
            color: #1b1b1b;
            gap: 8px;
        }
            .header i {
                font-size: 18px;
                cursor: pointer;
            }
        .logo {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
        .system-name {
            font-weight: 600;
            margin-left: 5px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 1.3rem;
            color: #222;
        }
        .password-field {
            position: relative;
            width: 100%;
        }
        .password-field input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #dcdcdc;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }
        .password-field input:focus {
            border-color: #006d5b;
        }
        .password-field i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            font-size: 18px;
            }
        .password-field i:hover {
            color: #006d5b;
        }
        ul {
            text-align: left;
            margin: 15px 0;
            list-style: none;
        }
        ul li {
            font-size: 14px;
            margin-bottom: 8px;
            color: #c0392b;
            display: flex;
            align-items: center;
        }
        ul li i {
            margin-right: 8px;
        }
        ul li.valid {
            color: #2e7d32;
        }
        ul li.valid i::before {
            content: "✔";
        }
        ul li.invalid i::before {
            content: "✖";
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background-color: #004d40;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #00695c;
        }
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-arrow-left"></i>
            <img src="logo(10).jpg" alt="System Logo" class="logo">

            <span class="system-name">eBcSH System</span>
        </div>

        <h2>Now create a password</h2>

        <div class="password-field">
            <input type="password" id="password" placeholder="Enter new password">
            <i class="fas fa-eye" id="togglePassword"></i>
        </div>

        <ul>
            <li id="length" class="invalid"><i></i> Has at least 8 characters</li>
            <li id="number" class="invalid"><i></i> Has a number</li>
            <li id="uppercase" class="invalid"><i></i> Has an uppercase letter</li>
        </ul>

        <button id="continueBtn">Continue</button>
    </div>

    <script>
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    const lengthRule = document.getElementById("length");
    const numberRule = document.getElementById("number");
    const uppercaseRule = document.getElementById("uppercase");

    togglePassword.addEventListener("click", () => {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      togglePassword.classList.toggle("fa-eye");
      togglePassword.classList.toggle("fa-eye-slash");
    });

    passwordInput.addEventListener("input", () => {
      const value = passwordInput.value;

      if (value.length >= 8) lengthRule.classList.add("valid");
      else lengthRule.classList.remove("valid");

      if (/\d/.test(value)) numberRule.classList.add("valid");
      else numberRule.classList.remove("valid");

      if (/[A-Z]/.test(value)) uppercaseRule.classList.add("valid");
      else uppercaseRule.classList.remove("valid");
    });
    </script>
</body>
</html>
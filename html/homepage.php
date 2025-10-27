<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit();
}

// Handle logout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    header("Location: sign-in.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>eBCsH - Community Portal</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />


  <style>
    :root {
      --bg-color: #DAF1DE;
      --new-request-color: #007BFF;
      --track-request-color: #00B050;
      --logout-color: #FD7E7E;
      --font: 'Poppins', sans-serif;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: var(--font);
    }

    body {
      background-color: var(--bg-color);
      color: #333;
    }

    
    header {
      background-color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .header-left img {
      height: 50px;
      margin-right: 10px;
    }

    .header-left .title {
      font-size: 16px;
      font-weight: 600;
    }

    .logout-btn {
      background-color: var(--logout-color);
      color: #fff;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
    }

    .logout-btn i {
      margin-right: 6px;
    }

  
    .welcome-section {
      text-align: center;
      padding: 2rem 1rem;
    }

    .welcome-section img {
      height: 100px;
      margin-bottom: 1rem;
    }

    .welcome-section h1 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .welcome-section p {
      font-size: 18px;
      color: #555;
      max-width: 600px;
      margin: 0 auto;
    }

    
    .action-buttons {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 2rem;
      padding: 2rem;
      max-width: 1000px;
      margin: 0 auto;
    }

    .action-card {
      background-color: #fff;
      border-radius: 12px;
      padding: 2.5rem;
      width: 48%;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .action-card i {
      font-size: 36px;
      margin-bottom: 1rem;
      color: #333;
    }

    .action-card h3 {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .action-card p {
      font-size: 15px;
      color: #666;
      margin-bottom: 1.5rem;
    }

    .action-card button {
      border: none;
      border-radius: 8px;
      padding: 0.9rem 1.6rem;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      color: #fff;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .submit-btn {
      background-color: var(--new-request-color);
    }

    .track-btn {
      background-color: var(--track-request-color);
    }

   
    .how-it-works {
      background-color: #fff;
      margin: 2rem auto;
      max-width: 960px;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .how-it-works h2 {
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 700;
    }

    .steps {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .step {
      flex: 1;
      min-width: 200px;
      text-align: center;
      padding: 1rem;
    }

    .step i {
      font-size: 24px;
      color: #f0ad4e;
      margin-bottom: 0.5rem;
    }

    .step p {
      font-size: 14px;
      color: #555;
    }

    
    @media (max-width: 768px) {
      .action-buttons {
        flex-direction: column;
        align-items: center;
      }

      .action-card {
        width: 90%;
      }
    }
  </style>
</head>
<body>

 
  <header>
    <div class="header-left">
      <img src="logo.png" alt="eBCsH Logo" />
      <div class="title">
        Barangay 170-E<br />
        <span style="font-weight: 400;">Community Portal</span>
      </div>
    </div>
    <form method="POST" action="homepage.php" style="display: inline;">
      <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
  </header>

 
  <section class="welcome-section">
    <img src="logo.png" alt="eBCsH Logo" />
    <h1>Welcome to eBCsH</h1>
    <p>Submit health-related requests to your barangay and track their progress in real time. Our system ensures efficient and transparent processing of your requests.</p>
  </section>

  
  <section class="action-buttons">
    <div class="action-card">
      <i class="fas fa-file-alt"></i>
      <h3>Submit Request</h3><br>
      <p>File new requests directly to your local barangay officials.</p>
      <a href="submitreq.php"><button class="submit-btn"> Start a New Request</button></a>
    </div>
    <div class="action-card">
      <i class="fas fa-search"></i>
      <h3>Track Request</h3><br>
      <p>Check the status and updates on your submitted requests any time.</p>
      <a href="trackreq.php"><button class="track-btn"> View Existing Request</button></a>
    </div>
  </section>

  
  <section class="how-it-works">
    <h2>How it Works</h2>
    <div class="steps">
      <div class="step">
        <i class="fas fa-upload"></i>
        <p><strong>Submit</strong><br><br>Fill out the online request form with your details and submit it to the barangay office.</p>
      </div>
      <div class="step">
        <i class="fas fa-bell"></i>
        <p><strong>Track</strong><br><br>Monitor your request status in real-time and receive notification for any updates.</p>
      </div>
      <div class="step">
        <i class= "fas fa-check-circle"></i>
        <p><strong>Receive</strong><br><br>Get notified whenever your request is approved and ready for pickup or delivery.</p>
      </div>
    </div>
  </section>

</body>
</html>

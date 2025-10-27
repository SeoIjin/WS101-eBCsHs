<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";  // Replace with your DB username
$password = "";      // Replace with your DB password
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $contact = trim($_POST['contact']);
    $requesttype = $_POST['requesttype'];
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($fullname) || empty($contact) || empty($requesttype) || empty($description)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[0-9]{11}$/', $contact)) {
        $error = "Contact number must be 11 digits.";
    } else {
        // Generate unique ticket ID
        $year = date('Y');
        $stmt = $conn->prepare("SELECT COUNT(*) FROM requests WHERE YEAR(submitted_at) = ?");
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        $ticket_id = 'BHR-' . $year . '-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);

        // Insert request
        $stmt = $conn->prepare("INSERT INTO requests (ticket_id, user_id, fullname, contact, requesttype, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $ticket_id, $user_id, $fullname, $contact, $requesttype, $description);
        if ($stmt->execute()) {
            $success = "Request submitted successfully! Your Ticket ID is: <strong>$ticket_id</strong>. Use it to track your request.";
        } else {
            $error = "Error submitting request. Please try again.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>NEW REQUEST</title>
    
    <style>
      body {
        font-family: "Poppins", sans-serif;
        margin: 0;
        padding: 0;
        background-color: #13582a;
      }

      .container {
        max-width: 700px;
        margin: 40px auto;
        padding: 30px 30px 20px 30px;
        border-radius: 30px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      }

      .title {
        font-size: 40px;
        font-weight: bold;
        text-align: center;
        color: #3bb43b;
        margin-bottom: 0;
      }

      .description {
        font-size: 16px;
        text-align: center;
        color: #3bb43b;
        margin-bottom: 30px;
        margin-top: 0;
      }

      form {
        display: flex;
        flex-direction: column;
        gap: 18px;
      }

      .row {
        display: flex;
        gap: 20px;
      }

      .form-label {
        font-weight: bold;
        color: #249c3b;
        margin-bottom: 6px;
        font-size: 18px;
      }

      .form-group {
        flex: 1;
        display: flex;
        flex-direction: column;
      }

      input, select, textarea {
        padding: 10px;
        border-radius: 6px;
        border: none;
        background: #d2e7d2;
        font-size: 16px;
        margin-bottom: 0;
        outline: none;
      }

      textarea {
        min-height: 80px;
        resize: vertical;
      }
      
      .submit-button {
        width: 100%;
        padding: 12px;
        background: #249c3b;
        color: #fff;
        font-size: 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 10px;
        letter-spacing: 1px;
      }
    
      .submit-button:hover {
        background: #1e7a2c;
      }

      .message {
        text-align: center;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 6px;
      }
      .success { background: #d4edda; color: #155724; }
      .error { background: #f8d7da; color: #721c24; }
    </style>
  </head>
  <body>
    <div class="container">
      <h1 class="title">Submit New Request</h1>
      <p class="description">
        Please provide detailed information about your any related request or concern.
      </p>
      <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
      <?php endif; ?>
      <form method="POST" action="submitreq.php">
        <div class="row">
          <div class="form-group">
            <label class="form-label" for="fullname"><i>Full</i> Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required />
          </div>
          <div class="form-group">
            <label class="form-label" for="contact"><i>Contact</i> Number</label>
            <input type="tel" id="contact" name="contact" placeholder="Enter contact number Ex.09123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="requesttype"><i>Request</i> Type</label>
          <select id="requesttype" name="requesttype" required>
            <option value="" disabled selected>Select the type of request</option>
            <option value="ID">Barangay ID</option>
            <option value="Clearance">Barangay Business Clearance</option>
            <option value="indigency">Certificate of Indigency</option>
            <option value="Residency">Certificate of Residency</option>
            <option value="No Objection">Clearance of No Objection</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="description"><i>Detailed</i> Description</label>
          <textarea id="description" name="description" placeholder="Please provide a detailed description of your  request, including any relevant symptoms, timeline, or specific assistance needed." required></textarea>
        </div>
        <button type="submit" class="submit-button">SUBMIT</button>
      </form>
    </div>
  </body>
</html>
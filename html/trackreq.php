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

$user_id = $_SESSION['user_id'];
$search_result = null;
$search_error = "";

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id'])) {
    $ticket_id = trim($_POST['ticket_id']);
    if (!empty($ticket_id)) {
        $stmt = $conn->prepare("SELECT ticket_id, requesttype, status, submitted_at FROM requests WHERE ticket_id = ? AND user_id = ?");
        $stmt->bind_param("si", $ticket_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($ticket_id, $requesttype, $status, $submitted_at);
            $stmt->fetch();
            $search_result = ['ticket_id' => $ticket_id, 'requesttype' => $requesttype, 'status' => $status, 'submitted_at' => $submitted_at];
        } else {
            $search_error = "No request found for that ticket ID.";
        }
        $stmt->close();
    } else {
        $search_error = "Please enter a ticket ID.";
    }
}

// Fetch recent requests for the user
$recent_requests = [];
$stmt = $conn->prepare("SELECT ticket_id, requesttype, status, submitted_at FROM requests WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 10");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_requests[] = $row;
}
$stmt->close();
$conn->close();

// Helper function for status color
function statusColor($status) {
    switch ($status) {
        case 'READY': return '#064b38';
        case 'UNDER REVIEW': return '#f39c12';
        case 'COMPLETED': return '#1ea2a8';
        case 'IN PROGRESS': return '#ff6b4a';
        default: return '#6b6f72';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Barangay Health Services — Dashboard</title>
  <style>
    :root{
      --bg:#e6f0e8;
      --deep:#064b38; /* dark green */
      --muted:#f2f6f4;
      --card:#ffffff;
      --accent:#2f8f6f;
      --pill:#cfeee0;
      --ready:#064b38; /* changed to dark green */
      --review:#f39c12;
      --completed:#1ea2a8;
      --inprogress:#ff6b4a;
      --muted-text:#6b6f72;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    html,body{height:100%;}
    body{
      margin:0;
      background: linear-gradient(#c6e0d2 0%, #c6e0d2 50%, #08302a 50%);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px 16px;
      color:#223;
    }

    .frame{
      width:920px;
      max-width:96vw;
      background:var(--card);
      border-radius:12px;
      box-shadow:0 6px 30px rgba(2,26,22,0.25);
      overflow:hidden;
      border-top:18px solid var(--deep);
    }

    header.topbar{
      background:var(--deep);
      color:#fff;
      text-align:center;
      padding:10px 20px;
      font-weight:600;
      letter-spacing:0.2px;
    }

    .container{
      padding:22px;
      display:grid;
      grid-template-columns: 1fr 320px;
      gap:20px;
    }

    /* Track New Request card */
    .track{
      background:linear-gradient(180deg,#fafafa,#f6f8f7);
      border-radius:10px;
      padding:18px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.5);
    }

    .track h2{
      margin:0 0 8px 0;
      font-size:18px;
      color:var(--deep); /* dark green */
    }
    .muted{color:var(--muted-text);font-size:13px}

    .input-row{
      display:flex;
      gap:10px;
      margin-top:10px;
    }
    .input-row input[type=text]{
      flex:1;
      padding:10px 12px;
      border-radius:8px;
      border:1px solid #e1e6e4;
      font-size:14px;
      background:#fff;
    }
    .btn{
      background:var(--deep);
      color:#fff;
      border:none;
      padding:10px 14px;
      border-radius:8px;
      cursor:pointer;
      font-weight:600;
      min-width:110px;
    }

    .help{
      background:linear-gradient(180deg,#fff,#f7fbf9);
      border-radius:10px;
      padding:18px;
      text-align:center;
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap:8px;
      border:1px solid #e9f0ed;
    }
    .help .icon{
      width:56px;height:56px;border-radius:999px;background:var(--muted);display:inline-grid;place-items:center;font-weight:700;color:var(--deep);margin:0 auto 6px auto;
    }
    .help a{font-size:13px;color:var(--deep);text-decoration:none}

    /* Recent requests area */
    .recent{
      padding:20px 22px 28px 22px;
      border-top:1px solid #eef3ef;
      display:flex;
      flex-direction:column;
      gap:14px;
    }
    .recent-top{
      display:flex;align-items:center;justify-content:space-between;gap:12px
    }
    .tabs{display:flex;gap:8px}
    .tab{
      background:var(--muted);
      padding:6px 10px;border-radius:20px;font-weight:600;font-size:13px;color:var(--deep);cursor:pointer;border:1px solid rgba(0,0,0,0.03)
    }
    .tab.active{background:var(--deep);color:#fff}

    .cards{
      display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:6px
    }
    .card{
      background:linear-gradient(180deg,#fff,#fbfffd);
      border-radius:10px;padding:14px;border:1px solid #eef4f1;box-shadow:0 4px 14px rgba(5,24,20,0.04);
    }
    .card .ticket{font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:8px}
    .dot{width:10px;height:10px;border-radius:99px;display:inline-block}
    .status{font-size:13px;font-weight:700}
    .meta{font-size:13px;color:var(--muted-text);margin-top:6px}

    .badge{
      display:inline-block;padding:6px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#f4faf7;color:var(--ready);border:1px solid rgba(47,178,90,0.08)
    }

    .search-result {
      margin-top: 14px;
      padding: 12px;
      border-radius: 8px;
      background: #f0f8f0;
      border: 1px solid #d0e0d0;
    }
    .search-error {
      margin-top: 14px;
      padding: 12px;
      border-radius: 8px;
      background: #ffeaea;
      border: 1px solid #ffdddd;
      color: #d9534f;
    }

    /* responsive */
    @media (max-width:880px){
      .container{grid-template-columns:1fr;}
      .cards{grid-template-columns:1fr}
    }
  </style>
</head>
<body>
  <div class="frame">
    <header class="topbar">Barangay Health Services</header>

    <div class="container">
      <div>
        <div class="track">
          <h2>Track New Request</h2>
          <div class="muted">Ticket ID</div>
          <form method="POST" action="trackreq.php">
            <div class="input-row">
              <input id="searchInput" type="text" name="ticket_id" placeholder="Enter your ticket ID (e.g., BHR-2024-001234)" required />
              <button type="submit" class="btn">Track Request</button>
            </div>
          </form>

          <?php if ($search_result): ?>
            <div class="search-result">
              <strong>Ticket ID:</strong> <?php echo htmlspecialchars($search_result['ticket_id']); ?><br>
              <strong>Type:</strong> <?php echo htmlspecialchars($search_result['requesttype']); ?><br>
              <strong>Status:</strong> <?php echo htmlspecialchars($search_result['status']); ?><br>
              <strong>Submitted:</strong> <?php echo htmlspecialchars($search_result['submitted_at']); ?>
            </div>
          <?php elseif ($search_error): ?>
            <div class="search-error"><?php echo htmlspecialchars($search_error); ?></div>
          <?php endif; ?>

          <div style="margin-top:14px;display:flex;gap:10px;align-items:center">
            <div class="badge" id="liveBadge">In Ready</div>
            <div class="muted" style="font-size:13px">Enter a ticket ID and click "Track Request" to find its status.</div>
          </div>
        </div>
      </div>

      <aside class="help">
        <div class="icon">?</div>
        <div style="font-weight:700">Need Help?</div>
        <div class="muted">• Lost your ID?<br>• Contact Support</div>
        <a href="#" id="contactLink">Contact Support</a>
      </aside>
    </div>

    <div class="recent">
      <div class="recent-top">
        <div style="font-weight:800">My Recent Requests</div>
        <div class="tabs">
          <div class="tab active" data-filter="all">All</div>
          <div class="tab" data-filter="ready">Ready</div>
          <div class="tab" data-filter="completed">Completed</div>
        </div>
      </div>

      <div class="cards" id="cardsContainer">
        <?php foreach ($recent_requests as $request): ?>
          <div class="card">
            <div class="ticket">
              <span class="dot" style="background:<?php echo statusColor($request['status']); ?>"></span>
              <div style="flex:1"><?php echo htmlspecialchars($request['ticket_id']); ?></div>
              <div class="status"><?php echo htmlspecialchars($request['status']); ?></div>
            </div>
            <div style="font-weight:700;margin-top:6px"><?php echo htmlspecialchars($request['requesttype']); ?></div>
            <div class="meta">Submitted: <?php echo htmlspecialchars(date('Y-m-d', strtotime($request['submitted_at']))); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <script>
    // Tabs functionality
    document.querySelectorAll('.tab').forEach(t => {
      t.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const filter = t.dataset.filter;
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
          const status = card.querySelector('.status').textContent.toLowerCase();
          if (filter === 'all' || status.includes(filter)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });

    // Contact link
    document.getElementById('contactLink').addEventListener('click', (e) => {
      e.preventDefault();
      alert('Contacting support... (this demo does not actually send a message)');
    });
  </script>
</body>
</html>
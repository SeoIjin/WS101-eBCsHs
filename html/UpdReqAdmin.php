<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Update Request - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Quicksand:wght@400;500&display=swap" rel="stylesheet">
<style>
  /* enlarge layout by ~40% */

  html,body{
    height:100vh;               /* lock to viewport */
    margin:0;
    font-family:"Quicksand",system-ui,-apple-system,Arial,sans-serif;
    background:#eaf6e9;
    overflow:hidden;            /* prevent page scrollbar */
    box-sizing:border-box;
  }

  /* make the center wrapper fill viewport without causing page scroll */
  .wrap{
    height:100vh;               /* exact viewport height */
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px;               /* slightly reduced to avoid overflow */
    box-sizing:border-box;
  }

  /* card (scaled up ~40%) */
  .card{
    width:490px;
    background:#ffffff;
    border-radius:17px;
    border:2px solid rgba(19,83,63,0.08);
    box-shadow:0 6px 18px rgba(16,40,28,0.06);
    padding:31px;
    max-height: calc(100vh - 64px); /* ensure the card never exceeds viewport */
    overflow:auto;                   /* internal scroll only if necessary */
  }

  /* header */
  .card-head{
    display:flex;
    align-items:flex-start;
    gap:17px;
    margin-bottom:12px;
  }
  .icon-circle{
    width:62px;
    height:62px;
    border-radius:14px;
    background:#144d3f;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    box-shadow:0 8px 22px rgba(20,77,63,0.12);
  }
  .icon-circle svg{ width:28px; height:28px; display:block; fill:#fff }
  .title-wrap{ flex:1 }
  .title{
    font-family:"Montserrat",sans-serif;
    font-size:25px;
    color:#144d3f;
    margin:0 0 6px 0;
    font-weight:700;
  }
  .subtitle{
    font-size:18px;
    color:#98bfae;
    margin:0;
  }

  /* form */
  form{ margin-top:12px }
  label{
    display:block;
    font-weight:700;
    font-size:18px;
    color:#144d3f;
    margin:20px 0 12px;
  }

  select, input, textarea {
    width:100%;
    background:#edf7ef;
    border:1px solid #cfe6d3;
    border-radius:11px;
    padding:17px 20px;
    font-size:20px;
    color:#123f33;
    outline:none;
    box-sizing:border-box;
  }

  select{
    appearance:none;
    background-image:
      linear-gradient(45deg, transparent 50%, #6d7 50%),
      linear-gradient(135deg, #6d7 50%, transparent 50%);
    background-position: calc(100% - 18px) calc(1em + 2px), calc(100% - 13px) calc(1em + 2px);
    background-size: 8px 8px, 8px 8px;
    background-repeat: no-repeat;
  }

  .select-wrap{ position:relative }
  .select-wrap::after{
    content:"";
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    width:12px; height:12px;
    pointer-events:none;
  }

  textarea{
    min-height:120px;
    max-height: calc(100vh - 360px); /* keep textarea from forcing card overflow */
    resize:vertical;
    padding:17px;
    border-radius:11px;
    font-size:20px;
    line-height:1.5;
    color:#2b4f3f;
  }
  textarea::placeholder{ color:#9bb7a8; font-style:italic }

  /* button (scaled up) */
  .btn{
    display:flex;
    align-items:center;
    gap:14px;
    justify-content:center;
    width:100%;
    margin-top:26px;
    background:#144d3f;
    color:#fff;
    border:0;
    padding:18px 22px;
    border-radius:14px;
    font-weight:800;
    font-size:21px;
    cursor:pointer;
    box-shadow:0 10px 30px rgba(20,77,63,0.14);
  }
  .btn svg{ width:25px; height:25px; fill:#fff; flex-shrink:0 }

  /* responsive tweak */
  @media (max-width:720px){
    .card{
      width:100%;
      padding:22px;
      border-radius:12px;
    }
    .wrap{ padding:20px }
    .title{ font-size:22px }
    label{ font-size:16px }
    select,input,textarea{ font-size:16px; padding:12px }
    .btn{ font-size:18px; padding:14px }
    .icon-circle{ width:52px; height:52px }
    .icon-circle svg{ width:22px; height:22px }
  }
</style>
</head>
<body>
  <div class="wrap">
    <div class="card" role="region" aria-label="Update request">
      <div class="card-head">
        <div class="icon-circle" aria-hidden>
          <!-- edit icon -->
          <svg viewBox="0 0 24 24" aria-hidden focusable="false">
            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
          </svg>
        </div>

        <div class="title-wrap">
          <h3 class="title">Update Request</h3>
          <p class="subtitle">Change status and add comments</p>
        </div>
      </div>

      <form id="updateForm" autocomplete="off">
        <label for="status">New Status</label>
        <div class="select-wrap">
          <select id="status" name="status" aria-label="New status">
            <option>Completed</option>
            <option>Pending</option>
            <option>In progress</option>
            <option>Rejected</option>
          </select>
        </div>

        <label for="message">Update Message</label>
        <textarea id="message" name="message" placeholder="Please provide details about this update, actions taken, or next steps..."></textarea>

        <button class="btn" type="submit" aria-label="Update request">
          <!-- calendar/check icon -->
          <svg viewBox="0 0 24 24" aria-hidden focusable="false">
            <path d="M9 16.2l-3.5-3.5L4 14.2 9 19.2 20 8.2 17.5 5.7z"/>
          </svg>
          Update Request
        </button>
      </form>
    </div>
  </div>

<script>
  // simple submit handler to prevent navigation during testing
  document.getElementById('updateForm').addEventListener('submit', function(e){
    e.preventDefault();
    // placeholder: handle submit
    alert('Update saved (demo)');
  });
</script>
</body>
</html>
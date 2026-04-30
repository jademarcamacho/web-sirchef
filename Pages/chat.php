<?php
session_start();
require_once "db.php";
require_once "helpers.php";
require_login();

$uid = current_user_id();
$selectedId = (int) ($_GET["user_id"] ?? 0);

$peopleSql = "
SELECT
  u.id,
  u.first_name,
  u.last_name,
  u.bio,
  (
    SELECT m.message
    FROM messages m
    WHERE m.group_id IS NULL
      AND ((m.sender_id = $uid AND m.receiver_id = u.id) OR (m.sender_id = u.id AND m.receiver_id = $uid))
    ORDER BY m.created_at DESC
    LIMIT 1
  ) last_message,
  (
    SELECT m.created_at
    FROM messages m
    WHERE m.group_id IS NULL
      AND ((m.sender_id = $uid AND m.receiver_id = u.id) OR (m.sender_id = u.id AND m.receiver_id = $uid))
    ORDER BY m.created_at DESC
    LIMIT 1
  ) last_at
FROM users u
WHERE u.id <> $uid
ORDER BY last_at IS NULL, last_at DESC, u.first_name ASC
LIMIT 40";
$people = $conn->query($peopleSql)->fetch_all(MYSQLI_ASSOC);

if ($selectedId <= 0 && $people) {
    $selectedId = (int) $people[0]["id"];
}

$selectedUser = null;
$messages = [];
if ($selectedId > 0) {
    $stmt = $conn->prepare("SELECT id, first_name, last_name, bio FROM users WHERE id = ? AND id <> ? LIMIT 1");
    $stmt->bind_param("ii", $selectedId, $uid);
    $stmt->execute();
    $selectedUser = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($selectedUser) {
        $stmt = $conn->prepare("
            SELECT m.id, m.sender_id, m.receiver_id, m.message, m.created_at, s.first_name sender_name
            FROM messages m
            JOIN users s ON s.id = m.sender_id
            WHERE m.group_id IS NULL
              AND ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            ORDER BY m.created_at ASC
            LIMIT 120
        ");
        $stmt->bind_param("iiii", $uid, $selectedId, $selectedId, $uid);
        $stmt->execute();
        $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages | SirChef</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/chat.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include "header.php"; ?>
<main class="chat-page">
  <section class="chat-shell">
    <aside class="conversation-list">
      <div class="messages-title">
        <h1>Messages</h1>
        <span>Private chats</span>
      </div>
      <?php foreach ($people as $person): ?>
        <?php $active = (int) $person["id"] === $selectedId; ?>
        <a class="conversation-item <?= $active ? "active" : "" ?>" href="chat.php?user_id=<?= (int) $person["id"] ?>">
          <div class="conversation-avatar"><?= e(strtoupper(substr($person["first_name"], 0, 1))) ?></div>
          <div class="conversation-meta">
            <strong><?= e($person["first_name"] . " " . $person["last_name"]) ?></strong>
            <span><?= e($person["last_message"] ?: ($person["bio"] ?: "Start a private message")) ?></span>
          </div>
          <?php if ($person["last_at"]): ?><time><?= e(date("M j", strtotime($person["last_at"]))) ?></time><?php endif; ?>
        </a>
      <?php endforeach; ?>
      <?php if (!$people): ?>
        <div class="empty-state">No other users yet.</div>
      <?php endif; ?>
    </aside>

    <section class="message-panel">
      <?php if ($selectedUser): ?>
        <header class="message-header">
          <div class="conversation-avatar"><?= e(strtoupper(substr($selectedUser["first_name"], 0, 1))) ?></div>
          <div>
            <h2><?= e($selectedUser["first_name"] . " " . $selectedUser["last_name"]) ?></h2>
            <span><?= e($selectedUser["bio"] ?: "SirChef member") ?></span>
          </div>
          <a href="profile.php?id=<?= (int) $selectedUser["id"] ?>" class="profile-link"><i class="fas fa-user"></i></a>
        </header>

        <div class="message-thread" id="messageThread">
          <?php foreach ($messages as $m): ?>
            <div class="message-bubble <?= (int) $m["sender_id"] === $uid ? "mine" : "theirs" ?>">
              <p><?= e($m["message"]) ?></p>
              <time><?= e(date("M j, g:i A", strtotime($m["created_at"]))) ?></time>
            </div>
          <?php endforeach; ?>
          <?php if (!$messages): ?>
            <div class="empty-thread">No messages yet. Start the conversation privately.</div>
          <?php endif; ?>
        </div>

        <form class="message-form" id="messageForm">
          <input type="hidden" name="action" value="send_private_message">
          <input type="hidden" name="receiver_id" value="<?= (int) $selectedUser["id"] ?>">
          <input name="message" id="messageInput" autocomplete="off" placeholder="Write a private message..." required>
          <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
        <div class="message-status" id="messageStatus"></div>
      <?php else: ?>
        <div class="empty-thread">Choose a person to start a private message.</div>
      <?php endif; ?>
    </section>
  </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const thread=document.getElementById('messageThread');
if(thread)thread.scrollTop=thread.scrollHeight;
function escHtml(value){return String(value).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
const form=document.getElementById('messageForm');
if(form){
  form.addEventListener('submit',e=>{
    e.preventDefault();
    const status=document.getElementById('messageStatus');
    const input=document.getElementById('messageInput');
    const btn=form.querySelector('button');
    if(btn)btn.disabled=true;
    fetch('backend.php',{method:'POST',body:new FormData(form)})
      .then(r=>r.json())
      .then(d=>{
        if(!d.success){
          if(status)status.textContent=d.message||'Message failed.';
          return;
        }
        const empty=thread.querySelector('.empty-thread');
        if(empty)empty.remove();
        const bubble=document.createElement('div');
        bubble.className='message-bubble mine';
        bubble.innerHTML=`<p>${escHtml(d.message_text)}</p><time>${escHtml(d.created_at_label)}</time>`;
        thread.appendChild(bubble);
        thread.scrollTop=thread.scrollHeight;
        input.value='';
        if(status)status.textContent='';
      })
      .catch(()=>{if(status)status.textContent='Message failed. Please try again.';})
      .finally(()=>{if(btn)btn.disabled=false;});
  });
}
</script>
</body>
</html>

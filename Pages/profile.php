<?php
session_start();
require_once "db.php";
require_once "helpers.php";
require_login();

$uid = current_user_id();
$profileId = (int) ($_GET["id"] ?? $uid);
if ($profileId <= 0) {
    $profileId = $uid;
}
$isOwnProfile = $profileId === $uid;

$stmt = $conn->prepare("SELECT id, first_name, last_name, email, bio, profile_photo, created_at FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    die("Profile not found.");
}

$stmt = $conn->prepare("SELECT COUNT(*) total FROM follows WHERE following_id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$followersCount = (int) ($stmt->get_result()->fetch_assoc()["total"] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) total FROM follows WHERE follower_id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$followingCount = (int) ($stmt->get_result()->fetch_assoc()["total"] ?? 0);
$stmt->close();

$isFollowing = false;
if (!$isOwnProfile) {
    $stmt = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ? LIMIT 1");
    $stmt->bind_param("ii", $uid, $profileId);
    $stmt->execute();
    $stmt->store_result();
    $isFollowing = $stmt->num_rows > 0;
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$fullName = trim($user["first_name"] . " " . $user["last_name"]);
$pageTitle = $isOwnProfile ? "My Profile" : $fullName . " | SirChef";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/profile.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include "header.php"; ?>
<main class="profile-page">
  <section class="profile-hero">
    <div class="profile-avatar"><?= e(strtoupper(substr($user["first_name"], 0, 1))) ?></div>
    <h1><?= e($fullName) ?></h1>
    <p><?= e($user["bio"] ?: "SirChef home cook") ?></p>
    <div class="profile-stats-row">
      <div class="profile-stat"><strong id="followersCount"><?= $followersCount ?></strong><span>Followers</span></div>
      <div class="profile-stat"><strong id="followingCount"><?= $followingCount ?></strong><span>Following</span></div>
    </div>
    <?php if (!$isOwnProfile): ?>
      <div class="profile-actions">
        <button
          class="profile-follow-btn <?= $isFollowing ? "following" : "" ?>"
          id="profileFollowBtn"
          data-id="<?= (int) $profileId ?>">
          <i class="fas <?= $isFollowing ? "fa-user-check" : "fa-user-plus" ?>"></i>
          <span><?= $isFollowing ? "Following" : "Follow" ?></span>
        </button>
        <a class="profile-message-btn" href="chat.php?user_id=<?= (int) $profileId ?>">
          <i class="fas fa-message"></i>
          <span>Message</span>
        </a>
      </div>
      <div class="profile-follow-msg" id="profileFollowMsg"></div>
    <?php endif; ?>
  </section>

  <section class="container py-4">
    <h2><?= $isOwnProfile ? "My Recipes" : e($user["first_name"]) . "'s Recipes" ?></h2>
    <div class="row g-4">
      <?php foreach ($recipes as $r): ?>
        <div class="col-md-4">
          <a class="profile-recipe" target="_blank" href="recipe_detail.php?id=<?= (int) $r["id"] ?>">
            <img src="<?= e($r["image"]) ?>" alt="<?= e($r["title"]) ?>">
            <strong><?= e($r["title"]) ?></strong>
            <span><?= e($r["cuisine"]) ?> &middot; <?= e($r["difficulty"]) ?></span>
          </a>
        </div>
      <?php endforeach; ?>
      <?php if (!$recipes): ?>
        <div class="col-12">
          <div class="empty-state"><?= $isOwnProfile ? "You have not shared recipes yet." : e($user["first_name"]) . " has not shared recipes yet." ?></div>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const followBtn=document.getElementById('profileFollowBtn');
const followMsg=document.getElementById('profileFollowMsg');
function profileAjax(fd){
  return fetch('backend.php',{method:'POST',body:fd})
    .then(r=>r.json())
    .catch(()=>({success:false,message:'Unable to update follow status.'}));
}
if(followBtn){
  followBtn.addEventListener('click',()=>{
    const fd=new FormData();
    fd.append('action','follow');
    fd.append('following_id',followBtn.dataset.id);
    followBtn.disabled=true;
    profileAjax(fd).then(d=>{
      if(d.success){
        followBtn.classList.toggle('following',d.active);
        followBtn.querySelector('i').className=d.active?'fas fa-user-check':'fas fa-user-plus';
        followBtn.querySelector('span').textContent=d.active?'Following':'Follow';
        if(Number.isInteger(d.followers_count)){
          document.getElementById('followersCount').textContent=d.followers_count;
        }
      } else if(followMsg) {
        followMsg.textContent=d.message||'Unable to update follow status.';
      }
      followBtn.disabled=false;
    });
  });
}
</script>
</body>
</html>

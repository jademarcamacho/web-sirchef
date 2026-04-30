<?php
session_start();
require_once "db.php";
require_once "helpers.php";
require_login();

$uid = current_user_id();
$userName = $_SESSION["user_name"] ?? "Chef";
function dashboard_ini_bytes(string $value): int {
    $value = trim($value);
    if ($value === "") {
        return 0;
    }
    $unit = strtolower($value[strlen($value) - 1]);
    $bytes = (float) $value;
    if ($unit === "g") {
        $bytes *= 1024;
    }
    if ($unit === "g" || $unit === "m") {
        $bytes *= 1024;
    }
    if ($unit === "g" || $unit === "m" || $unit === "k") {
        $bytes *= 1024;
    }
    return (int) $bytes;
}
function dashboard_human_bytes(int $bytes): string {
    return $bytes >= 1024 * 1024 ? round($bytes / 1024 / 1024) . "MB" : round($bytes / 1024) . "KB";
}
$uploadLimits = array_filter([
    dashboard_ini_bytes((string) ini_get("post_max_size")),
    dashboard_ini_bytes((string) ini_get("upload_max_filesize")),
    50 * 1024 * 1024
]);
$shareUploadLimitBytes = min($uploadLimits);
$shareUploadLimitLabel = dashboard_human_bytes($shareUploadLimitBytes);

$feedSql = "
SELECT 'recipe' item_type, r.id, r.title, r.description content, r.image, 'image' media_type, r.duration_minutes, r.difficulty, r.cuisine, r.created_at, u.id author_id, COALESCE(u.first_name,'SirChef') first_name, COALESCE(u.last_name,'') last_name
FROM recipes r
LEFT JOIN users u ON u.id=r.user_id
WHERE r.status='published'
UNION ALL
SELECT 'post', p.id, 'Kitchen Thought', p.content, pm.media_path, pm.media_type, NULL, NULL, NULL, p.created_at, u.id, u.first_name, u.last_name
FROM user_posts p
JOIN users u ON u.id=p.user_id
LEFT JOIN post_media pm ON pm.post_id=p.id
ORDER BY created_at DESC
LIMIT 20";
$feed = $conn->query($feedSql)->fetch_all(MYSQLI_ASSOC);
$followersCount = (int) ($conn->query("SELECT COUNT(*) total FROM follows WHERE following_id=$uid")->fetch_assoc()["total"] ?? 0);
$followingCount = (int) ($conn->query("SELECT COUNT(*) total FROM follows WHERE follower_id=$uid")->fetch_assoc()["total"] ?? 0);
$favorites = $conn->query("SELECT r.* FROM favorites f JOIN recipes r ON r.id=f.recipe_id WHERE f.user_id=$uid ORDER BY f.created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
$suggested = $conn->query("SELECT u.id, u.first_name, u.last_name, u.bio, EXISTS(SELECT 1 FROM follows f WHERE f.follower_id=$uid AND f.following_id=u.id) is_following FROM users u WHERE u.id <> $uid ORDER BY u.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$searched = $conn->query("SELECT * FROM recipes WHERE status='published' ORDER BY search_count DESC, created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$favorited = $conn->query("SELECT r.*, COUNT(f.id) total FROM recipes r LEFT JOIN favorites f ON f.recipe_id=r.id WHERE r.status='published' GROUP BY r.id ORDER BY total DESC, r.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$liked = $conn->query("SELECT r.*, COUNT(l.id) total FROM recipes r LEFT JOIN likes l ON l.recipe_id=r.id WHERE r.status='published' GROUP BY r.id ORDER BY total DESC, r.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | SirChef</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/dashboard.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body class="dash-body">
<?php include "dashboard_header.php"; ?>

<nav class="mobile-dash-tabs" aria-label="Dashboard shortcuts">
  <a href="#feed"><i class="fas fa-house"></i><span>Feed</span></a>
  <a href="#mobileKitchen"><i class="fas fa-basket-shopping"></i><span>Kitchen</span></a>
  <a href="#mobileTrending"><i class="fas fa-fire"></i><span>Trending</span></a>
  <a href="#mobileProfile"><i class="fas fa-user"></i><span>Profile</span></a>
</nav>

<main class="dash-grid">
  <aside class="dash-panel left-panel" id="mobileProfile">
    <div class="profile-card compact">
      <div class="profile-avatar-lg"><?= e(strtoupper(substr($userName,0,1))) ?></div>
      <h2><?= e($userName) ?></h2>
      <p>@<?= e(strtolower(preg_replace('/\s+/', '', $userName))) ?></p>
      <div class="profile-stats compact-stats">
        <div class="pstat"><span class="pstat-num"><?= $followersCount ?></span><span class="pstat-lbl">Followers</span></div>
        <div class="pstat"><span class="pstat-num"><?= $followingCount ?></span><span class="pstat-lbl">Following</span></div>
      </div>
    </div>
    <button class="btn-share w-100" data-bs-toggle="modal" data-bs-target="#shareModal"><i class="fas fa-plus"></i> Share</button>
    <a class="side-link" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
    <a class="side-link" href="chat.php"><i class="fas fa-message"></i> Messages</a>
    <a class="side-link" href="settings.php"><i class="fas fa-gear"></i> Account Settings</a>
    <section class="left-favorites-panel">
      <h3><i class="fas fa-star"></i> Favorites</h3>
      <?php foreach($favorites as $r): ?>
        <a class="mini-recipe" target="_blank" href="recipe_detail.php?id=<?= (int)$r["id"] ?>"><img src="<?= e($r["image"]) ?>"><span><?= e($r["title"]) ?></span></a>
      <?php endforeach; ?>
      <?php if(!$favorites): ?><p class="muted">No favorites yet.</p><?php endif; ?>
    </section>
  </aside>

  <section class="feed-scroll" id="feed">
    <div class="composer-mini fb-composer">
      <div class="composer-top">
        <div class="composer-avatar"><?= e(strtoupper(substr($userName,0,1))) ?></div>
        <button class="composer-prompt" type="button" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-target="#thoughtPane">What's cooking, <?= e($userName) ?>?</button>
      </div>
      <div class="composer-actions">
        <button class="composer-action photo" type="button" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-target="#thoughtPane"><i class="fas fa-image"></i><span>Photo</span></button>
        <button class="composer-action video" type="button" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-target="#thoughtPane"><i class="fas fa-video"></i><span>Video</span></button>
        <button class="composer-action recipe" type="button" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-target="#recipePane"><i class="fas fa-utensils"></i><span>Recipe</span></button>
      </div>
    </div>
    <section class="kitchen-feed-results" id="kitchenFeedResults" hidden>
      <div class="kitchen-feed-head">
        <div>
          <span class="section-kicker"><i class="fas fa-basket-shopping"></i> Kitchen Matches</span>
          <h2>Recipe Results</h2>
        </div>
        <button class="post-action" id="clearKitchenResults" type="button"><i class="fas fa-xmark"></i> Clear</button>
      </div>
      <div class="match-results" id="kitchenFeedResultsList"></div>
    </section>
    <?php foreach ($feed as $item): ?>
      <article class="feed-post" data-title="<?= e(strtolower($item["title"])) ?>">
        <div class="post-header"><a class="post-av" href="<?= $item["author_id"] ? "profile.php?id=".(int)$item["author_id"] : "profile.php" ?>"><?= e(strtoupper(substr($item["first_name"],0,1))) ?></a><div class="post-meta"><a class="post-author" href="<?= $item["author_id"] ? "profile.php?id=".(int)$item["author_id"] : "profile.php" ?>"><?= e(trim($item["first_name"]." ".$item["last_name"])) ?></a><div class="post-time"><?= e(date("M j, Y", strtotime($item["created_at"]))) ?><?= $item["item_type"]==="recipe" ? " · ".(int)$item["duration_minutes"]." mins · ".e($item["difficulty"])." · ".e($item["cuisine"]) : "" ?></div></div><span class="post-badge"><?= e($item["item_type"]) ?></span></div>
        <div class="post-body"><div class="post-recipe-title"><?= e($item["title"]) ?></div><div class="post-caption"><?= e($item["content"]) ?></div></div>
        <?php if ($item["image"]): ?>
          <div class="post-image-wrap">
            <?php if (($item["media_type"] ?? "image") === "video"): ?>
              <?php
                $videoExt = strtolower(pathinfo(parse_url($item["image"], PHP_URL_PATH) ?? "", PATHINFO_EXTENSION));
                $videoType = $videoExt === "webm" ? "video/webm" : ($videoExt === "mov" ? "video/quicktime" : "video/mp4");
              ?>
              <video class="post-image-real post-video" controls preload="metadata">
                <source src="<?= e($item["image"]) ?>" type="<?= e($videoType) ?>">
              </video>
            <?php else: ?>
              <img class="post-image-real" src="<?= e($item["image"]) ?>" alt="<?= e($item["title"]) ?>">
            <?php endif; ?>
            <?php if ($item["item_type"]==="recipe"): ?>
              <span class="post-image-tag"><?= e($item["cuisine"]) ?></span>
              <span class="post-image-pill"><span class="post-image-pill-dot"></span><?= (int)$item["duration_minutes"] ?> mins &middot; <?= e($item["difficulty"]) ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <div class="post-footer">
          <button class="post-action ajax-action" data-action="like" data-type="<?= $item["item_type"] ?>" data-id="<?= (int)$item["id"] ?>"><i class="far fa-heart"></i> Like</button>
          <?php if ($item["item_type"]==="recipe"): ?>
            <button class="post-action ajax-action" data-action="favorite" data-type="recipe" data-id="<?= (int)$item["id"] ?>"><i class="far fa-star"></i> Favorite</button>
            <a class="post-action" target="_blank" href="recipe_detail.php?id=<?= (int)$item["id"] ?>"><i class="fas fa-up-right-from-square"></i> Open</a>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <aside class="right-scroll">
    <section class="dash-panel kitchen-panel kitchen-search-card dashboard-kitchen-card" id="mobileKitchen">
      <div class="ksc-top-bar"></div>
      <span class="ksc-float ksc-f1"><i class="fas fa-carrot"></i></span>
      <span class="ksc-float ksc-f2"><i class="fas fa-pepper-hot"></i></span>
      <span class="ksc-float ksc-f3"><i class="fas fa-egg"></i></span>
      <span class="ksc-float ksc-f4"><i class="fas fa-lemon"></i></span>

      <div class="ksc-header">
        <div class="ksc-icon-ring">
          <i class="fas fa-basket-shopping"></i>
        </div>
        <h3 class="ksc-title">What's in Your Kitchen?</h3>
        <p class="ksc-subtitle">Add your ingredients and we'll find matching recipes instantly.</p>
      </div>

      <div class="ksc-input-row">
        <div class="ksc-input-wrap">
          <i class="fas fa-search ksc-search-icon"></i>
          <input class="ksc-input" id="kitchenInput" placeholder="e.g. chicken, garlic, tomato...">
        </div>
        <button class="ksc-add-btn" id="kitchenAdd" type="button">
          <i class="fas fa-plus"></i> Add
        </button>
      </div>

      <div id="kitchenChips" class="ksc-tags-area">
        <span class="ksc-tags-empty">Your ingredients will appear here&hellip;</span>
      </div>

      <button class="ksc-find-btn" id="kitchenFind" type="button">
        <i class="fas fa-utensils"></i>
        <span>Find Recipes</span>
        <i class="fas fa-arrow-right ksc-arrow"></i>
      </button>

      <div id="kitchenResults" class="match-results"></div>

      <p class="ksc-note">
        <i class="fas fa-circle-info"></i>
        Dashboard searches can use all ingredients you add.
      </p>
    </section>

    <section class="dash-panel" id="mobileTrending"><h3><i class="fas fa-fire"></i> Trending</h3><div class="trend-tabs"><button data-tab="searched" class="active">Searched</button><button data-tab="favorited">Favorited</button><button data-tab="liked">Liked</button></div><?php $sets=["searched"=>$searched,"favorited"=>$favorited,"liked"=>$liked]; foreach($sets as $key=>$set): ?><div class="trend-set <?= $key==='searched'?'active':'' ?>" id="trend-<?= $key ?>"><?php foreach($set as $r): ?><a class="mini-recipe" target="_blank" href="recipe_detail.php?id=<?= (int)$r["id"] ?>"><img src="<?= e($r["image"]) ?>"><span><?= e($r["title"]) ?></span></a><?php endforeach; ?></div><?php endforeach; ?></section>
    <section class="dash-panel"><h3><i class="fas fa-user-plus"></i> Chefs to Follow</h3><?php foreach($suggested as $u): ?><div class="suggest-item"><a class="suggest-av" href="profile.php?id=<?= (int)$u["id"] ?>"><?= e(strtoupper(substr($u["first_name"],0,1))) ?></a><div class="suggest-info"><a class="suggest-name" href="profile.php?id=<?= (int)$u["id"] ?>"><?= e($u["first_name"]." ".$u["last_name"]) ?></a><div class="suggest-tag"><?= e($u["bio"] ?: "Home cook") ?></div></div><button class="btn-follow ajax-follow <?= (int)$u["is_following"] ? "following" : "" ?>" data-id="<?= (int)$u["id"] ?>"><?= (int)$u["is_following"] ? "Following" : "Follow" ?></button></div><?php endforeach; ?></section>
  </aside>
</main>

<div class="modal fade" id="shareModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content share-modal">
      <div class="modal-frame">
        <div class="modal-frame-dots"><span></span><span></span><span></span></div>
        <div class="modal-box">
          <div class="modal-header">
            <h2><i class="fas fa-paper-plane"></i> Share to SirChef</h2>
            <button class="modal-close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-xmark"></i></button>
          </div>
          <div class="modal-tabs">
            <button class="modal-tab active" type="button" data-bs-toggle="pill" data-bs-target="#thoughtPane" aria-selected="true">Thought/Post</button>
            <button class="modal-tab" type="button" data-bs-toggle="pill" data-bs-target="#recipePane" aria-selected="false">Recipe</button>
          </div>
          <div class="modal-body">
            <div class="tab-content">
              <form class="tab-pane fade show active" id="thoughtPane" enctype="multipart/form-data">
                <input type="hidden" name="action" value="share_post">
                <div class="post-create-head">
                  <div class="composer-avatar"><?= e(strtoupper(substr($userName,0,1))) ?></div>
                  <div>
                    <strong><?= e($userName) ?></strong>
                    <span>Share with the SirChef feed</span>
                  </div>
                </div>
                <textarea id="postContent" name="content" class="thought-textarea" rows="5" placeholder="What's cooking, <?= e($userName) ?>?"></textarea>
                <input id="postMediaInput" type="file" name="media" class="visually-hidden" accept="image/*,video/mp4,video/webm,video/quicktime,video/x-m4v">
                <div class="post-upload-actions">
                  <label class="post-upload-chip photo" for="postMediaInput" data-post-accept="image/*"><i class="fas fa-image"></i><span>Photo</span></label>
                  <label class="post-upload-chip video" for="postMediaInput" data-post-accept="video/mp4,video/webm,video/quicktime,video/x-m4v"><i class="fas fa-video"></i><span>Video</span></label>
                </div>
                <div id="postMediaPreview" class="post-media-preview" hidden></div>
                <div class="modal-footer">
                  <button class="btn-share" type="submit"><i class="fas fa-paper-plane"></i> Share Post</button>
                </div>
              </form>
              <form class="tab-pane fade" id="recipePane" enctype="multipart/form-data">
                <input type="hidden" name="action" value="share_recipe">
                <div class="modal-grid-3">
                  <div class="modal-field-group">
                    <label class="modal-field-label" for="recipeTitle">Recipe</label>
                    <input id="recipeTitle" class="modal-input" name="title" placeholder="Recipe title" required>
                  </div>
                  <div class="modal-field-group">
                    <label class="modal-field-label" for="recipeDuration">Mins</label>
                    <input id="recipeDuration" class="modal-input" name="duration" type="number" min="1" placeholder="30" required>
                  </div>
                  <div class="modal-field-group">
                    <label class="modal-field-label" for="recipeDifficulty">Level</label>
                    <select id="recipeDifficulty" class="modal-select" name="difficulty" required>
                      <option value="">Pick</option>
                      <option>Easy</option>
                      <option>Medium</option>
                      <option>Hard</option>
                    </select>
                  </div>
                </div>
                <div class="modal-grid-2">
                  <div class="modal-field-group">
                    <label class="modal-field-label" for="recipeCuisine">Cuisine</label>
                    <input id="recipeCuisine" class="modal-input" name="cuisine" placeholder="Cuisine type" required>
                  </div>
                  <div class="modal-field-group">
                    <label class="modal-field-label" for="recipeTutorial">Tutorial</label>
                    <input id="recipeTutorial" class="modal-input" name="youtube_url" placeholder="YouTube/tutorial link">
                  </div>
                </div>
                <label class="modal-field-label" for="recipeIngredients">Ingredients</label>
                <textarea id="recipeIngredients" class="modal-textarea" name="ingredients" rows="3" placeholder="Ingredients separated by comma or new line" required></textarea>
                <label class="modal-field-label" for="recipeInstructions">Instructions</label>
                <textarea id="recipeInstructions" class="modal-textarea" name="instructions" rows="4" placeholder="Instructions" required></textarea>
                <input id="recipeMediaInput" type="file" name="media" class="visually-hidden" accept="image/*">
                <div class="post-upload-actions recipe-upload-actions">
                  <label class="post-upload-chip photo" for="recipeMediaInput"><i class="fas fa-image"></i><span>Photo</span></label>
                </div>
                <div id="recipeMediaPreview" class="post-media-preview recipe-media-preview" hidden></div>
                <div class="modal-footer">
                  <button class="btn-share" type="submit"><i class="fas fa-utensils"></i> Share Recipe</button>
                </div>
              </form>
            </div>
            <div id="shareMsg" class="small mt-3"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const SHARE_UPLOAD_LIMIT=<?= (int) $shareUploadLimitBytes ?>;
const SHARE_UPLOAD_LIMIT_LABEL=<?= json_encode($shareUploadLimitLabel) ?>;
const modeToggle=document.getElementById('modeToggle');
const modeIcon=modeToggle?modeToggle.querySelector('i'):null;
function storageGet(key){try{return localStorage.getItem(key);}catch(e){return null;}}
function storageSet(key,value){try{localStorage.setItem(key,value);}catch(e){}}
function setDashboardMode(isDark){
  document.body.classList.toggle('soft-dark',isDark);
  if(modeIcon)modeIcon.className=isDark?'fas fa-sun':'fas fa-moon';
  if(modeToggle)modeToggle.setAttribute('aria-label',isDark?'Switch to light mode':'Switch to dark mode');
  storageSet('sirchef-dashboard-mode',isDark?'dark':'light');
}
setDashboardMode(storageGet('sirchef-dashboard-mode')==='dark');
if(modeToggle)modeToggle.addEventListener('click',()=>setDashboardMode(!document.body.classList.contains('soft-dark')));
document.getElementById('navSearchInput').addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('.feed-post').forEach(p=>p.style.display=p.dataset.title.includes(q)?'':'none');});
function ajax(fd){
  return fetch('backend.php',{method:'POST',body:fd})
    .then(r=>r.text())
    .then(text=>{
      try{return JSON.parse(text);}
      catch(e){
        const start=text.indexOf('{');
        const end=text.lastIndexOf('}');
        if(start!==-1&&end>start){
          try{return JSON.parse(text.slice(start,end+1));}catch(ignore){}
        }
        const plain=text.replace(/<[^>]*>/g,' ').replace(/\s+/g,' ').trim();
        const tooLarge=plain.match(/exceeds the limit of\s+([0-9A-Za-z.]+)/i);
        if(tooLarge)return {success:false,message:`That video is too large for the current server limit (${tooLarge[1]}).`};
        return {success:false,message:plain?plain.slice(0,220):'Request failed. Please refresh and try again.'};
      }
    })
    .catch(()=>({success:false,message:'Network error. Please try again.'}));
}
document.querySelectorAll('.ajax-action').forEach(btn=>btn.addEventListener('click',()=>{const fd=new FormData();fd.append('action',btn.dataset.action);fd.append('target_type',btn.dataset.type==='post'?'post':'recipe');fd.append('target_id',btn.dataset.id);ajax(fd).then(d=>{if(d.success){btn.classList.toggle('active',d.active);btn.querySelector('i').className=d.active?btn.querySelector('i').className.replace('far','fas'):btn.querySelector('i').className.replace('fas','far');}});}));
document.querySelectorAll('.ajax-follow').forEach(btn=>btn.addEventListener('click',()=>{const fd=new FormData();fd.append('action','follow');fd.append('following_id',btn.dataset.id);ajax(fd).then(d=>{if(d.success){btn.textContent=d.active?'Following':'Follow';btn.classList.toggle('following',d.active);}});}));
function activateShareTab(target){
  const modal=document.getElementById('shareModal');
  if(!target||!modal)return;
  modal.querySelectorAll('.modal-tab').forEach(btn=>{
    const active=btn.getAttribute('data-bs-target')===target;
    btn.classList.toggle('active',active);
    btn.setAttribute('aria-selected',active?'true':'false');
  });
  modal.querySelectorAll('.tab-pane').forEach(pane=>{
    const active='#'+pane.id===target;
    pane.classList.toggle('active',active);
    pane.classList.toggle('show',active);
  });
}
document.querySelectorAll('#shareModal .modal-tab').forEach(tabBtn=>tabBtn.addEventListener('click',()=>activateShareTab(tabBtn.getAttribute('data-bs-target'))));
document.querySelectorAll('[data-share-target]').forEach(btn=>btn.addEventListener('click',()=>activateShareTab(btn.dataset.shareTarget)));
const postMediaInput=document.getElementById('postMediaInput');
const postMediaPreview=document.getElementById('postMediaPreview');
const recipeMediaInput=document.getElementById('recipeMediaInput');
const recipeMediaPreview=document.getElementById('recipeMediaPreview');
let postPreviewUrl='';
let recipePreviewUrl='';
function escHtml(value){return String(value).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
function clearPostMedia(){
  if(!postMediaInput||!postMediaPreview)return;
  postMediaInput.value='';
  if(postPreviewUrl){URL.revokeObjectURL(postPreviewUrl);postPreviewUrl='';}
  postMediaPreview.hidden=true;
  postMediaPreview.innerHTML='';
}
function clearRecipeMedia(){
  if(!recipeMediaInput||!recipeMediaPreview)return;
  recipeMediaInput.value='';
  if(recipePreviewUrl){URL.revokeObjectURL(recipePreviewUrl);recipePreviewUrl='';}
  recipeMediaPreview.hidden=true;
  recipeMediaPreview.innerHTML='';
}
document.querySelectorAll('[data-post-accept]').forEach(label=>label.addEventListener('click',()=>{if(postMediaInput)postMediaInput.accept=label.dataset.postAccept;}));
if(postMediaInput){
  postMediaInput.addEventListener('change',()=>{
    if(postPreviewUrl){URL.revokeObjectURL(postPreviewUrl);postPreviewUrl='';}
    if(!postMediaPreview)return;
    const file=postMediaInput.files&&postMediaInput.files[0];
    if(!file){postMediaPreview.hidden=true;postMediaPreview.innerHTML='';return;}
    if(file.size>SHARE_UPLOAD_LIMIT){
      postMediaPreview.hidden=false;
      postMediaPreview.innerHTML=`<div class="post-media-error"><i class="fas fa-circle-exclamation"></i><span>${escHtml(file.name)} is too large. Choose a video under ${SHARE_UPLOAD_LIMIT_LABEL}.</span></div>`;
      postMediaInput.value='';
      return;
    }
    postPreviewUrl=URL.createObjectURL(file);
    const isVideo=file.type.startsWith('video/')||/\.(mp4|m4v|mov|webm)$/i.test(file.name);
    const media=isVideo?`<video controls preload="metadata" src="${postPreviewUrl}"></video>`:`<img src="${postPreviewUrl}" alt="">`;
    postMediaPreview.hidden=false;
    postMediaPreview.innerHTML=`${media}<div class="post-media-name"><i class="fas ${isVideo?'fa-video':'fa-image'}"></i><span>${escHtml(file.name)}</span><button type="button" id="clearPostMedia"><i class="fas fa-xmark"></i></button></div>`;
    document.getElementById('clearPostMedia').addEventListener('click',clearPostMedia);
  });
}
if(recipeMediaInput){
  recipeMediaInput.addEventListener('change',()=>{
    if(recipePreviewUrl){URL.revokeObjectURL(recipePreviewUrl);recipePreviewUrl='';}
    if(!recipeMediaPreview)return;
    const file=recipeMediaInput.files&&recipeMediaInput.files[0];
    if(!file){recipeMediaPreview.hidden=true;recipeMediaPreview.innerHTML='';return;}
    if(file.size>SHARE_UPLOAD_LIMIT){
      recipeMediaPreview.hidden=false;
      recipeMediaPreview.innerHTML=`<div class="post-media-error"><i class="fas fa-circle-exclamation"></i><span>${escHtml(file.name)} is too large. Choose a photo under ${SHARE_UPLOAD_LIMIT_LABEL}.</span></div>`;
      recipeMediaInput.value='';
      return;
    }
    recipePreviewUrl=URL.createObjectURL(file);
    recipeMediaPreview.hidden=false;
    recipeMediaPreview.innerHTML=`<img src="${recipePreviewUrl}" alt=""><div class="post-media-name"><i class="fas fa-image"></i><span>${escHtml(file.name)}</span><button type="button" id="clearRecipeMedia"><i class="fas fa-xmark"></i></button></div>`;
    document.getElementById('clearRecipeMedia').addEventListener('click',clearRecipeMedia);
  });
}
document.querySelectorAll('#thoughtPane,#recipePane').forEach(form=>form.addEventListener('submit',e=>{e.preventDefault();const msg=document.getElementById('shareMsg');const btn=form.querySelector('button[type="submit"]');const media=form.querySelector('input[type="file"]');const file=media&&media.files&&media.files[0];if(file&&file.size>SHARE_UPLOAD_LIMIT){if(msg)msg.textContent=`${file.name} is too large. Choose a file under ${SHARE_UPLOAD_LIMIT_LABEL}.`;return;}if(btn)btn.disabled=true;if(msg)msg.textContent='Sharing...';ajax(new FormData(form)).then(d=>{if(msg)msg.textContent=d.message||'';if(d.success){setTimeout(()=>location.reload(),800);return;}if(btn)btn.disabled=false;});}));
document.querySelectorAll('.trend-tabs button').forEach(b=>b.addEventListener('click',()=>{document.querySelectorAll('.trend-tabs button').forEach(x=>x.classList.remove('active'));b.classList.add('active');document.querySelectorAll('.trend-set').forEach(s=>s.classList.remove('active'));document.getElementById('trend-'+b.dataset.tab).classList.add('active');}));
let kitchen=[];
const chipBox=document.getElementById('kitchenChips');
const kitchenInput=document.getElementById('kitchenInput');
const kitchenFind=document.getElementById('kitchenFind');
const kitchenSidebarStatus=document.getElementById('kitchenResults');
const kitchenFeedResults=document.getElementById('kitchenFeedResults');
const kitchenFeedResultsList=document.getElementById('kitchenFeedResultsList');
const clearKitchenResults=document.getElementById('clearKitchenResults');
/*
let kitchen=[];const chipBox=document.getElementById('kitchenChips');function renderKitchen(){chipBox.innerHTML=kitchen.map(i=>`<span class="ingredient-tag">${i}<button onclick="kitchen=kitchen.filter(x=>x!=='${i}');renderKitchen()">×</button></span>`).join('');}
*/
function addKitchenIngredient(){
  const item=kitchenInput.value.trim().toLowerCase();
  if(item&&!kitchen.includes(item))kitchen.push(item);
  kitchenInput.value='';
  renderKitchen();
  kitchenInput.focus();
}
function renderKitchen(){
  if(!kitchen.length){
    chipBox.innerHTML='<span class="ksc-tags-empty">Your ingredients will appear here&hellip;</span>';
    return;
  }
  chipBox.innerHTML=kitchen.map((item,index)=>`<span class="ingredient-tag">${escHtml(item)}<button type="button" data-index="${index}">&times;</button></span>`).join('')+`<button type="button" class="ksc-clear-btn" id="clearKitchenIngredients"><i class="fas fa-basket-shopping"></i><span>Clear all</span></button>`;
  chipBox.querySelectorAll('button[data-index]').forEach(btn=>btn.addEventListener('click',()=>{kitchen=kitchen.filter((_,i)=>i!==Number(btn.dataset.index));renderKitchen();}));
  document.getElementById('clearKitchenIngredients').addEventListener('click',clearKitchenIngredients);
}
function clearKitchenIngredients(){
  kitchen=[];
  kitchenInput.value='';
  kitchenSidebarStatus.innerHTML='';
  kitchenFeedResults.hidden=true;
  kitchenFeedResultsList.innerHTML='';
  renderKitchen();
  kitchenInput.focus();
}
function renderKitchenMatches(data){
  if(!data.success){
    kitchenSidebarStatus.innerHTML=`<div class="match-empty">${escHtml(data.message||'Add at least one ingredient.')}</div>`;
    kitchenFeedResults.hidden=true;
    return;
  }
  const html=data.recipes.length?data.recipes.map(r=>{
    const matched=Number(r.matched_count)||0;
    const searched=Number(r.searched_count)||Math.max(1,kitchen.length);
    const pct=Math.round(Number(r.match_score??(matched/Math.max(1,searched)))*100);
    return `<a class="match-card" target="_blank" href="recipe_detail.php?id=${Number(r.id)||0}">
      <img src="${escHtml(r.image||'')}" alt="${escHtml(r.title||'Recipe')}">
      <div class="match-info">
        <div class="match-topline"><strong>${escHtml(r.title||'Recipe')}</strong><span>${pct}% match</span></div>
        <p>${escHtml(r.description||'Open the full recipe details.')}</p>
        <div class="match-meta">
          <span><i class="fas fa-clock"></i> ${Number(r.duration_minutes)||0} min</span>
          <span><i class="fas fa-signal"></i> ${escHtml(r.difficulty||'')}</span>
          <span><i class="fas fa-globe"></i> ${escHtml(r.cuisine||'')}</span>
        </div>
        <div class="match-bar"><i style="width:${Math.min(100,pct)}%"></i></div>
        <small>${matched}/${searched} searched ingredients matched</small>
      </div>
    </a>`;
  }).join(''):'<div class="match-empty">No close recipe matches yet.</div>';
  kitchenFeedResultsList.innerHTML=html;
  kitchenFeedResults.hidden=false;
  kitchenSidebarStatus.innerHTML='<div class="match-empty">Results are shown in the news feed.</div>';
  document.getElementById('feed').scrollTo({top:kitchenFeedResults.offsetTop-8,behavior:'smooth'});
}
clearKitchenResults.addEventListener('click',()=>{
  kitchenFeedResults.hidden=true;
  kitchenFeedResultsList.innerHTML='';
  kitchenSidebarStatus.innerHTML='';
});
document.getElementById('kitchenAdd').addEventListener('click',addKitchenIngredient);
kitchenInput.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();addKitchenIngredient();}});
kitchenFind.addEventListener('click',()=>{
  const pending=kitchenInput.value.trim().toLowerCase();
  if(pending&&!kitchen.includes(pending))addKitchenIngredient();
  kitchenFind.classList.add('ksc-loading');
  const fd=new FormData();
  fd.append('action','ingredient_search');
  fd.append('ingredients',kitchen.join(','));
  fd.append('search_scope','dashboard');
  ajax(fd).then(d=>{kitchenFind.classList.remove('ksc-loading');renderKitchenMatches(d);});
});
document.querySelectorAll('.left-panel,.feed-scroll,.right-scroll').forEach(scroller=>{
  let scrollTimer=null;
  scroller.addEventListener('scroll',()=>{
    scroller.classList.add('is-scrolling');
    clearTimeout(scrollTimer);
    scrollTimer=setTimeout(()=>scroller.classList.remove('is-scrolling'),900);
  },{passive:true});
});
</script>
</body>
</html>

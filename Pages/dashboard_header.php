<?php
$dashboardUserName = $userName ?? ($_SESSION["user_name"] ?? "Chef");
if (trim($dashboardUserName) === "") {
    $dashboardUserName = "Chef";
}
$dashboardUserInitial = strtoupper(substr($dashboardUserName, 0, 1));
?>
<header class="bohemian-header dashboard-header">
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
      <a class="navbar-brand brand-animated" href="dashboard.php">
        <span class="brand-icon"><i class="fas fa-mortar-pestle"></i></span>
        <span class="brand-text">SirChef</span>
      </a>

      <div class="nav-search mx-3">
        <i class="fas fa-bowl-food ns-icon"></i>
        <input type="text" id="navSearchInput" placeholder="Search recipes..." autocomplete="off">
      </div>

      <button class="navbar-toggler border-0" type="button"
        data-bs-toggle="collapse" data-bs-target="#dashNavbar"
        aria-controls="dashNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span style="color:white;font-size:1.4rem;"><i class="fas fa-bars"></i></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="dashNavbar">
        <ul class="navbar-nav align-items-center me-3">
          <li class="nav-item"><a class="nav-link active" href="#feed"><i class="fas fa-home"></i> Home</a></li>
          <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="settings.php"><i class="fas fa-gear"></i> Settings</a></li>
          <li class="nav-item"><button class="mode-toggle" id="modeToggle" type="button" aria-label="Switch display mode"><i class="fas fa-moon"></i></button></li>
        </ul>
        <div class="dash-user-badge">
          <div class="dash-avatar"><?= htmlspecialchars($dashboardUserInitial, ENT_QUOTES, "UTF-8") ?></div>
          <span><?= htmlspecialchars($dashboardUserName, ENT_QUOTES, "UTF-8") ?></span>
        </div>
        <a href="logout.php" class="btn-header-auth btn-header-login ms-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </nav>
</header>

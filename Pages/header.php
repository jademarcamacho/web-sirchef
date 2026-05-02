<?php
$loggedIn = isset($_SESSION["user_id"]);
$adminLoggedIn = function_exists("current_admin_account_id") && current_admin_account_id();
$showAdmin = false;
if (($loggedIn || $adminLoggedIn) && isset($conn) && function_exists("is_admin")) {
    $showAdmin = is_admin($conn);
}
?>
<header class="bohemian-header">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand brand-animated" href="index.php">
        <span class="brand-icon"><i class="fas fa-mortar-pestle"></i></span>
        <span class="brand-text">SirChef</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
        <span style="color:white;font-size:1.5rem"><i class="fas fa-bars"></i></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
          <li class="nav-item"><a class="nav-link" href="team.php">Team</a></li>
          <li class="nav-item"><a class="nav-link" href="recipe.php">Recipe</a></li>
          <?php if ($loggedIn || $adminLoggedIn): ?>
            <?php if ($loggedIn): ?><li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li><?php endif; ?>
            <?php if ($showAdmin): ?><li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li><?php endif; ?>
            <li class="nav-item ms-2"><a href="logout.php" class="btn-header-auth btn-header-login"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          <?php else: ?>
            <li class="nav-item ms-2"><a href="#" class="btn-header-auth btn-header-login" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-sign-in-alt"></i> Log In</a></li>
            <li class="nav-item ms-2"><a href="#" class="btn-header-auth btn-header-register" data-bs-toggle="modal" data-bs-target="#registerModal"><i class="fas fa-user-plus"></i> Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

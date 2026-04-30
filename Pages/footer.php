<footer class="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <h4 class="footer-title"><i class="fas fa-mortar-pestle me-2"></i> SirChef</h4>
        <p class="mb-4">Ingredient-first recipe discovery and a cooking community built to reduce food waste at home.</p>
        <div class="social-icons">
          <a href="https://facebook.com" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://instagram.com" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="https://x.com" aria-label="X"><i class="fab fa-x-twitter"></i></a>
          <a href="https://youtube.com" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-6">
        <h5 class="footer-title">Quick Links</h5>
        <div class="footer-links">
          <a href="index.php">Home</a>
          <a href="about.php">About</a>
          <a href="recipe.php">Recipes</a>
          <a href="team.php">Team</a>
          <a href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5 class="footer-title">Community</h5>
        <div class="footer-links">
          <a href="dashboard.php">Dashboard</a>
          <a href="profile.php">Profile</a>
          <a href="settings.php">Settings</a>
          <a href="chat.php">Messages</a>
          <a href="privacy.php">Privacy</a>
        </div>
      </div>

      <div class="col-lg-3">
        <h5 class="footer-title">Stay Updated</h5>
        <p class="mb-3">Get new recipes, ingredient tips, and SirChef updates.</p>
        <form id="newsletterForm" class="input-group mb-2">
          <input type="email" name="email" class="form-control" placeholder="Your email address" required />
          <input type="hidden" name="action" value="newsletter" />
          <button class="btn btn-bohemian" type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
        <small id="newsletterMsg" class="text-white-50"></small>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="copyright">
          <p>&copy; 2026 SirChef. All rights reserved. |
            <a href="privacy.php" class="text-white">Privacy Policy</a> |
            <a href="terms-of-service.php" class="text-white">Terms of Service</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</footer>

<script>
document.addEventListener('submit', function (event) {
  if (event.target && event.target.id === 'newsletterForm') {
    event.preventDefault();
    const msg = document.getElementById('newsletterMsg');
    fetch('backend.php', { method: 'POST', body: new FormData(event.target) })
      .then(r => r.json())
      .then(data => {
        msg.textContent = data.message || 'Subscribed.';
        if (data.success) event.target.reset();
      })
      .catch(() => { msg.textContent = 'Unable to subscribe right now.'; });
  }
});
</script>

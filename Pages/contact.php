
<?php
session_start();
include 'db.php';
?>

<!doctype html>
<html lang="en">
<head>
  <base target="_self" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | SirChef</title>

  <link rel="stylesheet" href="../styles/main.css" />
  <link rel="stylesheet" href="../styles/contact.css" />
  <meta name="description" content="Get in touch with the SirChef team" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />
</head>

<body>

  <?php include 'header.php'; ?>

  <!-- ── Contact Hero ── -->
  <section class="contact-hero">
    <div class="container">
      <h1 class="contact-hero-title">
        <i class="fas fa-paper-plane me-3" style="color: var(--accent-color);"></i>Get In Touch
      </h1>
      <p class="contact-hero-subtitle">
        We'd love to hear from you — questions, feedback, or just a hello 👋
      </p>
    </div>
  </section>

  <!-- ── Main Contact Section ── -->
  <section class="contact-section">
    <div class="container">
      <div class="row g-4">

        <!-- Left: Info Card -->
        <div class="col-lg-5">
          <div class="contact-info-card">
            <h3 class="contact-info-title">Contact Information</h3>
            <p class="contact-info-subtitle">Reach us through any of these channels</p>

            <div class="contact-info-item">
              <div class="contact-info-icon icon-primary">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div class="contact-info-text">
                <h6>Our Location</h6>
                <p>123 Bohemian Lane, Manila,<br>Philippines 1000</p>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="contact-info-icon icon-accent">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="contact-info-text">
                <h6>Email Us</h6>
                <a href="mailto:hello@sirchef.com">hello@sirchef.com</a>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="contact-info-icon icon-secondary">
                <i class="fas fa-phone-alt"></i>
              </div>
              <div class="contact-info-text">
                <h6>Call Us</h6>
                <a href="tel:+639123456789">+63 912 345 6789</a>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="contact-info-icon icon-earth">
                <i class="fas fa-clock"></i>
              </div>
              <div class="contact-info-text">
                <h6>Office Hours</h6>
                <p>Mon – Fri: 9:00 AM – 6:00 PM<br>Sat: 10:00 AM – 2:00 PM</p>
              </div>
            </div>

            <!-- Social -->
            <div class="contact-social-row">
              <p>Follow Us</p>
              <div class="contact-social-icons">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Form Card -->
        <div class="col-lg-7">
          <div class="contact-form-card">
            <h3 class="contact-form-title">Send Us a Message</h3>
            <p class="contact-form-subtitle">Fill in the form and we'll get back to you within 24 hours</p>

            <form id="contactForm" novalidate>

              <!-- Name row -->
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label class="form-label">First Name</label>
                  <div class="contact-input-group">
                    <input type="text" class="form-control" id="contactFirstName" name="first_name" placeholder="John" required />
                    <i class="fas fa-user contact-input-icon"></i>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Last Name</label>
                  <div class="contact-input-group">
                    <input type="text" class="form-control" id="contactLastName" name="last_name" placeholder="Doe" required />
                    <i class="fas fa-user contact-input-icon"></i>
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="contact-input-group">
                  <input type="email" class="form-control" id="contactEmail" name="email" placeholder="name@example.com" required />
                  <i class="fas fa-envelope contact-input-icon"></i>
                </div>
              </div>

              <!-- Subject -->
              <div class="mb-3">
                <label class="form-label">Subject</label>
                <div class="contact-input-group">
                  <select class="form-select" id="contactSubject" name="subject" required style="padding-left: 2.7rem;">
                    <option value="" disabled selected>Select a topic…</option>
                    <option>General Inquiry</option>
                    <option>Recipe Suggestion</option>
                    <option>Technical Support</option>
                    <option>Partnership / Collaboration</option>
                    <option>Bug Report</option>
                    <option>Other</option>
                  </select>
                  <i class="fas fa-tag contact-input-icon"></i>
                </div>
              </div>

              <!-- Message -->
              <div class="mb-4">
                <label class="form-label">Your Message</label>
                <div class="contact-textarea-group">
                  <textarea class="form-control" id="contactMessage" name="message" placeholder="Tell us what's on your mind..." required></textarea>
                  <i class="fas fa-comment-alt contact-textarea-icon"></i>
                </div>
              </div>

              <button type="submit" class="btn-contact-submit">
                <i class="fas fa-paper-plane"></i>
                <span>Send Message</span>
              </button>
              <input type="hidden" name="action" value="contact" />

              <!-- Success message -->
              <div class="contact-success" id="contactSuccess">
                <i class="fas fa-check-circle"></i>
                <strong>Message sent!</strong><br>
                Thanks for reaching out — we'll reply within 24 hours. 🍳
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── Location Strip ── -->
  <section class="contact-map-strip">
    <div class="container">
      <div class="contact-map-badge">
        <i class="fas fa-map-marker-alt"></i>
        Based in Manila, Philippines — cooking up something great 🍲
      </div>
      <p style="color: rgba(255,255,255,0.55); font-size: 0.85rem; font-family: 'Poppins', sans-serif; margin: 0;">
        SirChef · Bohemian Recipe Discovery Platform
      </p>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>
  <?php include 'login_regis.php'; ?>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const firstName = document.getElementById('contactFirstName').value.trim();
      const email     = document.getElementById('contactEmail').value.trim();
      const subject   = document.getElementById('contactSubject').value;
      const message   = document.getElementById('contactMessage').value.trim();

      if (!firstName || !email || !subject || !message) {
        alert('Please fill in all required fields.');
        return;
      }

      // Simulate send
      const btn = this.querySelector('.btn-contact-submit');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending…</span>';
      btn.disabled = true;

      setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> <span>Sent!</span>';
        document.getElementById('contactSuccess').style.display = 'block';
        this.reset();
        setTimeout(() => {
          btn.innerHTML = '<i class="fas fa-paper-plane"></i> <span>Send Message</span>';
          btn.disabled = false;
          document.getElementById('contactSuccess').style.display = 'none';
        }, 4000);
      }, 1500);
    });
  </script>

  <script>
    // Functional backend submit. Capture phase prevents the older demo handler from running.
    document.getElementById('contactForm').addEventListener('submit', function(e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      const btn = this.querySelector('.btn-contact-submit');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending...</span>';
      btn.disabled = true;
      fetch('backend.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json())
        .then(data => {
          if (!data.success) throw new Error(data.message || 'Unable to send message.');
          document.getElementById('contactSuccess').style.display = 'block';
          this.reset();
        })
        .catch(err => alert(err.message))
        .finally(() => {
          setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> <span>Send Message</span>';
            btn.disabled = false;
            document.getElementById('contactSuccess').style.display = 'none';
          }, 2500);
        });
    }, true);
  </script>
</body>
</html>

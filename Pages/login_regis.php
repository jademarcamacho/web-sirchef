<!-- ==================== LOGIN MODAL ==================== -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="loginModalLabel">
            <i class="fas fa-utensils me-2"></i> Welcome back
          </h5>
          <p class="auth-modal-subtitle">Sign in to continue your culinary journey</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <ul class="nav auth-tabs mb-4" id="loginTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="forgot-tab" data-bs-toggle="tab" data-bs-target="#forgot" type="button" role="tab">Forgot Password</button>
          </li>
        </ul>

        <div class="tab-content" id="loginTabsContent">

          <!-- Login Form -->
          <div class="tab-pane fade show active" id="login" role="tabpanel">
            <form id="loginForm">
              <input type="hidden" name="action" value="login" />

              <div class="mb-3">
                <label for="loginEmail" class="form-label">Email Address</label>
                <div class="auth-input-group">
                  <i class="fas fa-envelope auth-input-icon"></i>
                  <input type="email" name="email" class="form-control" id="loginEmail" placeholder="name@example.com" required />
                </div>
              </div>

              <div class="mb-3">
                <label for="loginPassword" class="form-label">Password</label>
                <div class="auth-input-group">
                  <i class="fas fa-lock auth-input-icon"></i>
                  <input type="password" name="password" class="form-control" id="loginPassword" placeholder="Enter your password" required />
                  <button type="button" class="auth-eye-btn" onclick="toggleAuthPassword('loginPassword', this)" tabindex="-1">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="auth-error-msg" id="loginPasswordError">
                  <i class="fas fa-exclamation-circle me-1"></i> Wrong password or email not found.
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" />
                <label class="form-check-label" for="rememberMe">Remember me</label>
              </div>

              <button type="submit" class="btn-auth-primary" id="loginSubmitBtn">
                <span class="btn-text">Sign In</span>
                <i class="fas fa-arrow-right"></i>
              </button>
            </form>

            <div class="auth-modal-footer">
              Don't have an account?
              <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register here</a>
            </div>
          </div>

          <!-- Forgot Password Form -->
          <div class="tab-pane fade" id="forgot" role="tabpanel">
            <div class="auth-forgot-icon">
              <i class="fas fa-key"></i>
            </div>
            <p class="text-center text-muted mb-3" style="font-size:0.88rem;">
              Enter your email and we'll send you a reset link.
            </p>
            <form id="forgotPasswordForm">
              <div class="mb-3">
                <label for="forgotEmail" class="form-label">Email Address</label>
                <div class="auth-input-group">
                  <i class="fas fa-envelope auth-input-icon"></i>
                  <input type="email" class="form-control" id="forgotEmail" placeholder="name@example.com" required />
                </div>
              </div>
              <button type="submit" class="btn-auth-secondary">
                <i class="fas fa-paper-plane"></i>
                <span>Send Reset Link</span>
              </button>
            </form>
            <div class="auth-modal-footer">
              Remember your password?
              <a href="#" data-bs-toggle="tab" data-bs-target="#login">Back to login</a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- ==================== END LOGIN MODAL ==================== -->


<!-- ==================== REGISTER MODAL ==================== -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="registerModalLabel">
            <i class="fas fa-user-plus me-2"></i> Join SirChef
          </h5>
          <p class="auth-modal-subtitle">Create your free account and start cooking</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="registerForm">
          <input type="hidden" name="action" value="register" />

          <div class="auth-name-row mb-3">
            <div>
              <label for="firstName" class="form-label">First Name</label>
              <div class="auth-input-group">
                <i class="fas fa-user auth-input-icon"></i>
                <input type="text" name="firstName" class="form-control" id="firstName" placeholder="John" required />
              </div>
            </div>
            <div>
              <label for="lastName" class="form-label">Last Name</label>
              <div class="auth-input-group">
                <i class="fas fa-user auth-input-icon"></i>
                <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Doe" required />
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="registerEmail" class="form-label">Email Address</label>
            <div class="auth-input-group">
              <i class="fas fa-envelope auth-input-icon"></i>
              <input type="email" name="email" class="form-control" id="registerEmail" placeholder="name@example.com" required />
            </div>
            <div class="auth-error-msg" id="registerEmailError">
              <i class="fas fa-exclamation-circle me-1"></i> This email is already registered.
            </div>
          </div>

          <div class="mb-3">
            <label for="registerPassword" class="form-label">Password</label>
            <div class="auth-input-group">
              <i class="fas fa-lock auth-input-icon"></i>
              <input type="password" name="password" class="form-control" id="registerPassword"
                placeholder="Create a password" required
                oninput="checkPasswordStrength(this.value); updatePasswordChecklist(this.value);" />
              <button type="button" class="auth-eye-btn" onclick="toggleAuthPassword('registerPassword', this)" tabindex="-1">
                <i class="fas fa-eye"></i>
              </button>
            </div>

            <!-- Strength Bar -->
            <div class="auth-strength-bar mt-2">
              <div class="auth-strength-fill" id="strengthFill"></div>
            </div>

            <!-- ── Password Requirements Checklist ── -->
            <ul class="pw-checklist" id="pwChecklist">
              <li id="req-length">
                <i class="fas fa-circle-xmark"></i>
                <span>At least 8 characters</span>
              </li>
              <li id="req-upper">
                <i class="fas fa-circle-xmark"></i>
                <span>One uppercase letter (A–Z)</span>
              </li>
              <li id="req-number">
                <i class="fas fa-circle-xmark"></i>
                <span>One number (0–9)</span>
              </li>
              <li id="req-special">
                <i class="fas fa-circle-xmark"></i>
                <span>One special character (!@#$…)</span>
              </li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <div class="auth-input-group">
              <i class="fas fa-lock auth-input-icon"></i>
              <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm your password" required />
              <button type="button" class="auth-eye-btn" onclick="toggleAuthPassword('confirmPassword', this)" tabindex="-1">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div class="auth-error-msg" id="passwordError">
              <i class="fas fa-exclamation-circle me-1"></i> Passwords do not match
            </div>
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="agreeTerms" required />
            <label class="form-check-label" for="agreeTerms">
              I agree to the <a href="terms-of-service.php" class="forgot-password">Terms of Service</a>
              and <a href="privacy.php" class="forgot-password">Privacy Policy</a>
            </label>
          </div>

          <button type="submit" class="btn-auth-primary" id="registerSubmitBtn" disabled>
            <span class="btn-text">Create Account</span>
            <i class="fas fa-arrow-right"></i>
          </button>
        </form>

        <div class="auth-modal-footer">
          Already have an account?
          <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login here</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ==================== END REGISTER MODAL ==================== -->


<style>
  /* ── Password Requirements Checklist ── */
  .pw-checklist {
    list-style: none;
    padding: 8px 0 0 0;
    margin: 0;
    display: none; /* hidden until user starts typing */
  }

  .pw-checklist.visible {
    display: block;
  }

  .pw-checklist li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    color: #aaa;
    margin-bottom: 4px;
    transition: color 0.25s ease;
  }

  .pw-checklist li i {
    font-size: 0.85rem;
    color: #aaa;
    transition: color 0.25s ease;
  }

  /* Met requirement — green */
  .pw-checklist li.met {
    color: #87a96b;
  }

  .pw-checklist li.met i {
    color: #87a96b;
  }

  /* Create Account button — disabled look */
  #registerSubmitBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: all; /* keep cursor visible */
  }
</style>


<script>
  // ── Show/hide password toggle ─────────────────────────────
  function toggleAuthPassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
  }

  // ── Strength bar ──────────────────────────────────────────
  function checkPasswordStrength(value) {
    const fill = document.getElementById('strengthFill');
    if (!fill) return;
    let strength = 0;
    if (value.length >= 8)          strength++;
    if (/[A-Z]/.test(value))        strength++;
    if (/[0-9]/.test(value))        strength++;
    if (/[^A-Za-z0-9]/.test(value)) strength++;
    const colors = ['#ff6b6b', '#ffd166', '#4ecdc4', '#87a96b'];
    const widths = ['25%', '50%', '75%', '100%'];
    fill.style.width      = value.length === 0 ? '0%'  : widths[strength - 1]  || '25%';
    fill.style.background = value.length === 0 ? ''    : colors[strength - 1]  || colors[0];
  }

  // ── Live checklist + button gate ─────────────────────────
  function updatePasswordChecklist(value) {
    const rules = {
      'req-length':  value.length >= 8,
      'req-upper':   /[A-Z]/.test(value),
      'req-number':  /[0-9]/.test(value),
      'req-special': /[^A-Za-z0-9]/.test(value),
    };

    const checklist = document.getElementById('pwChecklist');
    const submitBtn = document.getElementById('registerSubmitBtn');

    // Show checklist as soon as user starts typing
    if (value.length > 0) {
      checklist.classList.add('visible');
    } else {
      checklist.classList.remove('visible');
    }

    let allMet = true;

    for (const [id, met] of Object.entries(rules)) {
      const li   = document.getElementById(id);
      const icon = li.querySelector('i');

      if (met) {
        li.classList.add('met');
        icon.classList.remove('fa-circle-xmark');
        icon.classList.add('fa-circle-check');
      } else {
        li.classList.remove('met');
        icon.classList.remove('fa-circle-check');
        icon.classList.add('fa-circle-xmark');
        allMet = false;
      }
    }

    // Enable/disable the Create Account button
    submitBtn.disabled = !allMet;
  }

  // ── Full password validation helper ──────────────────────
  function isValidPassword(value) {
    return (
      value.length >= 8 &&
      /[A-Z]/.test(value) &&
      /[0-9]/.test(value) &&
      /[^A-Za-z0-9]/.test(value)
    );
  }

  // ── Login: AJAX submit ────────────────────────────────────
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const btn = document.getElementById('loginSubmitBtn');
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<span class="btn-text"><i class="fas fa-spinner fa-spin"></i> Signing in...</span>';
      btn.disabled = true;

      const formData = new FormData(loginForm);

      fetch('/Web_sys/WEBSITE/Pages/backend.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.redirect || 'dashboard.php';
        } else {
          btn.innerHTML = originalHTML;
          btn.disabled = false;
          const passwordInput = document.getElementById('loginPassword');
          const passwordError = document.getElementById('loginPasswordError');
          passwordInput.classList.add('is-invalid');
          passwordError.textContent = data.message || 'Wrong password or email not found.';
          passwordError.classList.add('visible');
          if (data.redirect) window.location.href = data.redirect;
        }
      })
      .catch(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        alert('Something went wrong. Please try again.');
      });
    });

    document.getElementById('loginPassword').addEventListener('input', function () {
      this.classList.remove('is-invalid');
      document.getElementById('loginPasswordError').classList.remove('visible');
    });
  }

  // ── Register: AJAX submit ─────────────────────────────────
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    const confirmInput  = document.getElementById('confirmPassword');
    const errorMsg      = document.getElementById('passwordError');
    const passwordInput = document.getElementById('registerPassword');

    // Live confirm-password check
    confirmInput.addEventListener('input', function () {
      if (this.value && this.value !== passwordInput.value) {
        this.classList.add('is-invalid');
        errorMsg.classList.add('visible');
      } else {
        this.classList.remove('is-invalid');
        errorMsg.classList.remove('visible');
      }
    });

    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const password = passwordInput.value;
      const confirm  = confirmInput.value;

      // Double-check password validity on submit
      if (!isValidPassword(password)) {
        passwordInput.focus();
        return;
      }

      if (password !== confirm) {
        confirmInput.classList.add('is-invalid');
        errorMsg.classList.add('visible');
        return;
      }

      const btn = document.getElementById('registerSubmitBtn');
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<span class="btn-text"><i class="fas fa-spinner fa-spin"></i> Creating...</span>';
      btn.disabled = true;

      const formData = new FormData(registerForm);

      fetch('/Web_sys/WEBSITE/Pages/backend.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
          if (modal) modal.hide();
          let target = data.redirect || 'verify_email.php?email=' + encodeURIComponent(document.getElementById('registerEmail').value);
          window.location.href = target;
        } else {
          btn.innerHTML = originalHTML;
          btn.disabled = false;
          if (data.field === 'email') {
            const emailInput = document.getElementById('registerEmail');
            const emailError = document.getElementById('registerEmailError');
            emailInput.classList.add('is-invalid');
            emailError.classList.add('visible');
          } else {
            alert(data.message);
          }
        }
      })
      .catch(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        alert('Something went wrong. Please try again.');
      });
    });

    document.getElementById('registerEmail').addEventListener('input', function () {
      this.classList.remove('is-invalid');
      document.getElementById('registerEmailError').classList.remove('visible');
    });
  }

  // ── Forgot Password: AJAX submit ─────────────────────────
  const forgotForm = document.getElementById('forgotPasswordForm');
  if (forgotForm) {
    forgotForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const btn = forgotForm.querySelector('button[type="submit"]');
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending...</span>';
      btn.disabled = true;

      const formData = new FormData();
      formData.append('action', 'forgot_password');
      formData.append('email', document.getElementById('forgotEmail').value);

      fetch('/Web_sys/WEBSITE/Pages/backend.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btn.innerHTML = '<i class="fas fa-check"></i> <span>Email Sent!</span>';
        if (data.redirect) {
          let target = data.redirect;
          setTimeout(() => { window.location.href = target; }, 700);
        }
        setTimeout(() => {
          btn.innerHTML = originalHTML;
          btn.disabled = false;
        }, 3000);
      })
      .catch(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        alert('Something went wrong. Please try again.');
      });
    });
  }
</script>

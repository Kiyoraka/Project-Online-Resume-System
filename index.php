<?php
/**
 * Online Resume System - Landing Page
 * Public portfolio homepage
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$profile = getProfile();
$stats = getDashboardStats();
$experiences = getExperiences();
$educations = getEducations();
?>
<?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <?php if ($profile && $profile['profile_image']): ?>
                    <img src="<?= IMAGES_URL ?>/<?= e($profile['profile_image']) ?>" alt="<?= e($profile['full_name']) ?>" class="hero-image" onclick="openLoginModal()" style="cursor: pointer;" title="Admin Login">
                <?php else: ?>
                    <div class="hero-image-placeholder" onclick="openLoginModal()" style="cursor: pointer;" title="Admin Login">
                        <?= $profile ? strtoupper(substr($profile['full_name'], 0, 1)) : 'R' ?>
                    </div>
                <?php endif; ?>

                <h1 class="hero-name"><?= e($profile['full_name'] ?? 'Your Name') ?></h1>
                <p class="hero-title"><?= e($profile['job_title'] ?? 'Professional Title') ?></p>

                <div class="hero-buttons">
                    <a href="resume.php" class="hero-btn hero-btn-primary">View Resume</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['experiences'] ?>+</div>
                    <div class="stat-label">Work Experience</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['education'] ?></div>
                    <div class="stat-label">Education</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['skills'] ?>+</div>
                    <div class="stat-label">Skills</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['certifications'] ?></div>
                    <div class="stat-label">Certifications</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['projects'] ?></div>
                    <div class="stat-label">Projects</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Summary Section -->
    <?php if ($profile && $profile['summary']): ?>
    <section class="summary-section">
        <div class="container">
            <div class="summary-content">
                <h2 class="section-title">Professional Summary</h2>
                <p class="summary-text"><?= nl2br(e($profile['summary'])) ?></p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="container">
            <div class="summary-content">
                <h2 class="section-title">Get In Touch</h2>
            </div>
            <div class="contact-grid">
                <?php if ($profile && $profile['email']): ?>
                <a href="mailto:<?= e($profile['email']) ?>" class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <div class="contact-label">Email</div>
                        <div class="contact-value"><?= e($profile['email']) ?></div>
                    </div>
                </a>
                <?php endif; ?>

                <?php if ($profile && $profile['phone']): ?>
                <a href="tel:<?= e($profile['phone']) ?>" class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <div class="contact-label">Phone</div>
                        <div class="contact-value"><?= e($profile['phone']) ?></div>
                    </div>
                </a>
                <?php endif; ?>

                <?php if ($profile && $profile['location']): ?>
                <div class="contact-item" style="cursor: default;">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <div class="contact-label">Location</div>
                        <div class="contact-value"><?= e($profile['location']) ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($profile && $profile['linkedin_url']): ?>
                <a href="<?= e($profile['linkedin_url']) ?>" target="_blank" class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <div class="contact-label">LinkedIn</div>
                        <div class="contact-value">View Profile</div>
                    </div>
                </a>
                <?php endif; ?>

                <?php if ($profile && $profile['website_url']): ?>
                <a href="<?= e($profile['website_url']) ?>" target="_blank" class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <div class="contact-label">GitHub</div>
                        <div class="contact-value">View Profile</div>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal-overlay" id="loginModal" onclick="closeLoginModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <button class="modal-close" onclick="closeLoginModal()">&times;</button>
            <div class="modal-header">
                <div class="modal-logo">R</div>
                <h2 class="modal-title">Admin Login</h2>
                <p class="modal-subtitle">Sign in to manage your resume</p>
            </div>
            <div id="loginError" class="alert alert-danger" style="display: none;"></div>
            <form id="loginForm" onsubmit="handleLogin(event)">
                <input type="hidden" name="csrf_token" value="<?= e(generateCSRFToken()) ?>">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="admin@gmail.com" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;" id="loginBtn">Sign In</button>
            </form>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: var(--space-4);
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-content {
            background: var(--white);
            border-radius: var(--rounded-xl);
            padding: var(--space-8);
            width: 100%;
            max-width: 400px;
            position: relative;
            box-shadow: var(--shadow-xl);
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .modal-close {
            position: absolute;
            top: var(--space-4);
            right: var(--space-4);
            background: none;
            border: none;
            font-size: var(--text-2xl);
            color: var(--gray-400);
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }
        .modal-close:hover {
            color: var(--gray-600);
        }
        .modal-header {
            text-align: center;
            margin-bottom: var(--space-6);
        }
        .modal-logo {
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: var(--white);
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--text-xl);
            font-weight: var(--font-bold);
            margin: 0 auto var(--space-4);
        }
        .modal-title {
            font-size: var(--text-xl);
            color: var(--gray-900);
            margin-bottom: var(--space-1);
        }
        .modal-subtitle {
            color: var(--gray-500);
            font-size: var(--text-sm);
            margin: 0;
        }
        .modal-content .form-group {
            margin-bottom: var(--space-4);
        }
        .modal-content .btn {
            margin-top: var(--space-2);
        }
    </style>

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLoginModal(event) {
            if (event && event.target !== event.currentTarget) return;
            document.getElementById('loginModal').classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('loginError').style.display = 'none';
            document.getElementById('loginForm').reset();
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLoginModal();
        });

        function handleLogin(event) {
            event.preventDefault();
            const form = event.target;
            const btn = document.getElementById('loginBtn');
            const errorDiv = document.getElementById('loginError');

            btn.disabled = true;
            btn.textContent = 'Signing in...';
            errorDiv.style.display = 'none';

            const formData = new FormData(form);

            fetch('admin/ajax-login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'admin/index.php';
                } else {
                    errorDiv.textContent = data.message || 'Invalid credentials';
                    errorDiv.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Sign In';
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Sign In';
            });
        }
    </script>

<?php include __DIR__ . '/includes/footer.php'; ?>

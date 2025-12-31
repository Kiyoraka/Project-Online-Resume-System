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
                    <img src="<?= IMAGES_URL ?>/<?= e($profile['profile_image']) ?>" alt="<?= e($profile['full_name']) ?>" class="hero-image">
                <?php else: ?>
                    <div class="hero-image-placeholder">
                        <?= $profile ? strtoupper(substr($profile['full_name'], 0, 1)) : 'R' ?>
                    </div>
                <?php endif; ?>

                <h1 class="hero-name"><?= e($profile['full_name'] ?? 'Your Name') ?></h1>
                <p class="hero-title"><?= e($profile['job_title'] ?? 'Professional Title') ?></p>

                <div class="hero-buttons">
                    <a href="resume.php" class="hero-btn hero-btn-primary">View Resume</a>
                    <a href="resume.php?print=1" class="hero-btn hero-btn-outline" onclick="window.open(this.href); setTimeout(function(){ window.print(); }, 500); return false;">Download PDF</a>
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
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <div class="contact-label">Website</div>
                        <div class="contact-value">Visit Site</div>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Online Resume System - Resume View
 * ATS-friendly clean text format
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$profile = getProfile();
$experiences = getExperiences();
$educations = getEducations();
$skills = getSkills();
$skillsByCategory = getSkillsByCategory();
$certifications = getCertifications();
$projects = getProjects();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($profile['summary'] ?? 'Professional Resume') ?>">
    <title><?= e($profile['full_name'] ?? 'Resume') ?> - Resume</title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/landing.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/resume.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/print.css" media="print">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <div class="navbar-brand-icon">R</div>
                <span class="hide-mobile"><?= e($profile['full_name'] ?? 'Resume') ?></span>
            </a>
            <div class="navbar-nav">
                <a href="index.php" class="navbar-link">Home</a>
                <a href="resume.php" class="navbar-link active">Resume</a>
                <a href="index.php#contact" class="navbar-link">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Resume Page -->
    <main class="resume-page">
        <div class="resume-container">
            <!-- Action Buttons -->
            <div class="resume-actions">
                <a href="index.php" class="btn btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print / Save PDF
                </button>
            </div>

            <!-- Resume Document -->
            <div class="resume">
                <!-- Header -->
                <header class="resume-header">
                    <h1 class="resume-name"><?= e($profile['full_name'] ?? 'Your Name') ?></h1>
                    <?php if ($profile && $profile['job_title']): ?>
                        <p class="resume-title"><?= e($profile['job_title']) ?></p>
                    <?php endif; ?>

                    <div class="resume-contact">
                        <?php if ($profile && $profile['location']): ?>
                            <span class="resume-contact-item"><?= e($profile['location']) ?></span>
                        <?php endif; ?>
                        <?php if ($profile && $profile['email']): ?>
                            <span class="resume-contact-item">
                                <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a>
                            </span>
                        <?php endif; ?>
                        <?php if ($profile && $profile['phone']): ?>
                            <span class="resume-contact-item"><?= e($profile['phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </header>

                <!-- Professional Summary -->
                <?php if ($profile && $profile['summary']): ?>
                <section class="resume-section">
                    <h2 class="resume-section-title">Professional Summary</h2>
                    <p class="resume-entry-description"><?= nl2br(e($profile['summary'])) ?></p>
                </section>
                <?php endif; ?>

                <!-- Work Experience -->
                <?php if (!empty($experiences)): ?>
                <section class="resume-section">
                    <h2 class="resume-section-title">Work Experience</h2>
                    <?php foreach ($experiences as $exp): ?>
                        <div class="resume-entry">
                            <div class="resume-entry-header">
                                <h3 class="resume-entry-title"><?= e($exp['job_title']) ?></h3>
                                <span class="resume-entry-date"><?= formatDateRange($exp['start_date'], $exp['end_date'], $exp['is_current']) ?></span>
                            </div>
                            <p class="resume-entry-subtitle"><?= e($exp['company_name']) ?><?= $exp['location'] ? ', ' . e($exp['location']) : '' ?></p>
                            <?php if ($exp['description']): ?>
                                <div class="resume-entry-description">
                                    <?php
                                    $lines = explode("\n", $exp['description']);
                                    $hasBullets = false;
                                    foreach ($lines as $line) {
                                        $line = trim($line);
                                        if (strpos($line, '- ') === 0 || strpos($line, '* ') === 0) {
                                            if (!$hasBullets) {
                                                echo '<ul>';
                                                $hasBullets = true;
                                            }
                                            echo '<li>' . e(substr($line, 2)) . '</li>';
                                        } else if (!empty($line)) {
                                            if ($hasBullets) {
                                                echo '</ul>';
                                                $hasBullets = false;
                                            }
                                            echo '<p>' . e($line) . '</p>';
                                        }
                                    }
                                    if ($hasBullets) echo '</ul>';
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
                <?php endif; ?>

                <!-- Education -->
                <?php if (!empty($educations)): ?>
                <section class="resume-section">
                    <h2 class="resume-section-title">Education</h2>
                    <?php foreach ($educations as $edu): ?>
                        <div class="resume-entry">
                            <div class="resume-entry-header">
                                <h3 class="resume-entry-title"><?= e($edu['degree']) ?><?= $edu['field_of_study'] ? ' in ' . e($edu['field_of_study']) : '' ?></h3>
                                <span class="resume-entry-date"><?= formatDateRange($edu['start_date'], $edu['end_date']) ?></span>
                            </div>
                            <p class="resume-entry-subtitle"><?= e($edu['institution']) ?><?= $edu['location'] ? ', ' . e($edu['location']) : '' ?></p>
                        </div>
                    <?php endforeach; ?>
                </section>
                <?php endif; ?>

                <!-- Skills -->
                <?php if (!empty($skills)): ?>
                <section class="resume-section">
                    <h2 class="resume-section-title">Skills</h2>
                    <div class="resume-skills-list">
                        <?php
                        $skillNames = array_column($skills, 'skill_name');
                        echo e(implode(', ', $skillNames));
                        ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Certifications -->
                <?php if (!empty($certifications)): ?>
                <section class="resume-section">
                    <h2 class="resume-section-title">Certifications</h2>
                    <?php foreach ($certifications as $cert): ?>
                        <div class="resume-cert">
                            <span class="resume-cert-name"><?= e($cert['cert_name']) ?></span>
                            <span class="resume-cert-org"> - <?= e($cert['issuing_org']) ?></span>
                            <?php if ($cert['issue_date']): ?>
                                <span class="resume-cert-org"> (<?= formatDate($cert['issue_date']) ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
                <?php endif; ?>

                <!-- Projects -->
                <?php if (!empty($projects)): ?>
                <section class="resume-section">
                    <h2 class="resume-section-title">Projects</h2>
                    <?php foreach ($projects as $project): ?>
                        <div class="resume-project">
                            <div class="resume-project-name"><?= e($project['project_name']) ?></div>
                            <?php if ($project['technologies_used']): ?>
                                <div class="resume-project-tech"><?= e($project['technologies_used']) ?></div>
                            <?php endif; ?>
                            <?php if ($project['description']): ?>
                                <div class="resume-project-desc">
                                    <?php
                                    $lines = explode("\n", $project['description']);
                                    $hasBullets = false;
                                    foreach ($lines as $line) {
                                        $line = trim($line);
                                        if (strpos($line, '- ') === 0 || strpos($line, '* ') === 0) {
                                            if (!$hasBullets) {
                                                echo '<ul>';
                                                $hasBullets = true;
                                            }
                                            echo '<li>' . e(substr($line, 2)) . '</li>';
                                        } else if (!empty($line)) {
                                            if ($hasBullets) {
                                                echo '</ul>';
                                                $hasBullets = false;
                                            }
                                            echo '<p>' . e($line) . '</p>';
                                        }
                                    }
                                    if ($hasBullets) echo '</ul>';
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="footer-text">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?></p>
        </div>
    </footer>

    <script src="<?= JS_URL ?>/main.js"></script>
</body>
</html>

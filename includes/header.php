<?php
/**
 * Online Resume System - Public Header
 * Navigation for public pages
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$profile = getProfile();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($profile['summary'] ?? 'Professional Resume') ?>">
    <title><?= e($profile['full_name'] ?? 'Resume') ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/landing.css">
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
                <a href="index.php" class="navbar-link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
                <a href="resume.php" class="navbar-link <?= $currentPage === 'resume' ? 'active' : '' ?>">Resume</a>
                <a href="#contact" class="navbar-link">Contact</a>
            </div>
        </div>
    </nav>

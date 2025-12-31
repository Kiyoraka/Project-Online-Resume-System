<?php
/**
 * Online Resume System - Admin Logout Handler
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/includes/auth.php';

// Log out the user
logoutUser();

// Start new session for flash message
session_start();
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'You have been logged out successfully.'
];

// Redirect to login page
header('Location: login.php');
exit;

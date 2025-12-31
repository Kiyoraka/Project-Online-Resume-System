<?php
/**
 * Online Resume System - Helper Functions
 * Common utilities and database operations
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/config.php';

// =====================================================
// Profile Functions
// =====================================================
/**
 * Get profile data (single row)
 */
function getProfile() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
    return $stmt->fetch() ?: [];
}

/**
 * Update profile data
 */
function updateProfile($data) {
    $pdo = getDB();
    $sql = "UPDATE profile SET
            full_name = :full_name,
            job_title = :job_title,
            email = :email,
            phone = :phone,
            location = :location,
            linkedin_url = :linkedin_url,
            website_url = :website_url,
            profile_image = :profile_image,
            summary = :summary,
            updated_at = NOW()
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

// =====================================================
// Experience Functions
// =====================================================
/**
 * Get all experiences ordered by display_order
 */
function getExperiences() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM experiences ORDER BY display_order ASC, start_date DESC");
    return $stmt->fetchAll();
}

/**
 * Get single experience by ID
 */
function getExperience($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM experiences WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/**
 * Create new experience
 */
function createExperience($data) {
    $pdo = getDB();
    $sql = "INSERT INTO experiences (company_name, job_title, location, start_date, end_date, is_current, description, display_order)
            VALUES (:company_name, :job_title, :location, :start_date, :end_date, :is_current, :description, :display_order)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Update experience
 */
function updateExperience($data) {
    $pdo = getDB();
    $sql = "UPDATE experiences SET
            company_name = :company_name,
            job_title = :job_title,
            location = :location,
            start_date = :start_date,
            end_date = :end_date,
            is_current = :is_current,
            description = :description,
            display_order = :display_order,
            updated_at = NOW()
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Delete experience
 */
function deleteExperience($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM experiences WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

// =====================================================
// Education Functions
// =====================================================
/**
 * Get all education entries
 */
function getEducations() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM education ORDER BY display_order ASC, start_date DESC");
    return $stmt->fetchAll();
}

/**
 * Get single education by ID
 */
function getEducation($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/**
 * Create new education
 */
function createEducation($data) {
    $pdo = getDB();
    $sql = "INSERT INTO education (institution, degree, field_of_study, location, start_date, end_date, description, display_order)
            VALUES (:institution, :degree, :field_of_study, :location, :start_date, :end_date, :description, :display_order)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Update education
 */
function updateEducation($data) {
    $pdo = getDB();
    $sql = "UPDATE education SET
            institution = :institution,
            degree = :degree,
            field_of_study = :field_of_study,
            location = :location,
            start_date = :start_date,
            end_date = :end_date,
            description = :description,
            display_order = :display_order,
            updated_at = NOW()
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Delete education
 */
function deleteEducation($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM education WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

// =====================================================
// Skills Functions
// =====================================================
/**
 * Get all skills
 */
function getSkills() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM skills ORDER BY display_order ASC, category ASC");
    return $stmt->fetchAll();
}

/**
 * Get skills grouped by category
 */
function getSkillsByCategory() {
    $skills = getSkills();
    $grouped = [];
    foreach ($skills as $skill) {
        $category = $skill['category'] ?: 'Other';
        $grouped[$category][] = $skill;
    }
    return $grouped;
}

/**
 * Get single skill by ID
 */
function getSkill($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/**
 * Create new skill
 */
function createSkill($data) {
    $pdo = getDB();
    $sql = "INSERT INTO skills (skill_name, category, proficiency_level, display_order)
            VALUES (:skill_name, :category, :proficiency_level, :display_order)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Update skill
 */
function updateSkill($data) {
    $pdo = getDB();
    $sql = "UPDATE skills SET
            skill_name = :skill_name,
            category = :category,
            proficiency_level = :proficiency_level,
            display_order = :display_order,
            updated_at = NOW()
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Delete skill
 */
function deleteSkill($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

// =====================================================
// Certifications Functions
// =====================================================
/**
 * Get all certifications
 */
function getCertifications() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM certifications ORDER BY display_order ASC, issue_date DESC");
    return $stmt->fetchAll();
}

/**
 * Get single certification by ID
 */
function getCertification($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM certifications WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/**
 * Create new certification
 */
function createCertification($data) {
    $pdo = getDB();
    $sql = "INSERT INTO certifications (cert_name, issuing_org, issue_date, expiry_date, credential_url, display_order)
            VALUES (:cert_name, :issuing_org, :issue_date, :expiry_date, :credential_url, :display_order)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Update certification
 */
function updateCertification($data) {
    $pdo = getDB();
    $sql = "UPDATE certifications SET
            cert_name = :cert_name,
            issuing_org = :issuing_org,
            issue_date = :issue_date,
            expiry_date = :expiry_date,
            credential_url = :credential_url,
            display_order = :display_order,
            updated_at = NOW()
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Delete certification
 */
function deleteCertification($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM certifications WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

// =====================================================
// Projects Functions
// =====================================================
/**
 * Get all projects
 */
function getProjects() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY display_order ASC, start_date DESC");
    return $stmt->fetchAll();
}

/**
 * Get single project by ID
 */
function getProject($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/**
 * Create new project
 */
function createProject($data) {
    $pdo = getDB();
    $sql = "INSERT INTO projects (project_name, description, technologies_used, project_url, start_date, end_date, display_order)
            VALUES (:project_name, :description, :technologies_used, :project_url, :start_date, :end_date, :display_order)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Update project
 */
function updateProject($data) {
    $pdo = getDB();
    $sql = "UPDATE projects SET
            project_name = :project_name,
            description = :description,
            technologies_used = :technologies_used,
            project_url = :project_url,
            start_date = :start_date,
            end_date = :end_date,
            display_order = :display_order,
            updated_at = NOW()
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Delete project
 */
function deleteProject($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

// =====================================================
// User/Auth Functions
// =====================================================
/**
 * Get admin user by email
 */
function getUserByEmail($email) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}

/**
 * Update user email
 */
function updateUserEmail($id, $email) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE users SET email = :email, updated_at = NOW() WHERE id = :id");
    return $stmt->execute(['id' => $id, 'email' => $email]);
}

/**
 * Update user password
 */
function updateUserPassword($id, $passwordHash) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id");
    return $stmt->execute(['id' => $id, 'password_hash' => $passwordHash]);
}

// =====================================================
// Dashboard Statistics Functions
// =====================================================
/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    $pdo = getDB();

    return [
        'experiences' => $pdo->query("SELECT COUNT(*) FROM experiences")->fetchColumn(),
        'education' => $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn(),
        'skills' => $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
        'certifications' => $pdo->query("SELECT COUNT(*) FROM certifications")->fetchColumn(),
        'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    ];
}

// =====================================================
// Utility Functions
// =====================================================
/**
 * Format date for display
 */
function formatDate($date, $format = 'M Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format date range
 */
function formatDateRange($startDate, $endDate, $isCurrent = false) {
    $start = formatDate($startDate);
    if ($isCurrent) {
        return $start . ' - Present';
    }
    $end = formatDate($endDate);
    return $start . ' - ' . ($end ?: 'Present');
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged in user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Handle file upload for profile image
 */
function uploadProfileImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
    }

    if ($file['size'] > $maxSize) {
        return ['error' => 'File too large. Maximum size is 5MB.'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . time() . '.' . $extension;
    $destination = UPLOADS_PATH . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['error' => 'Failed to upload file.'];
}

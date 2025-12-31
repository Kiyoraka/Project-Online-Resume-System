<?php
/**
 * Online Resume System - Admin Education Management
 * CRUD for academic background
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth();

$errors = [];
$editMode = false;
$editData = null;

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle delete
if ($action === 'delete' && $id > 0) {
    if (deleteEducation($id)) {
        setFlash('success', 'Education entry deleted successfully.');
    } else {
        setFlash('danger', 'Failed to delete education entry.');
    }
    redirect('education.php');
}

// Handle edit
if ($action === 'edit' && $id > 0) {
    $editData = getEducation($id);
    if ($editData) $editMode = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request.';
    } else {
        $data = [
            'institution' => trim($_POST['institution'] ?? ''),
            'degree' => trim($_POST['degree'] ?? ''),
            'field_of_study' => trim($_POST['field_of_study'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?: null,
            'description' => trim($_POST['description'] ?? ''),
            'display_order' => (int)($_POST['display_order'] ?? 0),
        ];

        if (empty($data['institution'])) $errors[] = 'Institution is required.';
        if (empty($data['degree'])) $errors[] = 'Degree is required.';
        if (empty($data['start_date'])) $errors[] = 'Start date is required.';

        if (empty($errors)) {
            if (isset($_POST['id']) && $_POST['id'] > 0) {
                $data['id'] = (int)$_POST['id'];
                if (updateEducation($data)) {
                    setFlash('success', 'Education updated successfully.');
                    redirect('education.php');
                } else {
                    $errors[] = 'Failed to update education.';
                }
            } else {
                if (createEducation($data)) {
                    setFlash('success', 'Education added successfully.');
                    redirect('education.php');
                } else {
                    $errors[] = 'Failed to add education.';
                }
            }
        }
    }
}

$educations = getEducations();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title">Education</h1>
                </div>
                <div class="topbar-right">
                    <a href="logout.php" class="topbar-btn logout-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </header>

            <div class="page-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form-card" style="margin-bottom: var(--space-6);">
                    <h3 class="form-section-title"><?= $editMode ? 'Edit Education' : 'Add Education' ?></h3>
                    <form method="POST">
                        <?= csrfField() ?>
                        <?php if ($editMode): ?>
                            <input type="hidden" name="id" value="<?= e($editData['id']) ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Institution *</label>
                                <input type="text" name="institution" class="form-input" value="<?= e($editData['institution'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Degree *</label>
                                <input type="text" name="degree" class="form-input" value="<?= e($editData['degree'] ?? '') ?>" placeholder="e.g., Bachelor of Science" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Field of Study</label>
                                <input type="text" name="field_of_study" class="form-input" value="<?= e($editData['field_of_study'] ?? '') ?>" placeholder="e.g., Computer Science">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-input" value="<?= e($editData['location'] ?? '') ?>" placeholder="City, Country">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Start Date *</label>
                                <input type="date" name="start_date" class="form-input" value="<?= e($editData['start_date'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-input" value="<?= e($editData['end_date'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-input" value="<?= e($editData['display_order'] ?? 0) ?>" min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-textarea" rows="3" placeholder="Achievements, activities, or relevant coursework..."><?= e($editData['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-actions" style="border-top: none; padding-top: 0;">
                            <?php if ($editMode): ?><a href="education.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update' : 'Add' ?> Education</button>
                        </div>
                    </form>
                </div>

                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Education (<?= count($educations) ?>)</h3>
                </div>

                <?php if (empty($educations)): ?>
                    <div class="card"><div class="empty-state"><p>No education entries added yet.</p></div></div>
                <?php else: ?>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Institution</th>
                                    <th>Degree</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($educations as $edu): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($edu['institution']) ?></strong>
                                            <br><small style="color: var(--gray-500);"><?= e($edu['location'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?= e($edu['degree']) ?>
                                            <?php if ($edu['field_of_study']): ?>
                                                <br><small style="color: var(--gray-500);"><?= e($edu['field_of_study']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDateRange($edu['start_date'], $edu['end_date']) ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?action=edit&id=<?= $edu['id'] ?>" class="table-btn edit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                                <a href="?action=delete&id=<?= $edu['id'] ?>" class="table-btn delete" onclick="return confirm('Delete this entry?')">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
        console.log('%c Powered by Kiyo Software TechLab', 'color: #0047AB; font-size: 14px; font-weight: bold;');
    </script>
</body>
</html>

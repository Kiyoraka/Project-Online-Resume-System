<?php
/**
 * Online Resume System - Admin Projects Management
 * CRUD for portfolio projects
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

if ($action === 'delete' && $id > 0) {
    if (deleteProject($id)) {
        setFlash('success', 'Project deleted successfully.');
    } else {
        setFlash('danger', 'Failed to delete project.');
    }
    redirect('projects.php');
}

if ($action === 'edit' && $id > 0) {
    $editData = getProject($id);
    if ($editData) $editMode = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request.';
    } else {
        $data = [
            'project_name' => trim($_POST['project_name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'technologies_used' => trim($_POST['technologies_used'] ?? ''),
            'project_url' => trim($_POST['project_url'] ?? ''),
            'start_date' => $_POST['start_date'] ?: null,
            'end_date' => $_POST['end_date'] ?: null,
            'display_order' => (int)($_POST['display_order'] ?? 0),
        ];

        if (empty($data['project_name'])) $errors[] = 'Project name is required.';

        if (empty($errors)) {
            if (isset($_POST['id']) && $_POST['id'] > 0) {
                $data['id'] = (int)$_POST['id'];
                if (updateProject($data)) {
                    setFlash('success', 'Project updated successfully.');
                    redirect('projects.php');
                } else {
                    $errors[] = 'Failed to update project.';
                }
            } else {
                if (createProject($data)) {
                    setFlash('success', 'Project added successfully.');
                    redirect('projects.php');
                } else {
                    $errors[] = 'Failed to add project.';
                }
            }
        }
    }
}

$projects = getProjects();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - <?= e(APP_NAME) ?></title>
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
                            <line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title">Projects</h1>
                </div>
                <div class="topbar-right">
                    <a href="logout.php" class="topbar-btn logout-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
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
                    <h3 class="form-section-title"><?= $editMode ? 'Edit Project' : 'Add Project' ?></h3>
                    <form method="POST">
                        <?= csrfField() ?>
                        <?php if ($editMode): ?>
                            <input type="hidden" name="id" value="<?= e($editData['id']) ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Project Name *</label>
                                <input type="text" name="project_name" class="form-input" value="<?= e($editData['project_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Project URL</label>
                                <input type="url" name="project_url" class="form-input" value="<?= e($editData['project_url'] ?? '') ?>" placeholder="https://github.com/...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Technologies Used</label>
                            <input type="text" name="technologies_used" class="form-input" value="<?= e($editData['technologies_used'] ?? '') ?>" placeholder="e.g., Laravel, Vue.js, MySQL">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-input" value="<?= e($editData['start_date'] ?? '') ?>">
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
                            <textarea name="description" class="form-textarea" rows="4" placeholder="Describe the project, your role, and achievements..."><?= e($editData['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-actions" style="border-top: none; padding-top: 0;">
                            <?php if ($editMode): ?><a href="projects.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update' : 'Add' ?> Project</button>
                        </div>
                    </form>
                </div>

                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Projects (<?= count($projects) ?>)</h3>
                </div>

                <?php if (empty($projects)): ?>
                    <div class="card"><div class="empty-state"><p>No projects added yet.</p></div></div>
                <?php else: ?>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Technologies</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($project['project_name']) ?></strong>
                                            <?php if ($project['project_url']): ?>
                                                <br><a href="<?= e($project['project_url']) ?>" target="_blank" style="color: var(--primary); font-size: var(--text-sm);">View Project</a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($project['technologies_used']): ?>
                                                <small style="color: var(--gray-600);"><?= e($project['technologies_used']) ?></small>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDateRange($project['start_date'], $project['end_date']) ?: '-' ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?action=edit&id=<?= $project['id'] ?>" class="table-btn edit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                                <a href="?action=delete&id=<?= $project['id'] ?>" class="table-btn delete" onclick="return confirm('Delete this project?')">
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

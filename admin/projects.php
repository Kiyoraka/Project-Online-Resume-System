<?php
/**
 * Online Resume System - Admin Projects Management
 * CRUD with popup modal and pagination
 *
 * ULTRATHINK #256 - Modal + Pagination Redesign
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth();

$errors = [];

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

// Get all projects
$allProjects = getProjects();
$totalItems = count($allProjects);

// Pagination settings
$itemsPerPage = 5;
$totalPages = (int)ceil($totalItems / $itemsPerPage);
$currentPage = (int)(isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1);
$offset = ($currentPage - 1) * $itemsPerPage;
$projects = array_slice($allProjects, $offset, $itemsPerPage);

// Check if editing
$editData = null;
if ($action === 'edit' && $id > 0) {
    $editData = getProject($id);
}

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
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal {
            background: white;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s;
            margin: var(--space-4);
        }
        .modal-overlay.active .modal {
            transform: scale(1);
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-4) var(--space-6);
            border-bottom: 1px solid var(--gray-200);
        }
        .modal-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }
        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: var(--space-2);
            color: var(--gray-500);
            border-radius: var(--radius);
            transition: background 0.2s, color 0.2s;
        }
        .modal-close:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }
        .modal-body {
            padding: var(--space-6);
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: var(--space-3);
            padding: var(--space-4) var(--space-6);
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
        }
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            margin-top: var(--space-6);
        }
        .pagination-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 var(--space-3);
            border: 1px solid var(--gray-300);
            background: white;
            color: var(--gray-700);
            font-size: var(--text-sm);
            border-radius: var(--radius);
            text-decoration: none;
            transition: all 0.2s;
        }
        .pagination-btn:hover:not(.disabled):not(.active) {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }
        .pagination-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
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
                    <h1 class="page-title">Projects</h1>
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

                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Projects (<?= $totalItems ?>)</h3>
                    <button class="btn btn-primary" onclick="openModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--space-2);">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add Project
                    </button>
                </div>

                <?php if (empty($allProjects)): ?>
                    <div class="card">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <p>No projects added yet.</p>
                            <button class="btn btn-primary" onclick="openModal()" style="margin-top: var(--space-4);">Add Your First Project</button>
                        </div>
                    </div>
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
                                                <button class="table-btn edit" title="Edit" onclick="editProject(<?= htmlspecialchars(json_encode($project), ENT_QUOTES, 'UTF-8') ?>)">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </button>
                                                <a href="?action=delete&id=<?= $project['id'] ?>" class="table-btn delete" title="Delete" onclick="return confirm('Delete this project?')">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <a href="?page=<?= $currentPage - 1 ?>" class="pagination-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </a>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            <a href="?page=<?= $currentPage + 1 ?>" class="pagination-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="projectModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add Project</h3>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" id="projectForm">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" id="projectId" value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Project Name *</label>
                            <input type="text" name="project_name" id="projectName" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Project URL</label>
                            <input type="url" name="project_url" id="projectUrl" class="form-input" placeholder="https://github.com/...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Technologies Used</label>
                        <input type="text" name="technologies_used" id="technologiesUsed" class="form-input" placeholder="e.g., Laravel, Vue.js, MySQL">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="startDate" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="endDate" class="form-input">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="displayOrder" class="form-input" value="0" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea" rows="4" placeholder="Describe the project, your role, and achievements..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Project</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        function openModal() {
            document.getElementById('projectForm').reset();
            document.getElementById('projectId').value = '';
            document.getElementById('modalTitle').textContent = 'Add Project';
            document.getElementById('submitBtn').textContent = 'Add Project';
            document.getElementById('projectModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('projectModal').classList.remove('active');
        }

        function editProject(data) {
            document.getElementById('projectId').value = data.id;
            document.getElementById('projectName').value = data.project_name || '';
            document.getElementById('projectUrl').value = data.project_url || '';
            document.getElementById('technologiesUsed').value = data.technologies_used || '';
            document.getElementById('startDate').value = data.start_date || '';
            document.getElementById('endDate').value = data.end_date || '';
            document.getElementById('displayOrder').value = data.display_order || 0;
            document.getElementById('description').value = data.description || '';

            document.getElementById('modalTitle').textContent = 'Edit Project';
            document.getElementById('submitBtn').textContent = 'Update Project';
            document.getElementById('projectModal').classList.add('active');
        }

        // Close modal on overlay click
        var projModal = document.getElementById('projectModal');
        if (projModal) {
            projModal.addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        <?php if ($editData): ?>
        editProject(<?= json_encode($editData) ?>);
        <?php endif; ?>

        console.log('%c Powered by Kiyo Software TechLab', 'color: #0047AB; font-size: 14px; font-weight: bold;');
    </script>
</body>
</html>

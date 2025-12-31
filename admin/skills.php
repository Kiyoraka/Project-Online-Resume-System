<?php
/**
 * Online Resume System - Admin Skills Management
 * CRUD for technical and soft skills
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
    if (deleteSkill($id)) {
        setFlash('success', 'Skill deleted successfully.');
    } else {
        setFlash('danger', 'Failed to delete skill.');
    }
    redirect('skills.php');
}

if ($action === 'edit' && $id > 0) {
    $editData = getSkill($id);
    if ($editData) $editMode = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request.';
    } else {
        $data = [
            'skill_name' => trim($_POST['skill_name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'proficiency_level' => $_POST['proficiency_level'] ?? 'Intermediate',
            'display_order' => (int)($_POST['display_order'] ?? 0),
        ];

        if (empty($data['skill_name'])) $errors[] = 'Skill name is required.';

        if (empty($errors)) {
            if (isset($_POST['id']) && $_POST['id'] > 0) {
                $data['id'] = (int)$_POST['id'];
                if (updateSkill($data)) {
                    setFlash('success', 'Skill updated successfully.');
                    redirect('skills.php');
                } else {
                    $errors[] = 'Failed to update skill.';
                }
            } else {
                if (createSkill($data)) {
                    setFlash('success', 'Skill added successfully.');
                    redirect('skills.php');
                } else {
                    $errors[] = 'Failed to add skill.';
                }
            }
        }
    }
}

$skills = getSkills();
$skillsByCategory = getSkillsByCategory();
$flash = getFlash();

$proficiencyLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
$categories = ['Programming', 'Framework', 'Database', 'Frontend', 'Backend', 'Tools', 'Soft Skills', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/dashboard.css">
    <style>
        .skill-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-2) var(--space-3);
            background: var(--gray-100);
            border-radius: var(--rounded-full);
            font-size: var(--text-sm);
            margin: var(--space-1);
        }
        .skill-badge .level {
            font-size: var(--text-xs);
            color: var(--gray-500);
        }
        .category-group {
            margin-bottom: var(--space-6);
        }
        .category-title {
            font-size: var(--text-lg);
            font-weight: var(--font-semibold);
            color: var(--primary);
            margin-bottom: var(--space-3);
            padding-bottom: var(--space-2);
            border-bottom: 2px solid var(--primary);
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
                            <line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title">Skills</h1>
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
                    <h3 class="form-section-title"><?= $editMode ? 'Edit Skill' : 'Add Skill' ?></h3>
                    <form method="POST">
                        <?= csrfField() ?>
                        <?php if ($editMode): ?>
                            <input type="hidden" name="id" value="<?= e($editData['id']) ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Skill Name *</label>
                                <input type="text" name="skill_name" class="form-input" value="<?= e($editData['skill_name'] ?? '') ?>" placeholder="e.g., JavaScript, Python, Communication" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">Select category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= e($cat) ?>" <?= ($editData['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Proficiency Level</label>
                                <select name="proficiency_level" class="form-select">
                                    <?php foreach ($proficiencyLevels as $level): ?>
                                        <option value="<?= e($level) ?>" <?= ($editData['proficiency_level'] ?? 'Intermediate') === $level ? 'selected' : '' ?>><?= e($level) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-input" value="<?= e($editData['display_order'] ?? 0) ?>" min="0">
                            </div>
                        </div>

                        <div class="form-actions" style="border-top: none; padding-top: 0;">
                            <?php if ($editMode): ?><a href="skills.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update' : 'Add' ?> Skill</button>
                        </div>
                    </form>
                </div>

                <!-- Skills by Category -->
                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Skills (<?= count($skills) ?>)</h3>
                </div>

                <?php if (empty($skills)): ?>
                    <div class="card"><div class="empty-state"><p>No skills added yet.</p></div></div>
                <?php else: ?>
                    <div class="card">
                        <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                            <div class="category-group">
                                <div class="category-title"><?= e($category) ?></div>
                                <div>
                                    <?php foreach ($categorySkills as $skill): ?>
                                        <span class="skill-badge">
                                            <?= e($skill['skill_name']) ?>
                                            <span class="level">(<?= e($skill['proficiency_level']) ?>)</span>
                                            <a href="?action=edit&id=<?= $skill['id'] ?>" style="color: var(--primary); margin-left: var(--space-1);">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>
                                            <a href="?action=delete&id=<?= $skill['id'] ?>" style="color: var(--danger);" onclick="return confirm('Delete this skill?')">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </a>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
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

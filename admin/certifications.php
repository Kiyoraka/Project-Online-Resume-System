<?php
/**
 * Online Resume System - Admin Certifications Management
 * CRUD for professional certifications
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
    if (deleteCertification($id)) {
        setFlash('success', 'Certification deleted successfully.');
    } else {
        setFlash('danger', 'Failed to delete certification.');
    }
    redirect('certifications.php');
}

if ($action === 'edit' && $id > 0) {
    $editData = getCertification($id);
    if ($editData) $editMode = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request.';
    } else {
        $data = [
            'cert_name' => trim($_POST['cert_name'] ?? ''),
            'issuing_org' => trim($_POST['issuing_org'] ?? ''),
            'issue_date' => $_POST['issue_date'] ?: null,
            'expiry_date' => $_POST['expiry_date'] ?: null,
            'credential_url' => trim($_POST['credential_url'] ?? ''),
            'display_order' => (int)($_POST['display_order'] ?? 0),
        ];

        if (empty($data['cert_name'])) $errors[] = 'Certification name is required.';
        if (empty($data['issuing_org'])) $errors[] = 'Issuing organization is required.';

        if (empty($errors)) {
            if (isset($_POST['id']) && $_POST['id'] > 0) {
                $data['id'] = (int)$_POST['id'];
                if (updateCertification($data)) {
                    setFlash('success', 'Certification updated successfully.');
                    redirect('certifications.php');
                } else {
                    $errors[] = 'Failed to update certification.';
                }
            } else {
                if (createCertification($data)) {
                    setFlash('success', 'Certification added successfully.');
                    redirect('certifications.php');
                } else {
                    $errors[] = 'Failed to add certification.';
                }
            }
        }
    }
}

$certifications = getCertifications();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Certifications - <?= e(APP_NAME) ?></title>
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
                    <h1 class="page-title">Certifications</h1>
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
                    <h3 class="form-section-title"><?= $editMode ? 'Edit Certification' : 'Add Certification' ?></h3>
                    <form method="POST">
                        <?= csrfField() ?>
                        <?php if ($editMode): ?>
                            <input type="hidden" name="id" value="<?= e($editData['id']) ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Certification Name *</label>
                                <input type="text" name="cert_name" class="form-input" value="<?= e($editData['cert_name'] ?? '') ?>" placeholder="e.g., AWS Certified Developer" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Issuing Organization *</label>
                                <input type="text" name="issuing_org" class="form-input" value="<?= e($editData['issuing_org'] ?? '') ?>" placeholder="e.g., Amazon Web Services" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Issue Date</label>
                                <input type="date" name="issue_date" class="form-input" value="<?= e($editData['issue_date'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-input" value="<?= e($editData['expiry_date'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Credential URL</label>
                                <input type="url" name="credential_url" class="form-input" value="<?= e($editData['credential_url'] ?? '') ?>" placeholder="https://...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-input" value="<?= e($editData['display_order'] ?? 0) ?>" min="0">
                            </div>
                        </div>

                        <div class="form-actions" style="border-top: none; padding-top: 0;">
                            <?php if ($editMode): ?><a href="certifications.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update' : 'Add' ?> Certification</button>
                        </div>
                    </form>
                </div>

                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Certifications (<?= count($certifications) ?>)</h3>
                </div>

                <?php if (empty($certifications)): ?>
                    <div class="card"><div class="empty-state"><p>No certifications added yet.</p></div></div>
                <?php else: ?>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Certification</th>
                                    <th>Issuer</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($certifications as $cert): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($cert['cert_name']) ?></strong>
                                            <?php if ($cert['credential_url']): ?>
                                                <br><a href="<?= e($cert['credential_url']) ?>" target="_blank" style="color: var(--primary); font-size: var(--text-sm);">View Credential</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= e($cert['issuing_org']) ?></td>
                                        <td>
                                            <?= $cert['issue_date'] ? formatDate($cert['issue_date']) : '-' ?>
                                            <?php if ($cert['expiry_date']): ?>
                                                <br><small style="color: var(--gray-500);">Expires: <?= formatDate($cert['expiry_date']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?action=edit&id=<?= $cert['id'] ?>" class="table-btn edit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                                <a href="?action=delete&id=<?= $cert['id'] ?>" class="table-btn delete" onclick="return confirm('Delete this certification?')">
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

<?php
$adminPage = 'change-password';
require 'auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate
    if (!$currentPassword || !$newPassword || !$confirmPassword) {
        $error = 'All fields are required';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } else {
        // Fetch current admin password from DB
        $stmt = $pdo->prepare('SELECT password FROM admins WHERE id = ?');
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $error = 'Admin account not found';
        } elseif (!password_verify($currentPassword, $admin['password'])) {
            $error = 'Current password is incorrect';
        } else {
            // Hash new password and update
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateStmt = $pdo->prepare('UPDATE admins SET password = ? WHERE id = ?');
            
            if ($updateStmt->execute([$hashedPassword, $_SESSION['admin_id']])) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to update password. Please try again.';
            }
        }
    }
}
?>
<?php include 'partials/header.php'; ?>

<div class="form-section">
    <h2>Change Password</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>

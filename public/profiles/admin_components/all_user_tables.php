<?php
// Fetch all users using Admin's method
$allUsers = $userProfile->getAllUsers();

// Handle form submission for saving or canceling user edits
$edit_user_id = isset($_POST['edit_user_id']) ? $_POST['edit_user_id'] : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['username'], $_POST['role'], $_POST['user_id'])) {
        $username = $_POST['username'];
        $role = (int) $_POST['role'];
        $user_id = (int) $_POST['user_id'];
        $userProfile->updateUser($user_id, $username, $role);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>

<h3>All Users</h3>
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allUsers as $user): ?>
            <?php if ($edit_user_id == $user['user_id']): ?>
                <tr>
                    <form action="" method="post">
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <select name="role">
                                <option value="1" <?php echo $user['role'] == 1 ? 'selected' : ''; ?>>Admin</option>
                                <option value="0" <?php echo $user['role'] == 0 ? 'selected' : ''; ?>>Guest</option>
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                            <button type="submit">Save</button>
                            <button type="submit" name="cancel" value="1">Cancel</button>
                        </td>
                    </form>
                </tr>
            <?php else: ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="edit_user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                            <button type="submit">Edit</button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
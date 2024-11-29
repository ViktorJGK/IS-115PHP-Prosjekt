<?php

if (!$userProfile instanceof Admin) {
    header("Location: ../logout.php"); // Redirect to an error page if not an admin
    exit;
}

// Get all users from the database using the Admin class method
$allUsers = $userProfile->getAllUsers();

// Determine the edit mode if a specific user ID is set
$edit_user_id = isset($_POST['edit_user_id']) ? $_POST['edit_user_id'] : null;
?>
<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<div>
    <div>
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p>
        <br>

        <!-- Table displaying all users with options to edit -->
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
                        <!-- Editing mode for the selected user -->
                        <tr>
                            <form action="" method="post">
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td>
                                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                                </td>
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
                        <!-- Display mode for all other users -->
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <!-- Form with the "Edit" button for each user -->
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
    </div>
</div>

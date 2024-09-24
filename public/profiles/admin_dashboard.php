<?php

function getAllUsers()
{
    global $conn;
    $sql = "SELECT user_id, username, email, role, created_at FROM users";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

$allUsers = getAllUsers();
?>

<div>
    <div>
        <h2>Admin Dashboard</h2>
        <p>Velkommen, <?php echo htmlspecialchars($userProfile['username']); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile['email']); ?></p>
        <br>


        <!-- gjør det mulig å redigere infoen under, Muligens slette bruker og redigere bruker, søke etter brukere -->
        <!-- Rydde opp i css slik at det ser fint ut -->

        <h3>All Users</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allUsers as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
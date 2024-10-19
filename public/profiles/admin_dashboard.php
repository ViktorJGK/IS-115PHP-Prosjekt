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

// Oppdatering av brukerdata når skjema sendes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel'])) {
        // Hvis avbrytknappen ble trykket, gjør ingenting og last siden på nytt
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['user_id'])) {
        // Hent data fra POST for å oppdatere brukeren
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];

        // Oppdatering av brukerdata i databasen
        $sql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $username, $role, $user_id);
        $stmt->execute();

        // Omdiriger tilbake etter oppdatering
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Variabel som bestemmer hvilken bruker som er i redigeringsmodus
$edit_user_id = isset($_POST['edit_user_id']) ? $_POST['edit_user_id'] : null;


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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allUsers as $user): ?>
                    <?php if ($edit_user_id == $user['user_id']): ?>
                        <!-- Redigeringsmodus -->
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
                                    <button type="submit">Lagre</button>
                                    <button type="submit" name="cancel" value="1">Avbryt</button>
                                </td>
                            </form>
                        </tr>
                    <?php else: ?>
                        <!-- Visningsmodus -->
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <!-- Form med "Rediger"-knappen -->
                                <form action="" method="post">
                                    <input type="hidden" name="edit_user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                    <button type="submit">Rediger</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php

function getAllUsers() {
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
        <h2>Din Hjemme Side</h2>
        <p>Velkommen, <?php echo htmlspecialchars($userProfile['username']); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile['email']); ?></p>
        <!-- Add more guest-specific details here -->
    </div>
</div>
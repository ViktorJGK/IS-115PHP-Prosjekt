<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/navbar.css">
    <title>Guest Profile</title>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Hjem</a>
        <a href="register.php">Registrer Bruker</a>
        <a href="login.php">Logg Inn</a>
        <a href="profile.php">Profil</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="#" id="logout" onclick="logoutUser()">Logg Ut</a>
        <?php endif; ?>
    </div>

    <script>
        function logoutUser() {
            window.location.href = '../logout.php';
        }
    </script>
</body>

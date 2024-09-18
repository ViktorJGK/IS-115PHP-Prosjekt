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
            <form id="logout-form" action="../logout.php" method="POST" style="display:inline;">
                <a href="../logout.php" type="submit" id="logout">Logg Ut</button> </a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

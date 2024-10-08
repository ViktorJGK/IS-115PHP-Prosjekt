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
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php">Registrer Bruker</a>
            <a href="login.php">Logg Inn</a>
        <?php else: ?>
            <a href="profile.php">Profil</a>
        <?php endif; ?>



        <?php if (isset($_SESSION['user_id'])): ?>
            <form id="logout-form" action="../logout.php" method="POST" style="display:inline;">
                <a href="../logout.php" type="submit" id="logout">Logg Ut</a>
            </form>
        <?php endif; ?>
    </div>
    <br>
</body>

</html>
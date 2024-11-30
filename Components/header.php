<?php
session_start(); //Kommer til å være med på hver side av prosjektet
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
        <!--- Enkel logikk for navbar/header --> 
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php">Registrer Bruker</a>
            <a href="login.php">Logg Inn</a>
        <?php else: ?>
            <a href="profile.php">Profil</a>
        <?php endif; ?>

        <!--- kan kun se dette hvis inlogget -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form class="navbar" id="logout-form" action="../logout.php" method="POST" style="display:inline;"> <!--- Fører bruker til logout filen som gjør logout logikken -->
                <a href="../logout.php" type="submit" id="logout">Logg Ut</a>
            </form>
        <?php endif; ?>
    </div>
    <br>
</body>

</html>
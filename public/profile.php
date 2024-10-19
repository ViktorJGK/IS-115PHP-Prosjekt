<?php
include '../db_connect.php'; // inkluderer databse koblinmg fil
include '../Components/header.php'; // inkluderer header

function getUserProfile($user_id)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'"; //sql spørring for å hente brukerdata
    $result = $conn->query($sql); //utfører spøring
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

//Initialiserer variabelen $userProfile med null
$userProfile = null;
if (isset($_SESSION['user_id'])) { //sjekker om bruker er innlogget
    $userProfile = getUserProfile($_SESSION['user_id']); // Henter brukerprofilen ved hjelp av bruker-ID fra sesjonen
}

$isAdmin = $userProfile && $userProfile['role'] == 1; // 'role' column indicates admin (1) or guest (0)
?>

<div class="container">
    <div class="profile">
        <!-- Logikk for hvilken profil du skal se -->
        <?php if ($userProfile): ?>
            <?php if ($isAdmin): ?>
                <?php include 'profiles/admin_dashboard.php'; ?>
                <link rel="stylesheet" href="../css/admin.css">
            <?php else: ?>
                <?php include 'profiles/guest_profile.php'; ?>
            <?php endif; ?>
        <?php else: ?>
            <p>Please <a href="login.php">log in</a> to view your profile.</p>
        <?php endif; ?>
    </div>
</div>
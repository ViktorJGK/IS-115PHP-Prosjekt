<?php
include '../db_connect.php'; // Include the database connection
include '../Components/header.php'; // Include the header
include '../Components/functions/user_functions.php'; // Include the file with User, Admin, and Guest classes


$userProfile = null;
if (isset($_SESSION['user_id'])) { // Sjekker innlogging
    $user_id = $_SESSION['user_id'];

    // Initialize user object
    $userProfile = new User($user_id, $conn); // Laster data fro bruker klassen

    // Henter bruker rollen
    $userRole = $userProfile->getRole();

    // Initialize the user as Admin or Guest based on the role
    if ($userRole === 1) {
        $userProfile = new Admin($user_id, $conn); // Admin hvis role = 1
    } elseif ($userRole === 0) {
        $userProfile = new Guest($user_id, $conn); // Guest hvis role = 0
        // Hvis ukjent rolle sendt til utlogging
        header('Location: login.php');
        exit();
    }
}

$isAdmin = $userProfile instanceof Admin; // Sjekker om bruker er admin
?>
<div class="container">
    <div class="profile">
        <?php

        //hvis bruker ikke er innlogget send til innloggins siden
        if (!$userProfile) {
            // Redirect guests to login
            header('Location: login.php');
            exit();
        }
        
        if ($isAdmin) {
            include "profiles/admin_dashboard.php";
        } else {
            include "profiles/guest_profile.php";
            exit();
        }
        ?>

    </div>
</div>
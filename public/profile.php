<?php
include '../db_connect.php'; // Include the database connection
include '../Components/header.php'; // Include the header
include '../Components/functions/user_functions.php'; // Include the file with User, Admin, and Guest classes

// Initialize $userProfile based on session data
$userProfile = null;

if (isset($_SESSION['user_id'])) { // Check if the user is logged in
    $user_id = $_SESSION['user_id'];

    // Initialize the user object
    $userProfile = new User($user_id, $conn); // Use the User class to load user data

    // Get the user's role using the method from the User class
    $userRole = $userProfile->getRole();

    // Initialize the user as Admin or Guest based on the role
    if ($userRole === 1) {
        $userProfile = new Admin($user_id, $conn); // Instantiate Admin if role is 1
    } elseif ($userRole === 0) {
        $userProfile = new Guest($user_id, $conn); // Instantiate Guest if role is 0
    } else {
        // Handle unknown roles or errors
        header('Location: login.php');
        exit();
    }
}

$isAdmin = $userProfile instanceof Admin; // Check if the user is an Admin
?>
<div class="container">
    <div class="profile">
        <!-- Logic for which profile to display -->
        <?php
        if (!$userProfile) {
            // Redirect guests to login
            header('Location: login.php');
            exit();
        }
        
        if ($isAdmin) {
            // Redirect admins to the admin dashboard
            include "profiles/admin_dashboard.php";
        } else {
            // Redirect guests to their profile
            include "profiles/guest_profile.php";
            exit();
        }
        ?>

    </div>
</div>
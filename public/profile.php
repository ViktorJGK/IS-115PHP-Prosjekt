<?php
session_start(); // Start the session

// Inkluderer databasetilkoblingen
include '../db_connect.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inkluderer header-komponenten, som sannsynligvis inneholder toppseksjonen for nettsiden
include '../Components/header.php';

// Funksjon for å hente brukerprofil basert på bruker-ID
function getUserProfile($user_id) {
    global $conn; // Gjør databasetilkoblingen tilgjengelig i funksjonen
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Returnerer raden som en assosiativ array
    } else {
        return null; // Returnerer null hvis brukeren ikke finnes
    }
}

// Initialiserer brukerprofil som null
$userProfile = null;

// Sjekker om bruker-ID finnes i økten
if (isset($_SESSION['user_id'])) {
    // Henter brukerprofil basert på bruker-ID
    $userProfile = getUserProfile($_SESSION['user_id']);
}

// Sjekker om brukeren er administrator (rolleverdi 1)
$isAdmin = $userProfile && $userProfile['role'] == 1; // 'role'-kolonnen angir admin (1) eller gjest (0)
?>

<div class="container"> <!-- Hovedcontainer for profilseksjonen -->
    <div class="profile"> <!-- Seksjon for profilinnhold -->
        <?php if ($userProfile): ?> <!-- Sjekker om brukerprofilen er tilgjengelig -->
            <?php if ($isAdmin): ?> <!-- Hvis brukeren er administrator -->
                <?php include 'profiles/admin_dashboard.php'; ?> <!-- Inkluderer admin-dashboard -->
            <?php else: ?> <!-- Hvis brukeren er en gjest -->
                <?php include 'profiles/guest_profile.php'; ?> <!-- Inkluderer gjesteprofil -->
            <?php endif; ?>
        <?php else: ?> <!-- Hvis brukerprofilen ikke er tilgjengelig -->
            <p>Please <a href="login.php">log in</a> to view your profile.</p> <!-- Oppfordrer til innlogging -->
        <?php endif; ?>
    </div>
</div>
<?php 
function getBookings($user_id) {
    global $conn;
    $sql = "SELECT * FROM bookings WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Hent bookinger for brukeren
$bookings = [];
if (isset($_SESSION['user_id'])) {
    $bookings = getBookings($_SESSION['user_id']);
}




?>
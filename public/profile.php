<?php
// Inkluderer databasetilkoblingen
include '../db_connect.php';

// Inkluderer header-komponenten, som sannsynligvis inneholder toppseksjonen for nettsiden
include '../Components/header.php';

// Inkluderer en statisk HTML-side som kan inneholde en mal for brukerprofil
include '../profile.html';

// Funksjon for å hente brukerprofil basert på bruker-ID
function getUserProfile($user_id) {
    global $conn; // Gjør databasetilkoblingen tilgjengelig i funksjonen

    // SQL-spørring for å hente alle detaljer om brukeren med gitt ID
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";

    // Utfører spørringen
    $result = $conn->query($sql);

    // Sjekker om det finnes en rad i resultatet
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

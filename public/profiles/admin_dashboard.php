<?php
// Funksjon for å hente alle brukere fra databasen
function getAllUsers()
{
    // Bruker den globale databaseforbindelsen
    global $conn;

    // SQL-spørring for å hente brukerdata
    $sql = "SELECT user_id, username, email, role, created_at FROM users";

    // Utfør spørringen og lagre resultatet
    $result = $conn->query($sql);

    // Sjekker om det finnes rader i resultatet
    if ($result->num_rows > 0) {
        // Returnerer alle resultater som en assosiativ array
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Returnerer en tom array hvis ingen brukere finnes
        return [];
    }
}

// Henter alle brukere ved å kalle funksjonen
$allUsers = getAllUsers();
?>

<div>
    <div>
        <!-- Overskrift for administrasjonspanelet -->
        <h2>Admin Dashboard</h2>

        <!-- Viser personlig informasjon om den innloggede brukeren -->
        <p>Velkommen, <?php echo htmlspecialchars($userProfile['username']); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile['email']); ?></p>
        <br>

        <!-- Kommentarer om videre arbeid:
             - Gjør det mulig å redigere brukerinfoen nedenfor
             - Legg til funksjonalitet for å slette brukere og søke etter brukere
             - Rydd opp i CSS for bedre visuell presentasjon -->

        <!-- Seksjon for å vise alle brukere -->
        <h3>All Users</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th> <!-- Brukerens unike ID -->
                    <th>Username</th> <!-- Brukernavn -->
                    <th>Email</th> <!-- E-postadresse -->
                    <th>Role</th> <!-- Brukerens rolle (Admin eller Gjest) -->
                    <th>Created At</th> <!-- Når brukeren ble opprettet -->
                </tr>
            </thead>
            <tbody>
                <!-- Itererer gjennom alle brukere og viser informasjon -->
                <?php foreach ($allUsers as $user): ?>
                    <tr>
                        <!-- Sikrer at dataene er trygge ved å bruke htmlspecialchars -->
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

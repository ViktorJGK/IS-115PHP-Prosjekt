<?php
// Henter alle brukere ved å bruke metoden fra Admin-klassen
$allUsers = $userProfile->getAllUsers();

// Håndterer innsending av skjema for å lagre eller kansellere redigering av brukerdata
$edit_user_id = isset($_POST['edit_user_id']) ? $_POST['edit_user_id'] : null;  // Sjekker om en bruker skal redigeres
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Sjekker om det er et POST-forespørsel (skjema blir sendt)
    // Hvis brukeren trykker på "cancel", går den tilbake til samme side uten å gjøre endringer
    if (isset($_POST['cancel'])) {
        header('Location: ' . $_SERVER['PHP_SELF']);  // Sender brukeren tilbake til samme side
        exit;
    }

    // Hvis skjemaet er sendt med nødvendige data, oppdateres brukeren
    if (isset($_POST['username'], $_POST['role'], $_POST['user_id'])) {
        $username = $_POST['username'];  // Henter det nye brukernavnet
        $role = (int) $_POST['role'];  // Henter og konverterer brukerens rolle (1 = Admin, 0 = Gjest)
        $user_id = (int) $_POST['user_id'];  // Henter brukerens ID
        $userProfile->updateUser($user_id, $username, $role);  // Kaller på funksjon for å oppdatere brukeren i databasen
        header('Location: ' . $_SERVER['PHP_SELF']);  // Etter oppdatering, send brukeren tilbake til samme side
        exit;
    }
}
?>

<h3>All Users</h3>  <!-- Tittel for tabellen -->
<table>
    <thead>
        <tr>
            <!-- Overskriftene på tabellen -->
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- Løkke for å vise alle brukerne i tabellen -->
        <?php foreach ($allUsers as $user): ?>
            <?php if ($edit_user_id == $user['user_id']): ?>  <!-- Hvis vi redigerer denne brukeren, vis et skjema for redigering -->
                <tr>
                    <form action="" method="post">
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>  <!-- Viser brukerens ID -->
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></td>  <!-- Redigerbart felt for brukernavn -->
                        <td><?php echo htmlspecialchars($user['email']); ?></td>  <!-- Viser brukerens e-post -->
                        <td>
                            <select name="role">  <!-- Dropdown for å velge brukerens rolle -->
                                <option value="1" <?php echo $user['role'] == 1 ? 'selected' : ''; ?>>Admin</option>  <!-- Velg Admin hvis rollen er 1 -->
                                <option value="0" <?php echo $user['role'] == 0 ? 'selected' : ''; ?>>Guest</option>  <!-- Velg Gjest hvis rollen er 0 -->
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>  <!-- Viser når brukeren ble opprettet -->
                        <td>
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">  <!-- Skjult felt for brukerens ID -->
                            <button type="submit">Save</button>  <!-- Lagre endringene -->
                            <button type="submit" name="cancel" value="1">Cancel</button>  <!-- Avbryt redigeringen -->
                        </td>
                    </form>
                </tr>
            <?php else: ?>  <!-- Hvis vi ikke redigerer denne brukeren, vises bare brukerdataene -->
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>  <!-- Viser brukerens ID -->
                    <td><?php echo htmlspecialchars($user['username']); ?></td>  <!-- Viser brukernavnet -->
                    <td><?php echo htmlspecialchars($user['email']); ?></td>  <!-- Viser e-posten -->
                    <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>  <!-- Viser "Admin" eller "Guest" avhengig av rollen -->
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>  <!-- Viser når brukeren ble opprettet -->
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="edit_user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">  <!-- Skjult felt for brukerens ID -->
                            <button type="submit">Edit</button>  <!-- Knapp for å redigere brukeren -->
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

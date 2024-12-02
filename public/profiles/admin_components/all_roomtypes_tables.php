<?php
// Behandle skjema for oppdatering av romtyper
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Sjekker om skjemaet ble sendt med POST-metoden
    if (isset($_POST['update_roomtype'], $_POST['room_type_id'], $_POST['type_name'], $_POST['description'], $_POST['max_adults'], $_POST['max_children'])) {
        // Henter og sikrer data fra skjemaet
        $room_type_id = (int)$_POST['room_type_id']; // Romtype-ID
        $type_name = $_POST['type_name']; // Navn på romtypen
        $description = $_POST['description']; // Beskrivelse av romtypen
        $max_adults = (int)$_POST['max_adults']; // Maks voksne tillatt
        $max_children = (int)$_POST['max_children']; // Maks barn tillatt

        // Oppdaterer romtypen i databasen
        $stmt = $conn->prepare("UPDATE room_types 
                                SET type_name = ?, description = ?, max_adults = ?, max_children = ? 
                                WHERE room_type_id = ?");
        $stmt->bind_param("ssiii", $type_name, $description, $max_adults, $max_children, $room_type_id);

        // Utfører oppdateringen og håndterer feil
        if (!$stmt->execute()) {
            die("Error updating room type: " . $stmt->error); // Stopper og viser feil hvis oppdatering mislykkes
        }
    }
}
?>

<h3>All Room Types</h3>
<table>
    <thead>
        <tr>
            <th>Room Type</th>
            <th>Description</th>
            <th>Max Adults</th>
            <th>Max Children</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Sjekker om en romtype er under redigering
        $edit_roomtype_id = isset($_POST['edit_roomtype_id']) ? (int)$_POST['edit_roomtype_id'] : null;

        // Henter alle romtyper fra databasen
        $sql = "SELECT * FROM room_types";
        $result = $conn->query($sql);

        if ($result->num_rows > 0): // Sjekker om det finnes romtyper
            while ($row = $result->fetch_assoc()): // Går gjennom hver rad (romtype)
                if ($edit_roomtype_id == $row['room_type_id']): ?>
                    <!-- Skjema for redigering av en romtype -->
                    <tr>
                        <form action="" method="post">
                            <td><input type="text" name="type_name" value="<?php echo htmlspecialchars($row['type_name']); ?>"></td>
                            <td><input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>"></td>
                            <td><input type="number" name="max_adults" value="<?php echo htmlspecialchars($row['max_adults']); ?>" min="1"></td>
                            <td><input type="number" name="max_children" value="<?php echo htmlspecialchars($row['max_children']); ?>" min="0"></td>
                            <td>
                                <input type="hidden" name="room_type_id" value="<?php echo htmlspecialchars($row['room_type_id']); ?>">
                                <button type="submit" name="update_roomtype">Save</button>
                                <button type="submit" name="cancel_edit_roomtype" value="1">Cancel</button>
                            </td>
                        </form>
                    </tr>
                <?php else: ?>
                    <!-- Viser romtypeinformasjon når den ikke redigeres -->
                    <tr>
                        <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_adults']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_children']); ?></td>
                        <td>
                            <!-- Skjema for å aktivere redigering -->
                            <form action="" method="post">
                                <input type="hidden" name="edit_roomtype_id" value="<?php echo htmlspecialchars($row['room_type_id']); ?>">
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                <?php endif;
            endwhile;
        else: ?>
            <!-- Vises hvis det ikke finnes noen romtyper -->
            <tr>
                <td colspan="5">No room types found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

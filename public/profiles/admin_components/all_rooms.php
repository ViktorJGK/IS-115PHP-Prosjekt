<h3>All Rooms</h3>
<table>
    <thead>
        <tr>
            <th>Room ID</th>
            <th>Room Number</th>
            <th>Type</th>
            <th>Available</th>
            <th>Unavailable From</th>
            <th>Unavailable To</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Henter en liste over alle rom ved å bruke metoden 'getAllRooms' fra $userProfile
        $allRooms = $userProfile->getAllRooms();

        // Går gjennom alle rom som ble hentet
        foreach ($allRooms as $room): ?>
            <tr>
                <!-- Viser detaljene for hvert rom -->
                <td><?php echo htmlspecialchars($room['room_id']); ?></td> <!-- Rom ID -->
                <td><?php echo htmlspecialchars($room['room_number']); ?></td> <!-- Romnummer -->
                <td><?php echo htmlspecialchars($room['type_name']); ?></td> <!-- Typen rom (f.eks. enkeltrom, suite) -->
                <td><?php echo $room['is_available'] ? 'Yes' : 'No'; ?></td> <!-- Tilgjengelighet (Ja/Nei) -->
                <td><?php echo $room['unavailable_from'] ?: 'N/A'; ?></td> <!-- Når rommet ikke er tilgjengelig fra -->
                <td><?php echo $room['unavailable_to'] ?: 'N/A'; ?></td> <!-- Når rommet ikke er tilgjengelig til -->
                <td>
                    <!-- Skjema for å oppdatere informasjon om rommet -->
                    <form action="" method="post">
                        <!-- Skjult felt for å lagre rom-ID -->
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                        
                        <!-- Velg om rommet er tilgjengelig eller ikke -->
                        <label for="is_available">Available:</label>
                        <select name="is_available">
                            <option value="1" <?php echo $room['is_available'] == 1 ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo $room['is_available'] == 0 ? 'selected' : ''; ?>>No</option>
                        </select><br>
                        
                        <!-- Angi fra hvilken dato rommet er utilgjengelig -->
                        <label for="unavailable_from">Unavailable From:</label>
                        <input type="date" name="unavailable_from" value="<?php echo htmlspecialchars($room['unavailable_from']); ?>"><br>
                        
                        <!-- Angi til hvilken dato rommet er utilgjengelig -->
                        <label for="unavailable_to">Unavailable To:</label>
                        <input type="date" name="unavailable_to" value="<?php echo htmlspecialchars($room['unavailable_to']); ?>"><br>
                        
                        <!-- Knapp for å oppdatere romdetaljene -->
                        <button type="submit" name="update_room">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

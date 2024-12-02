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
                $allRooms = $userProfile->getAllRooms();
                foreach ($allRooms as $room): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($room['room_id']); ?></td>
                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($room['type_name']); ?></td>
                        <td><?php echo $room['is_available'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $room['unavailable_from'] ?: 'N/A'; ?></td>
                        <td><?php echo $room['unavailable_to'] ?: 'N/A'; ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                                <label for="is_available">Available:</label>
                                <select name="is_available">
                                    <option value="1" <?php echo $room['is_available'] == 1 ? 'selected' : ''; ?>>Yes</option>
                                    <option value="0" <?php echo $room['is_available'] == 0 ? 'selected' : ''; ?>>No</option>
                                </select><br>
                                <label for="unavailable_from">Unavailable From:</label>
                                <input type="date" name="unavailable_from" value="<?php echo htmlspecialchars($room['unavailable_from']); ?>"><br>
                                <label for="unavailable_to">Unavailable To:</label>
                                <input type="date" name="unavailable_to" value="<?php echo htmlspecialchars($room['unavailable_to']); ?>"><br>
                                <button type="submit" name="update_room">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
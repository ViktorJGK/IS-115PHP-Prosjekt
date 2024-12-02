<?php
// Handle form submissions for updating room types
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_roomtype'], $_POST['room_type_id'], $_POST['type_name'], $_POST['description'], $_POST['max_adults'], $_POST['max_children'])) {
        $room_type_id = (int)$_POST['room_type_id'];
        $type_name = $_POST['type_name'];
        $description = $_POST['description'];
        $max_adults = (int)$_POST['max_adults'];
        $max_children = (int)$_POST['max_children'];

        $stmt = $conn->prepare("UPDATE room_types 
                                SET type_name = ?, description = ?, max_adults = ?, max_children = ? 
                                WHERE room_type_id = ?");
        $stmt->bind_param("ssiii", $type_name, $description, $max_adults, $max_children, $room_type_id);

        if (!$stmt->execute()) {
            die("Error updating room type: " . $stmt->error);
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
        // Check if a room type is being edited
        $edit_roomtype_id = isset($_POST['edit_roomtype_id']) ? (int)$_POST['edit_roomtype_id'] : null;

        // Fetch all room types
        $sql = "SELECT * FROM room_types";
        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                if ($edit_roomtype_id == $row['room_type_id']): ?>
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
                    <tr>
                        <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_adults']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_children']); ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="edit_roomtype_id" value="<?php echo htmlspecialchars($row['room_type_id']); ?>">
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                <?php endif;
            endwhile;
        else: ?>
            <tr>
                <td colspan="5">No room types found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


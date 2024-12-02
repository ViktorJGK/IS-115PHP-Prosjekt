<h3>All Roomtypes</h3>
        <?php
        // Check if the room type is being edited
        $edit_roomtype_id = isset($_POST['edit_roomtype_id']) ? (int)$_POST['edit_roomtype_id'] : null;

        // Fetch all room types
        $sql = "SELECT * FROM room_types";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>
            <tr>
                <th>Room Type</th>
                <th>Description</th>
                <th>Max Adults</th>
                <th>Max Children</th>
                <th>Actions</th>
            </tr>";

            // Loop through room types and display them
            while ($row = $result->fetch_assoc()) {
                if ($edit_roomtype_id == $row['room_type_id']) {
                    // Edit form
                    echo "<tr>
                    <form action='' method='post'>
                        <td><input type='text' name='type_name' value='" . htmlspecialchars($row['type_name']) . "'></td>
                        <td><input type='text' name='description' value='" . htmlspecialchars($row['description']) . "'></td>
                        <td><input type='number' name='max_adults' value='" . htmlspecialchars($row['max_adults']) . "' min='1'></td>
                        <td><input type='number' name='max_children' value='" . htmlspecialchars($row['max_children']) . "' min='0'></td>
                        <td>
                            <input type='hidden' name='room_type_id' value='" . htmlspecialchars($row['room_type_id']) . "'>
                            <button type='submit' name='update_roomtype'>Save</button>
                            <button type='submit' name='cancel_edit_roomtype' value='1'>Cancel</button>
                        </td>
                    </form>
                </tr>";
                } else {
                    // Display room type in normal view
                    echo "<tr>
                    <td>" . htmlspecialchars($row['type_name']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['max_adults']) . "</td>
                    <td>" . htmlspecialchars($row['max_children']) . "</td>
                    <td>
                        <form action='' method='post' style='display:inline-block;'>
                            <input type='hidden' name='edit_roomtype_id' value='" . htmlspecialchars($row['room_type_id']) . "'>
                            <button type='submit'>Edit</button>
                        </form>
                    </td>
                </tr>";
                }
            }
            echo "</table>";
        } else {
            echo "No room types found.";
        }
        ?>

        <?php
        // Handle form submissions for updating room type
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Handle room type update
            if (isset($_POST['update_roomtype'], $_POST['room_type_id'], $_POST['type_name'], $_POST['description'], $_POST['max_adults'], $_POST['max_children'])) {
                $room_type_id = (int)$_POST['room_type_id'];
                $type_name = $_POST['type_name'];
                $description = $_POST['description'];
                $max_adults = (int)$_POST['max_adults'];
                $max_children = (int)$_POST['max_children'];

                // Update the room type in the database
                $stmt = $conn->prepare("UPDATE room_types 
                                SET type_name = ?, description = ?, max_adults = ?, max_children = ? 
                                WHERE room_type_id = ?");
                $stmt->bind_param("ssiii", $type_name, $description, $max_adults, $max_children, $room_type_id);

                if ($stmt->execute()) {
                } else {
                    die("Error updating room type: " . $stmt->error);
                }
            }
        }
        ?>
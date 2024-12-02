<?php
ob_start(); // Start output buffering

include_once '../Components/functions/user_functions.php';

// Start session and ensure user authentication
if (!isset($userProfile) || !$userProfile instanceof Admin) {
    header("Location: ../../logout.php");
    exit;
}


ob_end_flush(); // Send all output
?>


<html lang="en">

<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div>
        <div>
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p>
            <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p>
            <br>

            <?php
            include 'admin_components/all_user_tables.php';
            echo "<br>";
            ?>

            <?php
            include 'admin_components/all_booking_tables.php';
            echo "<br>";
            ?>

            <?php
            include 'admin_components/all_roomtypes_tables.php';
            echo "<br>";
            ?>

            <?php 
            include 'admin_components/all_rooms.php';
            echo "<br>";
            ?>


        </div>

        </tr>
        </tbody>
        </table>






    </div>
</body>

</html>
<?php

//For error handlinger direkte i nettleser
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    include "../Components/header.php";
    //include '../db_connect.php';
  
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/index.css">
    <title>Rombooking System</title>
</head>
<body>
    <div class="container">
        <h1>Velkommen til vårt motell</h1>
        <h2>Søk etter tilgjengelige rom</h2>
        <form method="post" action="available_rooms.php">
            <label for="check_in">Innsjekkingsdato:</label>
            <input type="date" id="check_in" name="check_in" required><br><br>
            
            <label for="check_out">Utsjekkingsdato:</label>
            <input type="date" id="check_out" name="check_out" required><br><br>
            
            <label for="adults">Antall voksne:</label>
            <input type="number" id="adults" name="adults" min="1" required><br><br>
            
            <label for="children">Antall barn:</label>
            <input type="number" id="children" name="children" min="0" required><br><br>
            
            <input type="submit" value="Søk">
        </form>
    </div>
</body>
</html>

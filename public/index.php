<!DOCTYPE html>
<html>
<head>
    <title>Rombooking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="password"],
        input[type="date"],
        input[type="number"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Hjem</a>
        <a href="register.php">Registrer Bruker</a>
        <a href="login.php">Logg Inn</a>
        <a href="profile.php">Profil</a>
    </div>
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

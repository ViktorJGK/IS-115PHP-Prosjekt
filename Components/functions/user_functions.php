<?php

// Klassen User representerer en bruker i systemet
class User
{
    // Beskytter brukerens detaljer og databaseforbindelse
    protected $user_id; // ID for brukeren
    protected $username; // Brukernavn
    protected $email; // E-postadresse
    protected $role; // Brukerens rolle (for eksempel admin eller gjest)
    protected $created_at; // Opprettelsesdato for brukeren
    protected $conn; // Databaseforbindelse

    // Konstruktør som initierer brukerens ID og laster inn brukerdata fra databasen
    public function __construct($user_id, $conn)
    {
        $this->user_id = $user_id; // Setter brukerens ID
        $this->conn = $conn; // Setter databaseforbindelsen
        $this->loadUserData(); // Laster inn brukerdata fra databasen
    }

    // Henter brukerdata fra databasen basert på brukerens ID
    protected function loadUserData()
    {
        $sql = "SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql); // Forbereder SQL-spørringen
        $stmt->bind_param("i", $this->user_id); // Binder brukerens ID som parameter
        $stmt->execute(); // Utfører SQL-spørringen
        $result = $stmt->get_result()->fetch_assoc(); // Henter resultatet som en assosiativ array

        // Hvis brukerdata finnes, oppdateres objektets egenskaper
        if ($result) {
            $this->username = $result['username'];
            $this->email = $result['email'];
            $this->role = $result['role'];
            $this->created_at = $result['created_at'];
        }
    }

    // Henter alle rom og deres informasjon fra databasen
    public function getAllRooms()
    {
        global $conn;
        $sql = "SELECT room_id, room_number, type_name, is_available, unavailable_from, unavailable_to 
        FROM rooms 
        JOIN room_types ON rooms.room_type_id = room_types.room_type_id";
        $result = $conn->query($sql); // Utfører SQL-spørringen
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : []; // Returnerer romdata som en liste
    }

    // Henter alle romtyper fra databasen
    public function getRoomTypes()
    {
        global $conn;
        $result = $conn->query("SELECT type_name FROM room_types");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Henter alle bestillinger fra databasen med en grense og et offset
    public function getAllBookings($limit, $offset)
    {
        global $conn;
        $sql = "SELECT bookings.booking_id, bookings.check_in, bookings.check_out, 
                       users.username, room_types.type_name AS room_type 
                FROM bookings 
                JOIN rooms ON bookings.room_id = rooms.room_id 
                JOIN room_types ON rooms.room_type_id = room_types.room_type_id 
                JOIN users ON bookings.user_id = users.user_id
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql); // Forbereder SQL-spørringen
        if (!$stmt) {
            die("SQL error: " . $conn->error);
        }

        $stmt->bind_param("ii", $limit, $offset); // Binder parametrene for grense og offset
        if (!$stmt->execute()) {
            die("Execution error: " . $stmt->error);
        }
        $result = $stmt->get_result();

        // Returnerer resultatet som en liste med bestillinger eller en tom liste
        return $result && $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Henter brukerens egne bestillinger
    public function getUserBookings()
    {
        // SQL-spørring for å hente detaljer om brukerens bestillinger
        $sql = "
            SELECT 
                b.booking_id, 
                b.check_in, 
                b.check_out, 
                r.room_number, 
                rt.type_name AS room_type, 
                r.is_available 
            FROM bookings b
            JOIN rooms r ON b.room_id = r.room_id
            JOIN room_types rt ON r.room_type_id = rt.room_type_id
            WHERE b.user_id = ?
        ";

        // Forbereder og utfører SQL-spørringen
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id); // Binder brukerens ID som parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Returnerer resultatet som en liste med bestillinger
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Returnerer brukerens brukernavn
    public function getUsername()
    {
        return $this->username;
    }

    // Returnerer brukerens e-postadresse
    public function getEmail()
    {
        return $this->email;
    }

    // Returnerer brukerens rolle
    public function getRole()
    {
        return $this->role;
    }

    // Returnerer brukerens ID
    public function getId()
    {
        return $this->user_id;
    }
}

// Klassen Admin utvider User med ekstra funksjonalitet for administratorer
class Admin extends User
{
    // Henter en liste over alle brukere i systemet
    public function getAllUsers()
    {
        $sql = "SELECT user_id, username, email, role, created_at FROM users";
        $result = $this->conn->query($sql);

        // Returnerer en liste med brukere eller en tom liste
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    // Oppdaterer en brukers brukernavn og rolle
    public function updateUser($user_id, $username, $role)
    {
        $sql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $username, $role, $user_id); // Binder parametrene for oppdatering
        $stmt->execute();
    }
}

// Klassen Guest utvider User og kan inneholde spesifikke funksjoner for gjester
class Guest extends User
{

}

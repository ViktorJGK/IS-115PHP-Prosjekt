<?php

class User {
    protected $user_id;
    protected $username;
    protected $email;
    protected $role;
    protected $created_at;
    protected $conn;

    public function __construct($user_id, $conn) {
        $this->user_id = $user_id;
        $this->conn = $conn;
        $this->loadUserData();
    }

    // Load user data from the database based on user_id
    protected function loadUserData() {
        $sql = "SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $this->username = $result['username'];
            $this->email = $result['email'];
            $this->role = $result['role'];
            $this->created_at = $result['created_at'];
        }
    }

    public function getUserBookings() {
        // SQL query to join the bookings table with rooms and room_types to fetch detailed information
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
        
        // Prepare and execute the statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id); // Bind user_id as an integer parameter
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch all bookings with room number, room type, and availability status
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function getId() {
        return $this->user_id;
    }
}

class Admin extends User {
    public function getAllUsers() {
        $sql = "SELECT user_id, username, email, role, created_at FROM users";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function updateUser($user_id, $username, $role) {
        $sql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $username, $role, $user_id);
        $stmt->execute();
    }
}

class Guest extends User {
    // You can add any guest-specific functionality here
}

?>

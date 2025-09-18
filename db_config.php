<?php
$mysqli = new mysqli("localhost", "root", "", "cup_board");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Add admin check function with existence check
if (!function_exists('isAdmin')) {
    function isAdmin($mysqli, $user_id) {
        $stmt = $mysqli->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['is_admin'] == 1;
        }
        return false;
    }
}
?>

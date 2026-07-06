<?php
$conn = new mysqli("localhost", "root", "", "cashier");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT id, username, password FROM users");
$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
echo json_encode($users);
$conn->close();
?>

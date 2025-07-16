<?php
echo "Starting test...\n";

$servername = "localhost";
$username = "debian-sys-maint";
$password = "jlJjmoQYEmOhR2mJ";
$dbname = "file_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection successful.\n";

$conn->close();
echo "Test finished.\n";
?>

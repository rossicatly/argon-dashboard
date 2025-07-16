<?php
function get_db_connection() {
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

    return $conn;
}
?>

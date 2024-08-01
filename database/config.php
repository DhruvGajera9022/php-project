<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "dbuser";

// Create Connection
$conn = new mysqli($servername, $username, $password, $database);

// Check Connection
if ($conn->connect_error) { 
    die("Connection failure: " . $conn->connect_error); 
    exit();
}

// $sqlCreateTable = "CREATE TABLE tbluser(
// id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
// name VARCHAR(30) NOT NULL,
// email VARCHAR(30) NOT NULL,
// password VARCHAR(10) NOT NULL
// )";

// //Table Creattion:
// if ($conn->query($sqlCreateTable) === TRUE) {
//     echo "Table MyGuests created successfully";
// } else {
//     echo "Error creating table: " . $conn->error;
// }

?>
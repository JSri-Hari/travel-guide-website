<?php
session_start();

// Ensure POST variables are set, and initialize them with default empty values
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Check if any login fields are empty
if(empty($email) || empty($password)){
    echo '<script>alert("Fields should not be empty");</script>';
    echo '<script>window.location.href="signin.html";</script>';
}

// Establish database connection
$conn = new mysqli('localhost:8080', 'root', '', 'user'); // Assuming default port for MySQL

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute query to check if email and password match
$checkStmt = $conn->prepare("SELECT * FROM registration WHERE email = ? AND password = ?");
$checkStmt->bind_param("ss", $email, $password);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

// If email and password match, start a session and redirect to the home page
if ($checkResult->num_rows > 0) {
    $_SESSION['email'] = $email;
    echo '<script>alert("Login Successful");</script>';
    echo '<script>window.location.href="index.html";</script>';
    exit(); // Exit script execution after redirecting
} else {
    echo '<script>alert("Invalid Email or Password");</script>';
    echo '<script>window.location.href="signin.html";</script>';
}
// Close prepared statements and database connection
$checkStmt->close();
$conn->close();
?> 
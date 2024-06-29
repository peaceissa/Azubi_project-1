<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    if ($username == "admin" && $password == "admin123") {
        echo "Welcome, This is admin!";
    } else {
        echo "Invalid username or password";
    }
}
?>
 

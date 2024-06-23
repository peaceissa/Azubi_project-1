<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Process the login here (e.g., check credentials in a database)
    if ($username == 'admin' && $password == 'admin123') {
        echo 'Welcome admin! ';
    } else {
        echo 'Invalid username or password';
    }
 }
 ?>
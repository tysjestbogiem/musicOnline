<?php
// start session if not already active to track user login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include database connection and functions
require_once 'myConnect.php';
require_once 'php/functions.php';

// check if the form has been submitted with both username and password
if (empty($_POST['username']) || empty($_POST['password'])) {
    $_SESSION['error'] = 'Please fill both the username and password fields!';
    header("Location: login.php"); // redirect user back to login page if fields are empty
    exit();
}

// sanitise user input to prevent malicious code injection
$username = trim($_POST['username']);
$password = trim($_POST['password']); // trim password to remove leading/trailing spaces

// prepare SQL statement to prevent SQL injection
if (!$stmt = $dbConnect->prepare('SELECT userID, password, role FROM userInfo WHERE username = ?')) {
    die("Database error: " . $dbConnect->error); // stop execution if there's a database error
}

// bind parameters to the SQL statement
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

// check if a user exists with the provided username
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    // verify the provided password against the hashed password in the database
    if (password_verify($password, $hashed_password)) {
        session_regenerate_id(true); // security measure: prevent session fixation attacks

        // store user details in session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['userID'] = $id;
        $_SESSION['role'] = $role;

        // if the user is an admin, store their admin ID separately
        if ($role === 'admin') {
            $_SESSION['adminID'] = $id;
        }

        unset($_SESSION['error']); // clear any previous error messages

        // redirect to home page after successful login
        header("Location: home.php");
        exit();
    } else {
        // incorrect password entered
        $_SESSION['error'] = 'Incorrect username or password!';
        echo "<script>alert('Invalid username or password');</script>"; // alert message for incorrect login
        header("Location: login.php"); // redirect back to login page
        exit();
    }
}

// if no user was found with the entered username
$_SESSION['error'] = 'Incorrect username or password!';
header("Location: login.php"); // redirect back to login page
exit();
?>

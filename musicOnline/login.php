<?php 
// start session - needed for login handling
session_start();

// include necessary files
require_once "php/functions.php"; 
require_once "includes/nav.php";
require_once 'myConnect.php'; // database connection
require_once 'pageTitle.php';

// check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // check if username and password fields are filled
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $_SESSION['error'] = 'Please fill both the username and password fields!';
        header("Location: login.php"); // send user back to login page
        exit();
    }

    // trim username to remove extra spaces
    $username = trim($_POST['username']);

    // prepare the query to prevent SQL injection
    if (!$stmt = $dbConnect->prepare('SELECT userID, password, role FROM userInfo WHERE username = ?')) {
        die("Database error: " . $dbConnect->error);
    }

    // bind the username parameter and execute the query
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    // check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // verify if the entered password matches the hashed password
        if (password_verify($_POST['password'], $hashed_password)) {
            // regenerate session ID for security
            session_regenerate_id(true);

            // store user details in session
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['userID'] = $id;  
            $_SESSION['role'] = $role;

            // redirect to home page after successful login
            header("Location: home.php");
            exit();
        } else {
            // if password is incorrect, show error message
            $_SESSION['error'] = 'Incorrect username or password!';
            header("Location: login.php");
            exit();
        }
    } else {  
        // if username doesn't exist, show error message
        $_SESSION['error'] = 'User does not exist!';
        header("Location: login.php");
        exit();
    }
}
?>

<body>
    <div class="main-content">
            <main class="middle-page-container">     

            <!-- check if user is logged in -->
            <?php if (!isset($_SESSION['username'])) { ?>
                <div class="login-container">
                    <h2>log in</h2>
                    <p>enter your details below</p>

                    <!-- login form -->
                    <form action="authenticate.php" method="post"> 
                        <input type="text" placeholder="username" name="username" required>
                        <input type="password" placeholder="password" name="password" required>

                        <div class="clickable">
                            <button class="submit-btn-orange" type="submit">log in</button>
                            <span class="psw"><a href="#">forgot password?</a></span>
                        </div>
                    </form>
                </div>

            <?php } else { ?>
                <!-- display welcome message if user is already logged in -->
                <div class="welcome-message">
                    <script>alert('Hello, <?php echo $_SESSION['username']; ?>!!');</script>
                </div>
            <?php } ?>
        </main>
    </div> 

    <!-- include footer -->
    <?php include "includes/footer.php"; ?> 
</body>

<script>
    // show alert if there's an error message stored in the session
    window.onload = function() {
        <?php if (!empty($_SESSION['error'])): ?>
            alert("<?php echo $_SESSION['error']; ?>");
            <?php unset($_SESSION['error']); // clear error message after displaying ?>
        <?php endif; ?>
    };
</script>

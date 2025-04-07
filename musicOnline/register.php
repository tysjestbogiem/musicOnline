<?php 
session_start(); // start session to store errors


require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// set up an empty errors array
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // check if username is entered
    if (!empty($_POST['username'])) {
        $username = trim($_POST['username']);
        $username = mysqli_real_escape_string($dbConnect, $username);
    } else {
        $errors['username'] = 'username is required';
    }

    // check if email is entered and valid
    if (!empty($_POST['email'])) {
        $email = trim($_POST["email"]); // remove extra spaces
        $email = filter_var($email, FILTER_SANITIZE_EMAIL); // remove unwanted characters

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'please enter a valid email address.';
        } else {
            $email = mysqli_real_escape_string($dbConnect, $email);
        }
    } else {
        $errors['email'] = 'email address is required';
    }

    // check if password is entered
    if (!empty($_POST['password'])) {  
        $password = trim($_POST['password']);
    } else {
        $errors['password'] = 'password is required';
    }

    // check if confirm password is entered
    if (!empty($_POST['password2'])) {  
        $password2 = trim($_POST['password2']);
    } else {
        $errors['password2'] = 'password confirmation is required';
    }

    // check if passwords match
    if (!empty($password) && !empty($password2) && $password !== $password2) {
        $errors['password_match'] = "passwords do not match.";
    }

    // check if username or email is already in use
    if (empty($errors)) { 
        $check_user = "SELECT * FROM userInfo WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($dbConnect, $check_user);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $errors['username'] = "username or email already exists!";
        }

        mysqli_stmt_close($stmt);
    }

    // if everything is good, create the new user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user';

        $query = "INSERT INTO userInfo (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbConnect, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $role);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('you have successfully registered! please log in now.');
                    window.location.href = 'login.php'; 
                  </script>";
            exit();
        } else {
            die("error: " . mysqli_error($dbConnect));
        }

    }
}
?>

<body>

    <main class="middle-page-container"> 
        <div class="register-container">
            <h2>register</h2>
            <p>enter your details below</p>

            <!-- show errors if there are any -->
            <?php if (!empty($errors)): ?>
                <div style="color: red;">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <input type="text" placeholder="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                
                <input type="email" placeholder="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                
                <input type="password" placeholder="password" name="password" required>
                
                <input type="password" placeholder="confirm password" name="password2" required>
                
                <button class="submit-btn-orange" type="submit">create account</button>
                
                <div class="clickable-reg">
                    <p>already have an account?</p>
                    <a class="lg" href="login.php">log in</a>
                </div>
            </form>
        </div>
    </main>

<?php include "includes/footer.php"; ?>
</body>

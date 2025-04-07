<?php 
// include necessary files for functions, navigation, and page title
require_once "php/functions.php"; 
require_once 'includes/nav.php';
require_once 'pageTitle.php';

$pageTitle = 'Delete User';
$currentPage = basename($_SERVER['PHP_SELF']);

// ensure an admin is logged in before allowing access
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    die("Error: You must be an admin to delete users."); // stop execution if user is not an admin
}

$adminID = $_SESSION['userID']; // store the logged-in admin's ID

// connect to the database
$mysqli = dbConnect(); 

// validate userID to delete from GET or POST request
if (isset($_GET['userID']) && is_numeric($_GET['userID'])) {
    $userID = $_GET['userID'];
} elseif (isset($_POST['userID']) && is_numeric($_POST['userID'])) {
    $userID = $_POST['userID'];
} else {
    die("<p>Error. Invalid access.</p>"); // prevent invalid access attempts
}

// prevent the admin from deleting their own account
if ($userID == $adminID) {
    die("<p>Error: You cannot delete your own admin account.</p>");
}

// fetch user information to confirm existence
$query = "SELECT username, email, role FROM userInfo WHERE userID = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// check if the user exists before proceeding
if (!$user) {
    echo "<p>Error: User not found.</p>";
    include "includes/footer.php"; // include footer before exiting
    exit();
}

// handle form submission for deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accepted']) && $_POST['accepted'] === 'yes') {
        // delete the user from the database
        $stmt = $mysqli->prepare("DELETE FROM userInfo WHERE userID = ? LIMIT 1");
        $stmt->bind_param("i", $userID);
        $stmt->execute();

        // check if a record was successfully deleted
        if ($stmt->affected_rows === 1) {
            echo "<script>alert('User has been deleted successfully.');
                    window.location.href = 'allUsers.php'; 
                  </script>";
        } else {
            echo "<script>alert('Something went wrong, user was not deleted.')</script>";
            echo '<p>Error: ' . $mysqli->error . '</p>'; // display error if deletion fails
        }

        $stmt->close();
    } else {
        // if deletion is cancelled
        echo "<script>alert('Deletion cancelled. User was not deleted.');
              window.location.href = 'allUsers.php';
              </script>";
    }
}
?>

<body>
<main class="middle-page-container">     

    <div class="delete-box">
        <!-- confirmation message for user deletion -->
        <p>are you sure that you want to delete the following user?<br>
            <strong><?php echo htmlspecialchars($user['username']); ?></strong> (<?php echo htmlspecialchars($user['email']); ?>)
        </p>

        <!-- form to confirm deletion -->
        <form action="deleteUser.php" method="POST" class="delete-form">
            <div class="radio-group">
                <label><input type="radio" name="accepted" value="yes"> yes</label>
                <label><input type="radio" name="accepted" value="no"> no</label>
            </div>

            <input type="hidden" name="userID" value="<?php echo $userID; ?>">
            <input type="submit" name="Submit" value="Submit" class="submit-delete">
        </form>
    </div>

</main>   
</body>

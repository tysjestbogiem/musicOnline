<?php 
// include necessary files for functions, navigation, and database connection
include 'php/functions.php'; 
include "includes/nav.php";
require_once 'pageTitle.php';

$mysqli = dbConnect(); // ensure database connection is established

// define the admin menu for sidebar navigation
$adminMenu = [
    "dashboard" => "admin.php",
    "all users" => "allUsers.php",
    "all vinyls" => "allVinyls.php",
    "edit" => "editAllVinyls.php",
    "all orders" => "allOrders.php",
    "settings" => "musicOnline.php",
    "logout" => "logout.php"
];

// check if userID is provided via GET or POST request
if (isset($_GET['userID'])) {
    $userID = intval($_GET['userID']); // ensure it's an integer for security
} elseif (isset($_POST['userID'])) {
    $userID = intval($_POST['userID']); // userID sent from the form
} else {
    die("error: no user ID provided."); // stop execution if no ID is present
}

// retrieve user details from the database
$query = "SELECT username, email, firstName, surname FROM userInfo WHERE userID=?";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("database error: failed to prepare statement."); // prevent database errors from being displayed
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// check if the user exists
if (!$user) {
    die("error: user not found."); // stop execution if user does not exist
}

$currentPage = basename($_SERVER['PHP_SELF']);

// process form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // ensure userID is present in the form submission
    if (!isset($_POST['userID']) || empty($_POST['userID'])) {
        die("error: no user ID provided.");
    }

    $userID = intval($_POST['userID']); // convert userID to an integer

    // validate form fields
    $firstName = !empty($_POST['firstName']) ? trim($_POST['firstName']) : "";
    $surname = !empty($_POST['surname']) ? trim($_POST['surname']) : "";
    $email = !empty($_POST['email']) ? trim($_POST['email']) : $errors[] = "you need to enter an email address.";

    // proceed with update if there are no validation errors
    if (empty($errors)) {
        // prepare the SQL update query
        $stmt = $mysqli->prepare("UPDATE userInfo SET firstName=?, surname=?, email=? WHERE userID=?");

        if ($stmt) {
            $stmt->bind_param("sssi", $firstName, $surname, $email, $userID);

            // execute query and check if successful
            if ($stmt->execute()) {
                echo '<script>alert("account details updated successfully!");</script>';
            } else {
                echo "<p class='error'>update failed: " . $stmt->error . "</p>";
            }

            $stmt->close(); // close statement
        } else {
            echo '<script>alert("database error: unable to prepare statement.");</script>';
        }
    } else {
        // display validation errors if any
        echo "<p>the following errors occurred:</p>";
        foreach ($errors as $message) {
            echo "$message<br>";
        }
        echo "</p>";
    }
}

// store username for display in form
$username = $user['username'];

// close the database connection
mysqli_close($mysqli);
?>

<body class="<?php echo $bodyClass; ?>">

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>administrator account > all users > edit user</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <!-- loop through admin menu for navigation -->
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($adminMenu as $adMen => $url): 
                            $active = (basename($url) == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $adMen; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>

            <div class="mobile-sidebar">
                <!-- dropdown menu for mobile navigation -->
                <form id="mobile-form" name="mobile-form">
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <?php foreach ($adminMenu as $adMen => $url): 
                            $active = (basename($url) == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $adMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>
        </div>

        <div class="main-content">
            <div class="right"> 
                <!-- form for updating user details -->
                <form action="editUserAdmin.php" method="POST">
                    <!-- hidden input to pass userID -->
                    <input type="hidden" name="userID" value="<?php echo $userID; ?>">

                    <div class="double-box">
                        <div>
                            <label>first name</label>
                            <input type="text" placeholder="first name" name="firstName" 
                                value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>" />
                        </div>
                        <div>
                            <label>surname</label>
                            <input type="text" placeholder="surname" name="surname" 
                                value="<?php echo isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : ''; ?>" />
                        </div>
                    </div>

                    <!-- display-only username field -->
                    <div class="username">
                        <label>user display name</label>
                        <input type="text" placeholder="username" name="username" 
                        value="<?php echo htmlspecialchars($username); ?>" 
                        readonly /> 
                        <p>this is how the username is displayed in account sections and comments, it cannot be changed</p>
                    </div>

                    <!-- account type display-only field -->
                    <div class="account-type">
                        <label>account type</label>
                        <input type="text" placeholder="" name="accountType" value="hobby" readonly />
                    </div>

                    <div class="email-address">
                        <label>email address</label>
                        <input type="text" placeholder="email address" name="email" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                    </div>

                    <div class="submit-btn-container">
                        <button class="submit-btn" type="submit">save details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    

    <?php include "includes/footer.php" ?>

</body>

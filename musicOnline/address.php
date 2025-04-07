<?php 
// include required files for functionality, navigation, and page title
include 'php/functions.php'; 
include "includes/nav.php";
require_once 'pageTitle.php';

// get the current page name to highlight the correct menu item
$currentPage = basename($_SERVER['PHP_SELF']);

// check if the user is logged in, otherwise stop execution
if (!isset($_SESSION['userID'])) {
    die("Error: Unauthorised access."); // stops the script and displays an error
}

// retrieve the logged-in user's ID
$userID = $_SESSION['userID']; 

// menu options available to the user
$buyerMenu = [
    "my orders" => "myOrders.php",
    "address" => "address.php",
    "account details" => "accountDetails.php",
    "logout" => "logout.php"
];

// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // validate first name (required)
    if (!empty($_POST['firstName'])) {
        $firstName = trim($_POST['firstName']);
    } else {
        $errors[] = "You need to enter your first name.";
    }

    // validate surname (required)
    if (!empty($_POST['surname'])) {
        $surname = trim($_POST['surname']);
    } else {
        $errors[] = "You need to enter your surname.";
    }

    // validate street address (required)
    if (!empty($_POST['streetAddress'])) {
        $streetAddress = trim($_POST['streetAddress']);
    } else {
        $errors[] = "You need to enter your street address!";
    }

    // apartment is optional, so set it if provided
    $apartment = isset($_POST['apartment']) ? trim($_POST['apartment']) : "";

    // validate town (required)
    if (!empty($_POST['town'])) {
        $town = trim($_POST['town']);
    } else {
        $errors[] = "You need to enter your town!";
    }

    // validate country (required)
    if (!empty($_POST['country'])) {
        $country = trim($_POST['country']);
    } else {
        $errors[] = "You need to enter your country!";
    }

    // validate postcode (required)
    if (!empty($_POST['postcode'])) {
        $postcode = trim($_POST['postcode']);
    } else {
        $errors[] = "You need to enter your postcode!";
    }

    // validate phone number (optional)
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";

    // validate email (required)
    if (!empty($_POST['email'])) {
        $email = trim($_POST["email"]); // remove extra spaces
        $email = filter_var($email, FILTER_SANITIZE_EMAIL); // remove unwanted characters

        // check if email is in a valid format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        } else {
            // escape email to prevent SQL injection (if needed)
            $email = mysqli_real_escape_string($dbConnect, $email);
        }
    } else {
        $errors[] = "Email address is required.";
    }

    // proceed only if there are no validation errors
    if (empty($errors)) {

        // update user information in the database
        $queryUser = "UPDATE userInfo SET firstName = ?, surname = ?, email = ?, phone = ? WHERE userID = ?";
        $stmtUser = mysqli_prepare($dbConnect, $queryUser);
        mysqli_stmt_bind_param($stmtUser, "ssssi", $firstName, $surname, $email, $phone, $userID);
        $resultUser = mysqli_stmt_execute($stmtUser);

        // insert new address into the address table
        $queryAddress = "INSERT INTO address (streetAddress, apartment, town, country, postcode) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmtAddress = mysqli_prepare($dbConnect, $queryAddress);
        mysqli_stmt_bind_param($stmtAddress, "sssss", $streetAddress, $apartment, $town, $country, $postcode);
        $resultAddress = mysqli_stmt_execute($stmtAddress);

        // get the newly created address ID
        $addressID = mysqli_insert_id($dbConnect); 

        // if address insertion is successful, link it to the user
        if ($resultAddress && $addressID) {
            $queryLink = "INSERT INTO user_address (userID, addressID) VALUES (?, ?)";
            $stmtLink = mysqli_prepare($dbConnect, $queryLink);
            mysqli_stmt_bind_param($stmtLink, "ii", $userID, $addressID);
            $resultLink = mysqli_stmt_execute($stmtLink);
        } else {
            $resultLink = false; // prevents executing user_address insert if address insert failed
            echo '<script>alert("Failed to update user-address table!");</script>';
        }

        // check if all database operations were successful
        if ($resultUser && $resultAddress && $resultLink) {
            echo '<script>alert("Address updated successfully!");</script>';
        } else {
            echo '<script>alert("Failed to update details.");</script>';
            echo mysqli_error($dbConnect); // show database error message
        }

        // close database connections
        mysqli_stmt_close($stmtUser);
        mysqli_stmt_close($stmtAddress);
        mysqli_close($dbConnect);
    } else {
        // show validation errors if any
        echo "<p>The following errors occurred:</p>";
        foreach ($errors as $message) {
            echo "$message<br>";
        }
    }
}
?>

<body>

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>your account > address</p> 
            </div>
        </div>

        <div class="sidebar">
            <div class="desktop-sidebar">
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($buyerMenu as $menuItem => $url): 
                            $active = ($url == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $menuItem; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>

            <!-- dropdown menu for mobile users -->
            <div class="mobile-sidebar">
                <form id="mobile-form" name="mobile-form">
                    <!-- onchange triggers JS function, so selection automatically redirects to the page -->
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <!-- loop through the array to get the correct link -->
                        <?php foreach ($buyerMenu as $buyMen => $url): ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $buyMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form>
            </div>

        </div>

        <div class="main-content">
            <div class="right">
                <!-- form to update the address -->
                <form action="address.php" method="post">
                    <div class="address">
                        <div class="double-box">
                            <div>
                                <label>first name</label>
                                <input type="text" name="firstName" value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label>surname</label>
                                <input type="text" name="surname" value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <label>street address</label>
                        <input type="text" name="streetAddress" value="<?php echo htmlspecialchars($_POST['streetAddress'] ?? ''); ?>" required>

                        <label>apartment, suite, etc. (optional)</label>
                        <input type="text" name="apartment" value="<?php echo htmlspecialchars($_POST['apartment'] ?? ''); ?>">

                        <label>town / city</label>
                        <input type="text" name="town" value="<?php echo htmlspecialchars($_POST['town'] ?? ''); ?>" required>

                        <label>country</label>
                        <input type="text" name="country" value="<?php echo htmlspecialchars($_POST['country'] ?? ''); ?>" required>

                        <label>postcode</label>
                        <input type="text" name="postcode" value="<?php echo htmlspecialchars($_POST['postcode'] ?? ''); ?>" required>

                        <label>email address</label>
                        <input type="text" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

                        <div class="submit-btn-container">
                            <button class="submit-btn" type="submit">save address</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>

<?php include "includes/footer.php" ?>

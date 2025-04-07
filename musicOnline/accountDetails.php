<?php 
// include functions file 
include 'php/functions.php'; 

// include navigation bar
include "includes/nav.php";

// require page title setup
require_once 'pageTitle.php';

// get the current page name for menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// check if user is logged in; if not, redirect them to the login page
if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('You must be logged in to access this page.');
        window.location.href = 'login.php';
    </script>";
    exit(); // stop further execution
}

// get user details from the session
$userID = $_SESSION['userID']; 
$role = $_SESSION['role']; 

// menu options available for buyers
$buyerMenu = [
    "my orders" => "myOrders.php",
    "address" => "address.php",
    "account details" => "accountDetails.php",
    "logout" => "logout.php"
];

// check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // optional fields, so they can be empty
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : "";
    $surname = isset($_POST['surname']) ? trim($_POST['surname']) : "";
    
    // email is mandatory, so we need to check if it's filled in
    if (!empty($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $errors[] = "You need to enter an email address.";
    }

    // only proceed if there are no errors
    if (empty($errors)) {

        // prepare an SQL statement to update user details securely
        $stmt = $dbConnect->prepare("UPDATE userInfo SET firstName = ?, surname = ?, email = ? WHERE userID = ?");

        if ($stmt) {
            // bind the user input to the SQL query parameters
            $stmt->bind_param("sssi", $firstName, $surname, $email, $userID);

            // execute the query and check if it worked
            if ($stmt->execute()) {
                echo '<script>alert("Account details updated successfully!");</script>';
            } else {
                echo '<script>alert("Failed to update account details.");</script>';
                echo $stmt->error; // display the SQL error for debugging
            }

            // close the statement to free up resources
            $stmt->close();
        } else {
            echo '<script>alert("Database error: Unable to prepare statement.");</script>';
        }
    } else {
        // show validation errors if any
        echo "<p>The following errors occurred:</p>";
        foreach ($errors as $message) {
            echo "$message<br>";
        }
        echo "</p>";
    }
}

// retrieve the username for displaying in the form
$getUsername = "SELECT username FROM userInfo WHERE userID = $userID";
$result = mysqli_query($dbConnect, $getUsername);

// check if a result was returned and fetch the username
if ($row = mysqli_fetch_assoc($result)) {
    $username = $row['username'];
}

// close the database connection
mysqli_close($dbConnect);

?>

<body>

    <?php if ( $_SESSION['role'] == 'admin') { ?>  
        <main class="middle-page-container">
            <p>page not avaiable to admin </p>
        </main>
    <?php } else { ?>

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>your account > account details</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">

                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($buyerMenu as $buyMen => $url): 
                            // highlight the active menu option
                            $active = ($url == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $buyMen; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
                
            </div>

            <div class="mobile-sidebar">
                <!-- dropdown menu for mobile users -->
                <form id="mobile-form" name="mobile-form">
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <?php foreach ($buyerMenu as $buyMen => $url): 
                            $active = ($url == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $buyMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>

        </div>

        <div class="main-content">

            <div class="right"> 
                <!-- form for updating user account details -->
                <form action="accountDetails.php" method="POST">
                    
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

                        <!-- username is for display only and cannot be changed -->
                        <div class="username">
                            <label>display name</label>
                            <input type="text" placeholder="Username" name="username" 
                            value="<?php echo htmlspecialchars($username); ?>" 
                            readonly /> 
                            <p>this is how your name is displayed in account sections and comments, and it cannot be changed</p>
                        </div>

                        <!-- account type is static for now -->
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

    <?php } ?>

<?php include "includes/footer.php" ?>

</body>

<script>
    // function to navigate the user when they select an option from the dropdown
    function navigateToMenu(select) {
        let url = select.value;
        if (url) {
            window.location.href = url;
        }
    }
</script>

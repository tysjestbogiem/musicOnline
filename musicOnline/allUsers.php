<?php 
// ensure the session starts only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include necessary files for functions and navigation
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// get the current page name for highlighting menu items
$currentPage = basename($_SERVER['PHP_SELF']);

// check if admin is logged in; if not, stop execution with an error message
if (!isset($_SESSION['adminID']) || !isset($_SESSION['role'])) {
    die("Error: unauthorised access.");
}

// assign user ID and role from session data
$userID = $_SESSION['userID']; 
$role = $_SESSION['role']; 

// menu options for the admin panel
$adminMenu = [
    "dashboard" => "admin.php",
    "all users" => "allUsers.php",
    "all vinyls" => "allVinyls.php",
    "edit" => "editAllVinyls.php",
    "all orders" => "allOrders.php",
    "settings" => "musicOnline.php",
    "logout" => "logout.php"
];

?>

<!-- allows admin to change colour scheme via class -->
<body class="<?php echo $bodyClass; ?>">

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>administrator account > all users</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <!-- loop through the menu array to display admin options for desktop view -->
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($adminMenu as $adMen => $url): 
                            // highlight the active page
                            $active = (basename($url) == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $adMen; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>

            <div class="mobile-sidebar">
                <!-- dropdown menu for mobile view -->
                <form id="mobile-form" name="mobile-form">
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <?php foreach ($adminMenu as $adMen => $url): 
                            // highlight the active page
                            $active = (basename($url) == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $adMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>

        </div>

        <div class="main-content">

            <div class="right">

                <!-- section for displaying all users -->
                <section id="adm-new-box-sec"> 
                    <div class="adm-new-box-t">
                        <h3>all users</h3>
                    </div>

                    <div class="order-container-ad">
                        <?php 
                        // retrieve up to 100 users from the database
                        $users = getAllUsers(100); 

                        if (!empty($users)) {  
                            foreach ($users as $user) { 
                                // securely retrieve and display user details
                                $userID = htmlspecialchars($user['userID']);
                                $username = htmlspecialchars($user['username']);
                                $email = htmlspecialchars($user['email']);
                                $name = htmlspecialchars($user['firstName'] . " " . $user['surname']);
                                $dateJoined = isset($user['createdAT']) ? date("d-m-Y", strtotime($user['createdAT'])) : 'no date';
                                $role = htmlspecialchars($user['role']);
                        ?>
                            <div class="update-box-ad">
                                <div class="item-info-ad">
                                    <p class="userID">user ID: #<?php echo $userID; ?></p>
                                    <p class="username"><strong><?php echo $username; ?></strong></p>
                                    <p class="email"><?php echo $email; ?></p>
                                </div>

                                <div class="item-info-ad">
                                    <p class="name"><?php echo $name; ?></p>
                                    <p class="dateJoin">joined: <?php echo $dateJoined; ?></p>
                                    <p class="role">role: <?php echo $role; ?></p>
                                </div>
                                
                                <!-- action icons for editing or deleting users -->
                                <ul class="update-ikons">
                                    <li>
                                        <a href="editUserAdmin.php?userID=<?php echo urlencode($userID); ?>" class="ikon">
                                            <i class="fas fa-edit"></i> <!-- edit user -->
                                        </a>
                                    </li>
                                    <li>
                                        <a href="deleteUser.php?userID=<?php echo urlencode($userID); ?>" class="ikon" 
                                           onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fas fa-backspace"></i> <!-- delete user -->
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php 
                            } 
                        } else {  
                            echo "<p>no new users available.</p>"; // display message if no users are found
                        }
                        ?>
                    </div>

                </section>

            </div>

        </div>
    </div>    

<?php include "includes/footer.php" ?>

</body>

<!-- script for the dropdown menu navigation -->
<script>
    function navigateToMenu(select) {
        let url = select.value;
        if (url) {
            window.location.href = url; // redirect the user to the selected menu option
        }
    }
</script>

<?php 
// ensure the session starts only once to avoid duplication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include necessary files for functions, navigation, and page title
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

// admin menu options for sidebar navigation
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
                <p>administrator account > edit vinyls</p> 
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

                <div class="order-container">

                    <?php
                    // fetch the list of all vinyls (limit to 100)
                    $vinyls = getVinylsCard(100); 

                    // check if vinyl records exist before displaying them
                    if (!empty($vinyls)) {
                        foreach ($vinyls as $vinyl) {
                            $vinylID = intval($vinyl['vinylID']);
                            $title = htmlspecialchars($vinyl['title']);
                            $artist = htmlspecialchars($vinyl['artist']);
                            // use stored image if available, otherwise use default image
                            $imagePath = !empty($vinyl['photo1']) ? htmlspecialchars($vinyl['photo1']) : "images/no-image.jpg";
                    ?>
                        <!-- display each vinyl record in an update box -->
                        <div class="update-box">
                            <div class="image-placeholder">
                                <a href="vinyl.php?id=<?php echo $vinylID; ?>" class="mini-image">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $title; ?>">
                                </a>
                            </div>
                            <div class="item-info">
                                <p class="title"><?php echo $title; ?></p>
                                <p class="artist"><?php echo $artist; ?></p>
                            </div>
                            
                            <!-- icons for editing or deleting vinyl -->
                            <ul class="update-ikons">
                                <li>
                                    <a href="editAllVinylsAdmin.php?vinylID=<?php echo urlencode($vinylID); ?>" class="ikon">
                                        <i class="fas fa-edit"></i> <!-- edit vinyl -->
                                    </a>
                                </li>
                                <li>
                                    <a href="delete.php?vinylID=<?php echo urlencode($vinylID); ?>" class="ikon" onclick="return confirm('Are you sure you want to delete this vinyl?');">
                                        <i class="fas fa-backspace"></i> <!-- delete vinyl -->
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php
                        }
                    } else {
                        // display message if no vinyl records are found
                        echo "<p>no vinyl records found.</p>";
                    }
                    ?>

                </div>

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

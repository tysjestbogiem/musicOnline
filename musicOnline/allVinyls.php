<?php 
// ensure the session starts only once to prevent conflicts
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

// fetch all vinyl records
$vinyls = getAllVinyls();

?>

<!-- allows admin to change colour scheme via class -->
<body class="<?php echo $bodyClass; ?>">

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>administrator account > all vinyls</p> 
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
                <div class="adm-new-box-t">
                    <h3>all vinyls</h3>
                </div>

                <?php
                    // fetch vinyl records sorted by date added
                    $vinyls = getVinylsCardByDate();

                    // check if vinyl records exist and display them
                    if (!empty($vinyls)) {
                        foreach ($vinyls as $vinyl) {
                            $vinylID = intval($vinyl['vinylID']);
                            $title = htmlspecialchars($vinyl['title']);
                            $artist = htmlspecialchars($vinyl['artist']);
                            // use stored image if available, otherwise use default image
                            $imagePath = !empty($vinyl['photo1']) ? htmlspecialchars($vinyl['photo1']) : "images/no-image.jpg";

                            // retrieve the seller's username for the vinyl
                            $seller = getUsernameByVinyl($vinylID); 

                            // get the date when the vinyl was added
                            $dateAdd = isset($vinyl['createdAT']) ? date("d-m-Y H:i:s", strtotime($vinyl['createdAT'])) : 'no date';

                            // check if the vinyl was part of an order and update details accordingly
                            if (!empty($orders)) {
                                foreach ($orders as $order) {
                                    if (isset($order['vinylID']) && $order['vinylID'] == $vinylID) {
                                        $seller = isset($order['seller']) ? htmlspecialchars($order['seller']) : "Unknown";
                                        $dateAdd = isset($order['orderDate']) ? date("d-m-Y H:i:s", strtotime($order['orderDate'])) : 'no date';
                                        break;
                                    }
                                }
                            }
                    ?>
                            <!-- display each vinyl record -->
                            <div class="update-box-ad">
                                <div class="image-placeholder">
                                    <a href="vinyl.php?id=<?php echo $vinylID; ?>" class="mini-image">
                                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $title; ?>">
                                    </a>
                                </div>
                                <div class="item-info-ad">
                                    <p class="title"><?php echo $title; ?></p>
                                    <p class="artist"><?php echo $artist; ?></p>
                                </div>
                                <div class="item-info-ad">
                                    <p class="date"><?php echo $dateAdd; ?></p>
                                    <p class="seller">added by: <?php echo $seller; ?></p>
                                </div>
                                
                                <!-- icons for additional actions -->
                                <ul class="update-ikons">
                                    <li>
                                        <a href="#" class="ikon">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                    <?php 
                        }
                    } else {
                        // display message if no vinyl records are found
                        echo "<p>No vinyl records found.</p>";
                    }
                    ?>
                
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

<?php 
// ensure the session starts only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include required files for functionality, navigation, and page title
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// get the current page name to highlight the correct menu item
$currentPage = basename($_SERVER['PHP_SELF']);

// check if the user is an admin and has a role assigned
if (!isset($_SESSION['adminID']) || !isset($_SESSION['role'])) {
    die("Error: unauthorised access."); // stop execution if user is not an admin
}

// retrieve the admin user ID and role from the session
$userID = $_SESSION['userID']; 
$role = $_SESSION['role']; 

// admin navigation menu
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

<body class="<?php echo $bodyClass; ?>">
    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>administrator account > dashboard</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($adminMenu as $menuItem => $url): 
                            // highlight the active menu item
                            $active = (basename($url) == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $menuItem; ?></a>
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
                        <?php foreach ($adminMenu as $menuItem => $url): 
                            $active = (basename($url) == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $menuItem; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>

        </div>

        <div class="main-content">

            <div class="right">

                <!-- section for displaying new users -->
                <section id="adm-new-box-sec"> 
                    <div class="adm-new-box-t">
                        <h3>new users</h3>
                        <a href="allUsers.php" class="view-all-button">view all</a>
                    </div>

                    <div class="order-container-ad">
                        <?php 
                        // retrieve the 3 latest users
                        $users = getAllUsers(3); 

                        if (!empty($users)) {  
                            foreach ($users as $user) { 
                                // securely retrieve and display user details
                                $userID = htmlspecialchars($user['userID']);
                                $username = htmlspecialchars($user['username']);
                                $email = htmlspecialchars($user['email']);
                                $name = htmlspecialchars($user['firstName'] . " " . $user['surname']);
                                $dateJoined = isset($user['createdAT']) ? date("d-m-Y H:i:s", strtotime($user['createdAT'])) : 'no date';
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
                            echo "<p>no new users available.</p>";
                        }
                        ?>
                    </div>

                </section>

                <!-- section for displaying newly added vinyls -->
                <section id="adm-new-box-sec">
                    <div class="adm-new-box-t">
                        <h3>new vinyls</h3>
                        <a href="allVinyls.php" class="view-all-button">view all</a>
                    </div>

                    <div class="order-container-ad">
                    <?php
                        // retrieve the latest vinyl records
                        $vinyls = getVinylsCardByDate(3);

                        if (!empty($vinyls)) {
                            foreach ($vinyls as $vinyl) {
                                $vinylID = intval($vinyl['vinylID']);
                                $title = htmlspecialchars($vinyl['title']);
                                $artist = htmlspecialchars($vinyl['artist']);
                                $imagePath = !empty($vinyl['photo1']) ? htmlspecialchars($vinyl['photo1']) : "images/no-image.jpg";

                                $seller = getUsernameByVinyl($vinylID); 

                                // get the date when added
                                $dateAdd = isset($vinyl['createdAT']) ? date("d-m-Y H:i:s", strtotime($vinyl['createdAT'])) : 'no date';
                        ?>
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
                            echo "<p>no vinyl records found.</p>";
                        }
                        ?>
                </section>

                <section id="adm-new-box-sec">
                    <div class="adm-new-box-t">
                        <h3>new orders</h3>
                        <a href="allOrders.php" class="view-all-button">view all</a>
                    </div>

                    <div class="order-container-ad">
                        <?php 
                        $orders = getAllUsersOrders(3); // get order function

                        if (!empty($orders)) {  
                            foreach ($orders as $order) { 
                                $orderID = htmlspecialchars($order['orderID']);
                                $totalAmount = isset($order['totalAmount']) ? number_format($order['totalAmount'], 2) : 'unknown';
                                $orderDate = isset($order['orderDate']) ? date("d-m-Y", strtotime($order['orderDate'])) : 'unknown';
                                $buyer = isset($order['buyer']) ? htmlspecialchars($order['buyer']) : 'unknown';

                                // functions to get all vinyls that were bought in one order
                                $orderItems = getOrderItemsByOrderID($orderID); 
                        ?>

                            <!-- top of order display -->
                            <div class="update-box-ad" style="padding: 10px; margin-bottom: 10px; background: #ddd; border-radius: 5px;">
                                <div class="item-info-ad" style="font-size: 13px; padding-bottom: 5px;">
                                    <strong>order #<?php echo $orderID; ?></strong> &nbsp; | &nbsp; 
                                    <strong>buyer: <?php echo $buyer; ?></strong> &nbsp; | &nbsp; 
                                    <strong>date: <?php echo $orderDate; ?></strong> &nbsp; | &nbsp; 
                                    <strong>total: £<?php echo $totalAmount; ?></strong>
                                

                                <!-- part that displays vinyls ordered -->
                                <table cellspacing="0" cellpadding="5" 
                                    style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 5px; font-size: 13px;">
                                    <thead>
                                        <!-- styling for order display table -->
                                        <tr style="border-bottom: 2px solid #ddd; font-weight: bold;">
                                            <th style="width: 10%;">id</th>
                                            <th style="width: 40%;">title</th>
                                            <th class="hed-mob" style="width: 30%;">artist</th>
                                            <th style="width: 10%;">qty</th>
                                            <th style="width: 10%;">price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderItems as $item) { ?>
                                            <tr style="border-bottom: 1px solid #eee;">
                                                <td><?php echo htmlspecialchars($item['vinylID']); ?></td>
                                                <td class="tbl-mob"><?php echo htmlspecialchars($item['title']); ?></td>
                                                <td class="tbl-mob"><?php echo htmlspecialchars($item['artist']); ?></td>
                                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                <td>£<?php echo number_format($item['price'], 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>

                        <?php 
                            } 
                        } else {  
                            echo "<p>no orders available.</p>";
                        }
                        ?>
                    </div>

                </section>

            </div>
        </div>
    </div>    

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

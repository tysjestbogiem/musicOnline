<?php 
// ensure the session starts only once to avoid conflicts
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

?>

<!-- allows admin to change colour scheme via class -->
<body class="<?php echo $bodyClass; ?>">

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>administrator account > all orders</p> 
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

                <!-- section for displaying new orders -->
                <section id="adm-new-box-sec">
                    <div class="adm-new-box-t">
                        <h3>new orders</h3>
                        <a href="allOrders.php"></a>
                    </div>

                    <div class="order-container-ad">
                        <?php 
                        // retrieve up to 100 orders from the database
                        $orders = getAllUsersOrders(100);

                        if (!empty($orders)) {  
                            foreach ($orders as $order) { 
                                // retrieve and display order details
                                $orderID = htmlspecialchars($order['orderID']);
                                $totalAmount = isset($order['totalAmount']) ? number_format($order['totalAmount'], 2) : 'unknown';
                                $orderDate = isset($order['orderDate']) ? date("d-m-Y", strtotime($order['orderDate'])) : 'unknown';
                                $buyer = isset($order['buyer']) ? htmlspecialchars($order['buyer']) : 'unknown';

                                // retrieve the vinyl records associated with this order
                                $orderItems = getOrderItemsByOrderID($orderID); 
                        ?>

                            <!-- order box display -->
                            <div class="update-box-ad" style="padding: 10px; margin-bottom: 10px; background: #ddd; border-radius: 5px;">
                                <div class="item-info-ad" style="font-size: 13px; padding-bottom: 5px;">
                                    <strong>order #<?php echo $orderID; ?></strong> &nbsp; | &nbsp; 
                                    <strong>buyer: <?php echo $buyer; ?></strong> &nbsp; | &nbsp; 
                                    <strong>date: <?php echo $orderDate; ?></strong> &nbsp; | &nbsp; 
                                    <strong>total: £<?php echo $totalAmount; ?></strong>
                                
                                    <!-- table displaying ordered vinyls -->
                                    <table cellspacing="0" cellpadding="5" 
                                        style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 5px; font-size: 13px;">
                                        <thead>
                                            <!-- table header styling -->
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
                            echo "<p>no orders available.</p>"; // display message if no orders are found
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

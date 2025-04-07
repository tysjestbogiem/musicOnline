<?php 
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';


// var_dump($_SESSION['role']);


// check if the user is logged in and not an admin
if (!isset($_SESSION['userID']) || $_SESSION['role'] == 'admin') { ?>  

    <div class="info-page-layout">
        <div class="info-page-container">
            <div class="ipc-text">
                <h2>sell with us</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. <br><br>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                <a href="register.php" class="reg-btn-orange">register to sell</a>
            </div>
            <div class="ipc-img" style="background: url('images/sell-vinyl-online.webp') no-repeat center center/cover;">
            </div>
        </div>
    </div>

<?php } else {  

    $currentPage = basename($_SERVER['PHP_SELF']);

    // check if user is logged in
    if (!isset($_SESSION['userID'])) {
        die("error: seller id not found.");
    }
    $userID = $_SESSION['userID']; 

    // menu links for the sidebar
    $sellerMenu = [
        "dashboard" => "sell.php",
        "your vinyls" => "vinyls.php",
        "add" => "vinylAdd.php",
        "edit" => "vinylEdit.php",
        "your orders" => "orders.php",
        "logout" => "logout.php"
    ];

    // fetch seller's orders
    $orders = getSellersOrders($userID, $limit = 3); // get latest 3 orders
    $allOrders = getSellersOrders($userID, $limit = 100); // get all orders to display total price

    // get total number of active vinyls for sale
    $totalActive = getNumberOfVinylsToSell($userID);
    $totalSold = 0;  
    $totalAmountSold = 0.00;  

    // loop through orders to sum up total sold and total revenue
    foreach ($allOrders as $order) {
        $totalSold += $order['quantitySold'];  // count how many items sold
        $totalAmountSold += $order['price']; // sum up revenue
    }

?>

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>shop manager > dashboard</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($sellerMenu as $sellMen => $url): 
                            $active = (basename($url) == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $sellMen; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>

            <div class="mobile-sidebar">
                <form id="mobile-form" name="mobile-form">
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <?php foreach ($sellerMenu as $sellMen => $url): 
                            $active = (basename($url) == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $sellMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>

        </div>

        <div class="main-content">

            <div class="right">

                <!-- overview panel with quick stats -->
                <div class="dashboard-panel">
                    <div class="active">
                        <h3><?php echo $totalActive ?></h3>
                        <p>active</p>
                    </div>
                    <div class="orders">
                        <h3><?php echo $totalSold ?></h3>
                        <p>orders</p>
                    </div>
                    <div class="total-amount">
                        <h3>£<?php echo $totalAmountSold ?></h3>
                        <p>total</p>
                    </div>
                    <div class="add-vinyl-btn">
                        <a href="vinylAdd.php">add vinyl</a>
                    </div>
                </div>

                <div class="newest-order">
                    <div class="column-header">
                        <p>vinyls you sold so far - view 3</p>
                    </div>

                    <?php if (!empty($orders)) { ?>
                        <?php foreach ($orders as $order) { ?>
                            <div class="update-box">
                                <div class="image-placeholder">
                                    <a href="vinyl.php?id=<?php echo intval($order['vinylID']); ?>" class="mini-image">
                                        <?php 
                                        $imagePath = !empty($order['photo1']) ? $order['photo1'] : "images/no-image.jpg";
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($order['title']); ?>">
                                    </a>
                                </div>
                                <div class="item-info">
                                    <h3 class="title"><?php echo htmlspecialchars($order['title']); ?></h3>
                                    <h5 class="artist"><?php echo htmlspecialchars($order['artist']); ?></h5>
                                </div>
                                <div class="item-info">
                                    <h5 class="price">£<?php echo htmlspecialchars($order['price']); ?></h5>
                                </div>
                                <ul class="update-ikons">
                                    <li>
                                        <a href="#" class="ikon">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>no orders found.</p>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

<?php } ?>

<?php include "includes/footer.php"; ?> 

</body>
    
<script>
    function navigateToMenu(select) {
        if (select.value) {
            window.location.href = select.value;
        }
    }
</script>

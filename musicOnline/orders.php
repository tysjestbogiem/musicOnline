<?php 
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

$currentPage = basename($_SERVER['PHP_SELF']);

// check if user is logged in, otherwise stop execution
if (!isset($_SESSION['userID'])) {
    die("Error: No valid user session found.");
}

$userID = $_SESSION['userID']; // get user ID from session

// menu links for sellers
$sellerMenu = [
    "dashboard" => "sell.php",
    "your vinyls" => "vinyls.php",
    "add" => "vinylAdd.php",
    "edit" => "vinylEdit.php",
    "orders" => "orders.php",
    "logout" => "logout.php"
];

// get all orders that were SOLD by user
$orders = getSellersOrders($userID, $limit = 100);
?>

<body>
    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>shop manager > orders</p> 
            </div>
        </div>

        <div class="sidebar">
            <div class="desktop-sidebar">
                <!-- display seller menu on the left for larger screens -->
                <ul>
                    <?php foreach ($sellerMenu as $sellMen => $url): 
                        $active = ($url == $currentPage) ? 'data-active="active"' : ""; ?>
                        <li>
                            <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $sellMen; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="mobile-sidebar">
                <!-- dropdown menu for mobile navigation -->
                <form id="mobile-form" name="mobile-form">
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <?php foreach ($sellerMenu as $sellMen => $url): ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $sellMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form>
            </div>
        </div>

        <div class="main-content">
            <div class="right">
                <div class="newest-order">
                    <div class="column-header">
                        <p>vinyls you sold so far</p>
                    </div>

                    <!-- check if there are any orders -->
                    <?php if (!empty($orders)) { ?>
                        <?php foreach ($orders as $order) { ?>
                            <div class="update-box">
                                <div class="image-placeholder">
                                    <div class="mini-image">
                                        <?php 
                                        // if the vinyl has an image, show it, otherwise display a placeholder
                                        $imagePath = !empty($order['photo1']) ? $order['photo1'] : "images/no-image.jpg";
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($order['title']); ?>">
                                    </div>
                                </div>

                                <div class="item-info">
                                    <h3 class="title"><?php echo htmlspecialchars($order['title']); ?></h3>
                                    <h5 class="artist"><?php echo htmlspecialchars($order['artist']); ?></h5>
                                </div>

                                <div class="item-info">
                                    <h5 class="price"><?php echo htmlspecialchars($order['price']); ?></h5>
                                </div>

                                <!-- link to view the vinyl -->
                                <ul class="update-ikons">
                                    <li>
                                        <a href="vinyl.php?id=<?php echo intval($order['vinylID']); ?>" class="ikon">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <!-- if no orders were found -->
                        <p>no orders found.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

<?php include "includes/footer.php"; ?>
</body>

<script>
    // function to navigate dropdown menu for mobile users
    function navigateToMenu(select) {
        let url = select.value;
        if (url) {
            window.location.href = url;
        }
    }
</script>

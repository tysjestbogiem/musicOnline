<?php 
// include necessary files for functions, navigation, and page title
require_once 'php/functions.php';
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// start session if it hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// check if the user is logged in; if not, show an alert and redirect to login page
if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('You must be logged in to access this page.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// store user ID from session
$userID = $_SESSION['userID']; 

// get current page name
$currentPage = basename($_SERVER['PHP_SELF']);

// define menu for buyers (sidebar navigation)
$buyerMenu = [
    "my orders" => "myOrders.php",
    "address" => "address.php",
    "account details" => "accountDetails.php",
    "logout" => "logout.php"
];

// retrieve orders for the logged-in user
$orders = getBoughtVinylsByUser($userID); 

?>

<body>

        <?php if ( $_SESSION['role'] == 'admin') { ?>  
            <main class="middle-page-container">
                <p>page not avaiable to admin </p>
            </main>
        <?php } else { ?>

        <div class="main-container">

            <!-- header section -->
            <div class="header">
                <div class="page-view-title">
                    <p>your account > my orders</p> 
                </div>
            </div>

            <!-- sidebar section -->
            <div class="sidebar">
                <div class="desktop-sidebar">
                    <ul>
                        <!-- loop through the buyer menu to create links -->
                        <?php foreach ($buyerMenu as $menuItem => $url): 
                            $active = ($url == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $menuItem; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- mobile sidebar dropdown -->
                <div class="mobile-sidebar">
                    <form id="mobile-form" name="mobile-form">
                        <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                            <option value="">---- select menu option ----</option>
                            <?php foreach ($buyerMenu as $buyMen => $url): ?>
                                <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $buyMen; ?></option>
                            <?php endforeach; ?>
                        </select> 
                    </form>
                </div>
            </div>

            <!-- main content area -->
            <div class="main-content">
    
                <div class="right">

                    <div class="newest-order">
                        <div class="column-header">
                            <p>orders you made so far</p>
                        </div>
                        <?php if (!empty($orders)) { ?>
                            <?php foreach ($orders as $order) { ?>
                                <div class="update-box">
                                    <div class="image-placeholder">
                                        <a href="vinyl.php?id=<?php echo intval($order['vinylID']); ?>" class="mini-image">
                                            <?php 
                                            // set a default image if none is available
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
                                        <h5 class="price"><?php echo htmlspecialchars($order['price']); ?></h5>
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
                            <p>No orders found.</p>
                        <?php } ?>
                    </div>
                </div>
               
            </div>
        </div>

        <?php } ?>
</body>

<!-- include footer -->
<?php include "includes/footer.php"; ?>

<script>
    // function to handle navigation from dropdown menu on mobile
    function navigateToMenu(select) {
        if (select.value) {
            window.location.href = select.value;
        }
    }
</script>

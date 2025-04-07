<?php 
// start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// get current page name
$currentPage = basename($_SERVER['PHP_SELF']);

// make sure user is logged in
if (!isset($_SESSION['userID'])) {
    die("error: seller id not found.");
}
$userID = $_SESSION['userID']; 

// menu links for the sidebar
$sellerMenu = [
    "dashboard" => "sell.php",
    "vinyls" => "vinyls.php",
    "add" => "vinylAdd.php",
    "edit" => "vinylEdit.php",
    "orders" => "orders.php",
    "logout" => "logout.php"
];

?>

<body>

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>shop manager > your vinyls</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($sellerMenu as $sellMen => $url): 
                            $active = ($url == $currentPage) ? 'data-active="active"' : ""; ?>
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
                            $active = ($url == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $sellMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>
        </div>

        <div class="main-content">
            <div class="right">
                <div class="order-container">
                    <?php
                        // get vinyls that seller has listed
                        $vinylsBySell = getVinylsBySellerDate($userID);
                        
                        // check if seller has listed any vinyls
                        if (!empty($vinylsBySell)) {
                            foreach ($vinylsBySell as $vinyl) { 
                    ?>
                            <div class="update-box">
                                <!-- show vinyl image -->
                                <div class="image-placeholder">
                                    <div class="mini-image">
                                        <?php 
                                        // set default image if none uploaded
                                        $imagePath = !empty($vinyl['photo1']) ? $vinyl['photo1'] : "images/no-image.jpg";
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($vinyl['title']); ?>">
                                    </div>
                                </div>

                                <!-- vinyl title and artist -->
                                <div class="item-info">
                                    <h3 class="title"><?php echo htmlspecialchars($vinyl['title']); ?></h3>
                                    <h5 class="artist"><?php echo htmlspecialchars($vinyl['artist']); ?></h5>
                                </div>

                                <!-- price and quantity -->
                                <div class="item-info">
                                    <h5 class="price">£<?php echo htmlspecialchars($vinyl['price']); ?></h5>
                                    <h5 class="quantity">qty: <?php echo htmlspecialchars($vinyl['quantity']); ?></h5>
                                </div>

                                <ul class="update-ikons">
                                    <li>
                                        <a href="vinyl.php?id=<?php echo intval($vinyl['vinylID']); ?>" class="ikon">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div> 
                    <?php 
                            } // end of loop
                        } else {  
                            // show message if no vinyls listed
                            echo "<p>you haven't listed any vinyls for sale yet.</p>";
                        }
                    ?>
                </div> 
            </div> 
        </div> 
    </div>

</body>

<script>
    function navigateToMenu(select) {
        let url = select.value;
        if (url) {
            window.location.href = url;
        }
    }
</script>

<?php include "includes/footer.php" ?>

<?php 
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

$pageTitle = 'edit vinyl';

// get current page for sidebar highlight
$currentPage = basename($_SERVER['PHP_SELF']);

// check if user is logged in
if (!isset($_SESSION['userID'])) {
    die("error: seller id not found.");
}
$userID = $_SESSION['userID']; 

// seller menu for sidebar
$sellerMenu = [
    "dashboard" => "sell.php",
    "your vinyls" => "vinyls.php",
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
            <p>shop manager > edit vinyl</p> 
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
                // get vinyls listed by seller in A-Z order
                $vinyls = getVinylsBySellerAtoZ($userID);

                // check if seller has vinyl records
                if (!empty($vinyls)) {
                    foreach ($vinyls as $vinyl) {
                ?>
                    <div class="update-box">
                        <div class="image-placeholder">
                            <a href="vinyl.php?id=<?php echo intval($vinyl['vinylID']); ?>" class="mini-image">
                                <?php 
                                $imagePath = !empty($vinyl['photo1']) ? $vinyl['photo1'] : "images/no-image.jpg";
                                ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($vinyl['title']); ?>">
                            </a>
                        </div>
                        <div class="item-info">
                            <h3 class="title"><?php echo htmlspecialchars($vinyl['title']); ?></h3>
                            <h5 class="artist"><?php echo htmlspecialchars($vinyl['artist']); ?></h5>
                        </div>
                        
                        <ul class="update-ikons">
                            <li>
                                <a href="edit.php?vinylID=<?php echo urlencode($vinyl['vinylID']); ?>" class="ikon">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </li>
                            <li>
                                <a href="delete.php?vinylID=<?php echo urlencode($vinyl['vinylID']); ?>" class="ikon">
                                    <i class="fas fa-backspace"></i>
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
            </div>
        </div>
    </div>

</div>
</body>

<?php require_once 'includes/footer.php' ?>

<script>
    function navigateToMenu(select) {
        let url = select.value;
        if (url) {
            window.location.href = url;
        }
    }
</script>

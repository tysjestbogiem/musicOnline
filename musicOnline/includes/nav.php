<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'php/functions.php';
require_once 'pageTitle.php';

// make sure page title is set before using it in <title> tag
if (!isset($pageTitle)) {
    $pageTitle = "musicOnline"; 
}

// check user role to apply a different theme
$bodyClass = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? "admin-theme" : "user-theme";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- seo tags to help search engines understand the page -->
    <meta name="description" content="platform with new and used vinyls">
    <meta name="keywords" content="vinyls, new, used, buy, sell">

    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- link to css file -->
    <link rel="stylesheet" href="css/style.css">

    <!-- link to fontawesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- link to google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- link to swiperjs css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- link to swiperjs script -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- link to js file -->
    <script src="javascript/script.js"></script>

<?php
// get current page filename
$currentPage = basename($_SERVER['PHP_SELF']); 

$pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : "musicOnline";
?>

</head>

<section id="header">
    <a href="home.php"><div class="logo"><strong>mO</strong></div></a>
    <nav class="nav-container">
        <ul class="navbar left-nav">
            <li><a href="home.php" class="<?php echo ($currentPage == 'home.php') ? 'active' : ''; ?>">home</a></li>
            <li><a href="shop.php" class="<?php echo ($currentPage == 'shop.php') ? 'active' : ''; ?>">shop</a></li>
            <li><a href="sell.php" class="<?php echo ($currentPage == 'sell.php') ? 'active' : ''; ?>">sell</a></li>
            <li>
                <div class="search-container">
                    <form action="search.php" method="get">
                        <div class="search-box">
                            <input type="text" name="keyword" placeholder="search..">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <?php if (isset($_SESSION['username'])): ?>
                    <li>hi! <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                <?php endif; ?>
            </li>
        </ul>
        <ul class="navbar right-nav">
            <?php if (!isset($_SESSION['username'])) { ?>
                <!-- show login/register when not logged in -->
                <li>
                    <div class="log-buttons">
                        <a href="login.php" class="login-button">login</a>
                        <a href="register.php" class="register-button">register</a>
                    </div>
                </li>
            <?php } else { 
                $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user'; 
            ?>
                <?php if ($role == 'user') { ?>
                    <!-- show cart only for normal users -->
                    <li>
                        <div class="cart <?php echo ($currentPage == 'cart.php') ? 'active' : ''; ?>">
                            <a href="cart.php">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </div>
                    </li>
                <?php } ?>

                <li>
                    <div class="user <?php echo ($currentPage == 'myOrders.php' || $currentPage == 'admin.php') ? 'active' : ''; ?>">
                        <a href="<?php echo ($role == 'admin') ? 'admin.php' : 'myOrders.php'; ?>">
                            <i class="fa-solid fa-user"></i>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="log-buttons">
                        <a href="logout.php" class="register-button">logout</a>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </nav>
</section>

<!-- mobile navbar -->
<section class="mobile-view">
    <a href="home.php"><div class="logo"><strong>mO</strong></div></a>

    <button class="hamburger-menu">
        <i class="fa-solid fa-bars"></i> 
    </button>
    <button class="close-menu">
        <i class="fa-solid fa-xmark"></i> 
    </button>

    <!-- mobile navigation, hidden for desktop -->
    <div class="nav-links">
        <a href="home.php" class="<?php echo ($currentPage == 'home.php') ? 'active' : ''; ?>">home</a>
        <a href="shop.php" class="<?php echo ($currentPage == 'shop.php') ? 'active' : ''; ?>">shop</a>
        <a href="sell.php" class="<?php echo ($currentPage == 'sell.php') ? 'active' : ''; ?>">sell</a>

        <?php if (!isset($_SESSION['username'])) { ?>
            <a href="login.php">login</a>
            <a href="register.php">register</a> 
        <?php } else { ?>
            <a href="logout.php">logout</a>
        <?php } ?>
    </div>

    <div class="bottom-bar">
        <div class="user">
            <?php if (!isset($_SESSION['username'])) { ?>
                <!-- show login icon when user is NOT logged in -->
                <a href="login.php">
                    <i class="fa-solid fa-user"></i>
                </a>
            <?php } else { ?>
                <!-- show correct page based on role -->
                <?php if ($role == 'user') { ?>
                    <a href="myOrders.php">
                        <i class="fa-solid fa-user"></i>
                    </a>
                <?php } elseif ($role == 'admin') { ?>
                    <a href="admin.php">
                        <i class="fa-solid fa-user"></i>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>

        <div class="search-bar">
            <div class="search-container" style="width: 250px;">
                <form action="search.php" method="get">
                    <div class="search-box">
                        <input type="text" name="keyword" placeholder="search..">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($_SESSION['username']) && $role == 'user') { ?>
            <!-- show cart ONLY for logged-in users with the 'user' role -->
            <div class="cart <?php echo ($currentPage == 'cart.php') ? 'active' : ''; ?>">
                <a href="cart.php">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
            </div>
        <?php } ?>
    </div>
</section>

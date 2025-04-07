<?php
// Get the current page filename dynamically
$currentPage = basename($_SERVER['PHP_SELF']); 

// Set custom page titles for each page
$pageTitles = [  
    "about.php" => "About Us - musicOnline",
    "accountDetails.php" => "Your Account Details",
    "address.php" => "Manage Your Address",
    "admin.php" => "Admin Dashboard",
    "allOrders.php" => "All Orders",
    "allUsers.php" => "All Users",
    "allVinyls.php" => "All Vinyls",
    "authenticate.php" => "Authentication",
    "cart.php" => "Your Shopping Cart",
    "delete.php" => "Delete Confirmation",
    "deleteUser.php" => "Delete User Confirmation",
    "edit.php" => "Edit Page",
    "editAllVinyls.php" => "Edit All Vinyls",
    "editAllVinylsAdmin.php" => "Admin - Edit Vinyls",
    "editUserAdmin.php" => "Edit User Details",
    "genre.php" => "Browse by Genre",
    "home.php" => "Home - musicOnline",
    "info.php" => "Information Page",
    "login.php" => "Login to Your Account",
    "logout.php" => "Logging Out...",
    "myOrders.php" => "Your Orders",
    "orders.php" => "Order History",
    "register.php" => "Create a New Account",
    "search.php" => "Search Results",
    "sell.php" => "Sell Your Items",
    "shop.php" => "Shop for Vinyls",
    "users.php" => "User List",
    "vinyl.php" => "Vinyl Details",
    "vinylAdd.php" => "Add New Vinyl",
    "vinylEdit.php" => "Edit Vinyl",
    "vinyls.php" => "All Vinyls"
];

// Set the page title based on the current page, defaulting to "musicOnline"
$pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : "musicOnline";
?>

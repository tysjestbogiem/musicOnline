<?php
// start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include database connection and functions
require_once "php/functions.php"; 
require_once 'pageTitle.php'; 

$mysqli = dbConnect(); // connect to the database

// ensure user is logged in before allowing cart operations
$userID = $_SESSION['userID'] ?? null;
$addressID = 6; // default address for testing when order submit
$quantity = 1; 

if (!$userID) {
    die("Error: User not logged in."); // stop execution if user is not logged in
}

// handle "Add to Cart" logic
if (isset($_POST['vinylID'], $_POST['quantity']) && is_numeric($_POST['vinylID']) && is_numeric($_POST['quantity'])) {
    $vinylID = (int)$_POST['vinylID'];
    $quantity = (int)$_POST['quantity'];

    // retrieve the vinyl record from the database
    $stmt = $mysqli->prepare('SELECT * FROM vinylInfo WHERE vinylID = ?');
    $stmt->bind_param("i", $vinylID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // add item to cart if it exists and quantity is valid
    if ($product && $quantity > 0) {
        $_SESSION['cart'][$vinylID] = ($_SESSION['cart'][$vinylID] ?? 0) + $quantity;
    }
    
    $stmt->close();
    header('Location: cart.php'); // redirect to cart page
    exit();
}

// handle removing items from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header('Location: cart.php');
    exit();
}

// handle updating cart quantities
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            $quantity = (int)$v;
            if (isset($_SESSION['cart'][$id]) && $quantity > 0) {
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    header('Location: cart.php');
    exit();
}

// fetch products in cart using MySQLi
$totalAmount = $_SESSION['totalAmount'] ?? 0.00; // retrieve total amount
$products_in_cart = $_SESSION['cart'] ?? [];
$products = [];

if (!empty($products_in_cart)) { // prevent empty SQL queries
    $placeholders = implode(',', array_fill(0, count($products_in_cart), '?'));
    $types = str_repeat('i', count($products_in_cart));
    $ids = array_keys($products_in_cart);

    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo WHERE vinylID IN ($placeholders)");

    if ($stmt) { 
        $params = array_merge([$types], $ids);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        error_log("SQL Prepare Error: " . $mysqli->error); // log error if query fails
    }

    // calculate total amount
    foreach ($products as $product) {
        $quantity = (int)$products_in_cart[$product['vinylID']];
        $totalAmount += (float)$product['price'] * $quantity;
    }
}

// only place the order when "Place Order" is clicked
if (isset($_POST['placeorder']) && !empty($_SESSION['cart'])) {
    $_SESSION['totalAmount'] = $totalAmount;
    header('Location: cart.php?page=placeorder');
    exit();
}

// execute order placement only when on the "placeorder" page
if (isset($_GET['page']) && $_GET['page'] === "placeorder" && !empty($_SESSION['cart'])) {
    $orderSuccess = placeOrderQuery($userID, $_SESSION['cart'], $addressID, $_SESSION['totalAmount']);

    if ($orderSuccess) {
        $_SESSION['order_success'] = true; // store success message in session
        unset($_SESSION['cart']); // clear cart after successful order
        unset($_SESSION['totalAmount']);
        header("Location: cart.php"); // redirect to cart page
        exit();
    } else {
        $_SESSION['order_error'] = "Database error: Order was not inserted.";
        header("Location: cart.php");
        exit();
    }
}

// display success message after redirection
if (isset($_SESSION['order_success'])) {
    echo "<script>alert('Your order was successful! Thank you for participating in testing!');</script>";
    unset($_SESSION['order_success']);
}

// display error message if order fails
if (isset($_SESSION['order_error'])) {
    echo "<script>alert('{$_SESSION['order_error']}');</script>";
    unset($_SESSION['order_error']);
}

// close database connection
if ($mysqli) {
    $mysqli->close();
}

require_once "includes/nav.php";
?>

<body>
    <div class="main-container">
        <div class="header">
            <div class="page-view-title">
                <p>shopping cart</p> 
            </div>
        </div>
        <div class="sidebar"></div>
        <div class="main-content">
            <div class="right">
                <div class="cart-pg content-wrapper-cart">
                    <form action="cart.php?page=cart" method="post">
                        <table>
                            <thead>
                                <tr>
                                    <td colspan="2">product</td>
                                    <td>price</td>
                                    <td>quantity</td>
                                    <td>total</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="10" style="text-align:center; padding-top:40px;">you have no products added to your shopping cart</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="img">
                                        <a href="vinyl.php?id=<?= $product['vinylID'] ?>">
                                            <img src="<?= !empty($product['photo1']) ? htmlspecialchars($product['photo1']) : "images/no-image.jpg"; ?>" width="50" height="50" alt="<?= htmlspecialchars($product['title'] ?? 'unknown'); ?>">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="vinyl.php?id=<?= $product['vinylID'] ?>"><?= htmlspecialchars($product['title']) ?></a>
                                        <br>
                                        <a href="cart.php?remove=<?= $product['vinylID'] ?>" class="remove">remove</a>
                                    </td>
                                    <td class="price">&pound;<?= number_format($product['price'], 2) ?></td>
                                    <td class="quantity">
                                        <input type="number" name="quantity-<?= $product['vinylID'] ?>" value="<?= $products_in_cart[$product['vinylID']] ?? 1 ?>" min="1" max="<?= $product['quantity'] ?>" required>
                                    </td>
                                    <td class="price">&pound;<?= number_format($product['price'] * $products_in_cart[$product['vinylID']], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="totalAmount">
                            <span class="text">subtotal</span>
                            <span class="price">&pound;<?= number_format($totalAmount, 2); ?></span>
                        </div>
                        <div class="buttons">
                            <input type="submit" value="update" name="update">
                            <input type="submit" value="place order" name="placeorder">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    
<?php require_once "includes/footer.php"; ?>
</body>

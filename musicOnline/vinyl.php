<?php 
require_once "php/functions.php"; 
require_once "includes/nav.php";
require_once 'pageTitle.php';

// get vinyl id from the url
$vinylID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// fetch vinyl info from db
$vinyl = getVinylByID($vinylID);

// list of genres for sidebar
$genres = ['rock', 'pop', 'jazz', 'classical', 'hip-hop', 'folk', 'electronic'];

// set page title dynamically based on vinyl name
$title = !empty($vinyl['title']) ? htmlspecialchars($vinyl['title']) : "vinyl store";

// get the username of the seller
$username = getSoldByUsername($vinylID);

// get total amount
$totalAmount = $vinyl['price'];
?>

<title><?php echo $title; ?></title>

<body>
<div class="main-container">

    <div class="header">
        <div class="page-view-title">
            <p>vinyl details</p> 
        </div>
    </div>

    <div class="sidebar">
        <div class="desktop-sidebar">
            <ul>
                <?php foreach ($genres as $genre): ?>
                    <li>
                        <a href="genre.php?genre=<?php echo urlencode($genre); ?>">
                            <?php echo htmlspecialchars($genre); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="border-right">
        <?php if (!empty($vinyl)): ?> 
            <div class="vinyl-container">
                <div class="vinyl-image">
                    <?php 
                    // check if there's an image, otherwise use a placeholder
                    $imagePath = !empty($vinyl['photo1']) ? htmlspecialchars($vinyl['photo1']) : "images/no-image.jpg";
                    ?>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($vinyl['title'] ?? 'unknown'); ?>">
                </div>
                <div class="vinyl-details">
                    <h1 class="title"><?php echo htmlspecialchars($vinyl['title']); ?></h1>
                    <h3 class="artist"><?php echo htmlspecialchars($vinyl['artist']); ?></h3>
                    <p><strong>sold by:</strong> <?php echo (!empty($username) && is_string($username)) ? htmlspecialchars($username) : "N/A"; ?></p>
                    <p class="price">£<?php echo number_format($vinyl['price'] ?? 0, 2); ?></p>
                    <p><strong>genre:</strong> <?php echo htmlspecialchars($vinyl['genre']); ?></p>
                    <p><strong>format:</strong> <?php echo htmlspecialchars($vinyl['format']); ?></p>
                    <p><strong>release date:</strong> <?php echo htmlspecialchars($vinyl['releaseDate']); ?></p>
                    <p><strong>condition:</strong> <?php echo htmlspecialchars(strtolower($vinyl['vinylCondition'])); ?></p>

                    <!-- add to cart form -->
                    <form action="cart.php" method="post">
                        <!-- pass vinyl details to cart -->
                        <input type="hidden" name="vinylID" value="<?php echo $vinyl['vinylID']; ?>">
                        <input type="hidden" name="price" value="<?= $vinyl['price']; ?>">
                        <input type="hidden" name="quantity" value="1">  <!-- always adds 1 item to cart -->

                        <div class="quantity-container">
                            <label for="quantity"><strong>number of copies:</strong></label>
                            <?php if ($vinyl['quantity'] > 0): ?>
                                <p><?php echo htmlspecialchars($vinyl['quantity']); ?></p>
                            <?php else: ?>
                                <span style="color: red; font-weight: bold;">sold out</span>
                            <?php endif; ?>
                        </div>

                        <!-- show description if available -->
                        <p class="description">
                        <?php echo !empty($vinyl['vinylDescription']) ? nl2br(($vinyl['vinylDescription'])) : " "; ?>
                        </p>

                        <!-- different messages and buttons based on user role -->
                        <?php if (!isset($_SESSION['username'])) { ?>
                            <p style="color: #cc5500">you need to be logged in to place an order!</p>
                        <?php } elseif ($role == 'admin') { ?>
                            <p style="color: #cc5500;">admins cannot add items to the cart.</p>
                            <button type="button" class="add-to-cart" disabled style="background: gray; cursor: not-allowed;">add to cart</button>
                        <?php } else { ?>
                        <div class="cart-button-container">
                            <button type="submit" class="add-to-cart">add to cart</button>
                        </div>
                    <?php } ?>
                </form>
                </div>
            </div>
        <?php else: ?>
            <p>no vinyl records found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
</body>

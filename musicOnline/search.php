<?php 
require_once "php/functions.php"; 
require_once 'includes/nav.php';
require_once 'pageTitle.php';

$mysqli = dbConnect(); // connect to the database

// get the search keyword from the URL (if it exists)
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$keyword = trim($keyword); // remove extra spaces

if (!empty($keyword)) {
    // search for vinyls where the title or artist matches the keyword
    $sql = "SELECT vinylID, title, artist, photo1, price FROM vinylInfo WHERE title LIKE ? OR artist LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $searchTerm = "%{$keyword}%"; // wrap keyword in % for partial matches
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // if no keyword is entered, return an empty result set
    $result = $mysqli->query("SELECT vinylID, title, artist, photo1, price FROM vinylInfo WHERE 0");
}
?>

<body>
    <div class="main-container">
        <div class="header">
            <div class="page-view-title">
                <p>search results</p>
            </div>
        </div>

        <div class="sidebar"></div>

        <div class="main-content">
            <div class="right">
                <div class="all-vinyls">
                    <?php
                    if ($result->num_rows > 0) {
                        // loop through the results and display each vinyl
                        while ($row = $result->fetch_assoc()) {
                            $imagePath = !empty($row['photo1']) ? htmlspecialchars($row['photo1']) : "images/no-image.jpg"; ?>

                            <a href="vinyl.php?id=<?php echo intval($row['vinylID']); ?>" class="vinyl-card">
                                <img src="<?php echo $imagePath; ?>" 
                                    class="card-image" 
                                    alt="<?php echo htmlspecialchars($row['title'] ?? 'unknown'); ?>">
                                <h3 class="title"><?php echo htmlspecialchars($row['title'] ?? 'unknown'); ?></h3>
                                <h5 class="artist"><?php echo htmlspecialchars($row['artist'] ?? 'unknown'); ?></h5>
                                <p class="price">£<?php echo number_format($row['price'] ?? 0, 2); ?></p>
                            </a>

                    <?php
                        } // end while loop
                    } else {
                        // show message if no results found
                        echo "<p style='color: red; font-weight: bold;'>no records found for '<em>" . htmlspecialchars($keyword) . "</em>'</p>";
                    }
                    ?>
                </div>
            </div>    
        </div>
    </div>

<?php include "includes/footer.php"; ?>
</body>

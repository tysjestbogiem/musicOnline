<?php 

// include necessary files for functions, navigation, and page title
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// define available vinyl genres
$genres = ['rock', 'pop', 'jazz', 'classical', 'hip-hop', 'folk', 'electronic'];

?>

<?php
// check if a genre is selected from the URL parameters
if(isset($_GET['genre'])){
    $cat = urldecode($_GET['genre']); // decode URL parameter to get the selected genre
}
?>

<body>
    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>vinyl categories</p>
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <!-- display genre links in sidebar -->
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

            <div class="mobile-sidebar">
                <!-- dropdown menu for mobile users -->
                <form id="mobile-form" name="mobile-form" method="post">
                    <select id="genre-select" name="genre" onchange="navigateToGenre(this)">
                        <option value="">---- select genre ----</option>
                        <?php foreach ($genres as $genre): ?>
                            <!-- pre-select genre if user already selected one -->
                            <option value="<?php echo urlencode($genre); ?>" <?php echo ($cat === $genre) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($genre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

        </div>

        <div class="main-content">
            
            <div class="right">
                        
                <div class="all-vinyls">
                    <?php 
                    // retrieve selected genre from URL, or set to null if not provided
                    $cat = isset($_GET['genre']) ? urldecode($_GET['genre']) : null;

                    // fetch vinyl records filtered by selected genre
                    $vinyls = getImagesID($cat); 
                    
                    if (!empty($vinyls)) {
                        foreach ($vinyls as $vinyl) { 
                            // ensure vinyl ID exists before displaying
                            if (empty($vinyl['vinylID'])) {
                                continue; // skip if ID is missing
                            }

                            // set image path, use default if no image is available
                            $imagePath = !empty($vinyl['photo1']) ? $vinyl['photo1'] : "images/no-image.jpg";
                    ?>
                    
                    <!-- display vinyl record as a clickable card -->
                    <a href="vinyl.php?id=<?php echo intval($vinyl['vinylID']); ?>" class="vinyl-card">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                            class="card-image" 
                            alt="<?php echo htmlspecialchars($vinyl['title'] ?? 'unknown'); ?>">
                        <h3 class="title"><?php echo htmlspecialchars($vinyl['title'] ?? 'unknown'); ?></h3>
                        <h5 class="artist"><?php echo htmlspecialchars($vinyl['artist'] ?? 'unknown'); ?></h5>
                        <p class="price">£<?php echo number_format($vinyl['price'] ?? 0, 2); ?></p>
                    </a>

                    <?php 
                        } 
                    } else {  
                        // show message if no vinyls are found in the selected genre
                        echo "<p>No vinyls available at the moment.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    // function to redirect user to selected genre page when dropdown is used
    function navigateToGenre(select) {
        let genre = select.value;
        if (genre) {
            window.location.href = "genre.php?genre=" + encodeURIComponent(genre);
        }
    }
</script>

<?php include "includes/footer.php" ?>

<?php 

require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// list of available genres
$genres = ['rock', 'pop', 'jazz', 'classical', 'hip-hop', 'folk', 'electronic'];

// check if a genre was selected from the url
$selectedGenre = isset($_GET['genre']) ? urldecode($_GET['genre']) : null;

// fetch vinyls based on selected genre
$vinyls = getImagesID($selectedGenre);

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
                <ul>
                    <?php foreach ($genres as $genre): ?>
                        <li>
                            <a href="genre.php?genre=<?php echo urlencode($genre); ?>" 
                            class="<?php echo ($selectedGenre === $genre) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($genre); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="mobile-sidebar">
                <form id="mobile-form" name="mobile-form">
                    <select id="genre-select" name="genre" onchange="navigateToGenre(this)">
                        <option value="">---- select genre ----</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?php echo urlencode($genre); ?>" 
                                <?php echo ($selectedGenre === $genre) ? 'selected' : ''; ?> >
                                <?php echo htmlspecialchars($genre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="main-content">
            <div class="all-vinyls">
                <?php 
                if (!empty($vinyls)) {
                    foreach ($vinyls as $vinyl) { 
                        if (!isset($vinyl['vinylID'])) {
                            continue; // skip if vinyl id is missing
                        }
                        $imagePath = !empty($vinyl['photo1']) ? $vinyl['photo1'] : "images/no-image.jpg";?>
                
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
                        echo "<p>no vinyls available in this category.</p>";
                    }
                    ?>
            </div>
        </div>
    </div>
</body>

<?php include "includes/footer.php"; ?>

<script>
    function navigateToGenre(select) {
        let genre = select.value;
        if (genre) {
            window.location.href = "genre.php?genre=" + encodeURIComponent(genre);
        }
    }
</script>

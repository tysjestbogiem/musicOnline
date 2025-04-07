<?php 
// include necessary files for functions, navigation, and page title
require_once "php/functions.php"; 
require_once "includes/nav.php"; 
require_once 'pageTitle.php';
?>

<!-- hero section with background image -->
<section id="hero" style="background: url('images/black-vinyl.webp') no-repeat center center/cover;">
    <h1>shop</h1>
    <h1>with </h1>
    <h1><span style="color: #cc5500;">m</span>usic <span style="color: #cc5500;">O</span>nline</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    <a href="shop.php" class="cta-button">shop now</a>
</section>

<!-- advertisement section placeholder -->
<section id="advertisment">
    <div class="advertisment-section">
        <p>place for advertisment</p>
        <!-- print_r($_SESSION); -->
    </div>
</section>

<!-- section displaying newly added vinyls -->
<section id="new-vinyls" class="container swiper"> 
    <div class="slider-header">
        <h2>new vinyls</h2>
        <a href="shop.php" class="view-all-button">shop now</a>
    </div>

    <div class="card-wrapper">
        <!-- swiper slider container for vinyls -->
        <ul class="card-list swiper-wrapper">
            <?php 
            // fetch up to 13 vinyl records from the database
            $vinyls = getVinylsCard(13);

            // check if records exist before displaying them
            if (!empty($vinyls)) {  
                foreach ($vinyls as $vinyl) { 
            ?>
                <li class="card-item swiper-slide">
                    <a href="vinyl.php?id=<?php echo intval($vinyl['vinylID']); ?>" class="card-link">
                        
                        <?php 
                        // check if vinyl has an image, otherwise set default
                        $imagePath = !empty($vinyl['photo1']) ? $vinyl['photo1'] : "images/no-image.jpg";
                        ?>

                        <img src="<?php echo $imagePath; ?>" class="card-image" alt="<?php echo htmlspecialchars($vinyl['title']); ?>">
                        
                        <div class="card-details">
                            <h4 class="card-title"><?php echo htmlspecialchars($vinyl['title']); ?></h4>
                            <h5 class="card-artist"><?php echo htmlspecialchars($vinyl['artist']); ?></h5>
                            <p class="card-description"><?php echo htmlspecialchars($vinyl['vinylDescription']); ?></p>
                            <p class="card-price">£<?php echo number_format($vinyl['price'], 2); ?></p>
                        </div>
                    </a>
                </li>
            <?php 
                } // end foreach
            } else {  
                echo "<p>No vinyls available.</p>";
            }
            ?>
        </ul>

        <!-- Swiper navigation buttons -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>

<!-- section displaying different vinyl genres -->
<section id="genre" class="container swiper">
    <div class="slider-header">
        <h2>genre</h2>
        <a href="shop.php" class="view-all-button">shop now</a>
    </div>

    <div class="card-wrapper">
        <ul class="card-list swiper-wrapper">
            <!-- Loop through hardcoded genres -->
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=rock" class="card-link">
                    <img src="images/Nevermind.jpg" alt="Rock" class="card-image">
                    <h4 class="card-title">rock</h4>
                </a>
            </li>
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=electronic" class="card-link">
                <img src="images/Random-Access-Memories.jpg" alt="Electronic" class="card-image">
                <h4 class="card-title">electronic</h4>
                </a>
            </li>
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=pop" class="card-link">
                    <img src="images/Thriller.jpg" alt="Pop" class="card-image">
                    <h4 class="card-title">pop</h4>
                </a>
            </li>
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=classical" class="card-link">
                    <img src="images/Bach.jpg" alt="Classical" class="card-image">
                    <h4 class="card-title">classical</h4>
                </a>
            </li>
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=hip-hop" class="card-link">
                    <img src="images/DAMN.jpg" alt="Hip Hop" class="card-image">
                    <h4 class="card-title">hip hop</h4>
                </a>
            </li>
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=folk" class="card-link">
                    <img src="images/Hounds-of-Love.jpg" alt="Folk, World & Country" class="card-image">
                    <h4 class="card-title">folk</h4>
                </a>
            </li>
            <li class="card-item swiper-slide">
                <a href="genre.php?genre=jazz" class="card-link">
                    <img src="images/The-Immortal-King-Oliver.jpg" alt="Jazz" class="card-image">
                    <h4 class="card-title">jazz</h4>
                </a>
            </li>
        </ul>
        <!-- Swiper navigation buttons -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>

<!-- hero section for selling vinyls -->
<section id="hero-sell" style="background: url('images/orange-vinyl.png') no-repeat center center/cover;">
    <h1>sell<br>with</h1>
    <h1><span style="color: #cc5500;">m</span>usic <span style="color: #cc5500;">O</span>nline</h1>   
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    <a href="sell.php" class="cta-button">sell now</a>
</section>

<!-- section displaying customer comments -->
<section id="comment" class="container swiper">
    <div class="slider-header">
        <h2>customer comments</h2>
    </div>
    <div class="card-wrapper">
        <ul class="card-list swiper-wrapper">
            <!-- loop to display multiple dummy customer reviews -->
            <?php for ($i = 0; $i < 5; $i++): ?>
            <li class="card-item swiper-slide">
                <div class="card-link">
                    <p class="card-title">username</p>
                    <div class="start">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
                </div>
            </li>
            <?php endfor; ?>
        </ul>

        <!-- Swiper navigation buttons -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>

<!-- footer -->
<?php include "includes/footer.php"; ?>

<!-- swiper JS for carousel effect -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- external JavaScript file -->
<script src="javascript/script.js"></script>

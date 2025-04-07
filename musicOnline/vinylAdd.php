<?php 
require_once "php/functions.php";
require_once "includes/nav.php";
require_once 'pageTitle.php';

// check if database is connected
if (!$dbConnect) {
    die("database connection failed: " . mysqli_connect_error());
}

// get seller id from session
$userID = $_SESSION['userID']; 

// get current page name for sidebar highlight
$currentPage = basename($_SERVER['PHP_SELF']);

// seller menu for the sidebar
$sellerMenu = [
    "dashboard" => "sell.php",
    "your vinyls" => "vinyls.php",
    "add" => "vinylAdd.php",
    "edit" => "vinylEdit.php",
    "orders" => "orders.php",
    "logout" => "logout.php"
];

// list of genres and formats for dropdowns
$genres = ['rock', 'pop', 'jazz', 'classical', 'hip-hop', 'folk', 'electronic'];    
$formats = ['LP', 'EP', 'single', 'mixtape', 'live album'];

// get unique conditions for dropdown
$vinylConditionQuery = "SELECT DISTINCT vinylCondition FROM vinylInfo";
$vinylConditionResult = @mysqli_query($dbConnect, $vinylConditionQuery);

// check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // validate required fields
    $title = !empty($_POST['title']) ? trim($_POST['title']) : $errors[] = "you need to enter a title.";
    $artist = !empty($_POST['artist']) ? trim($_POST['artist']) : $errors[] = "you need to enter an artist.";
    $releaseDate = !empty($_POST['releaseDate']) ? trim($_POST['releaseDate']) : $errors[] = "you need to enter a release date.";
    $price = !empty($_POST['price']) ? trim($_POST['price']) : $errors[] = "you need to enter a price.";
    $genre = !empty($_POST['genre']) ? trim($_POST['genre']) : $errors[] = "you need to select a genre.";
    $format = !empty($_POST['format']) ? trim($_POST['format']) : $errors[] = "you need to enter a format.";
    $vinylCondition = !empty($_POST['vinylCondition']) ? trim($_POST['vinylCondition']) : $errors[] = "you need to enter a condition.";
    $quantity = !empty($_POST['quantity']) ? trim($_POST['quantity']) : $errors[] = "you need to enter quantity.";
    $vinylDescription = isset($_POST['vinylDescription']) ? trim($_POST['vinylDescription']) : "";

    // image upload directory
    $uploadDirectory = "images/";

    // check if directory exists, if not, create it
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // generate a unique filename to avoid duplicates
    $filename = uniqid() . "_" . basename($_FILES["uploadfile"]["name"]);
    $filePath = $uploadDirectory . $filename;

    // allowed file types and max size
    $allowedFileTypes = ['image/jpeg', 'image/png'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB max

    // handle image upload
    if (!empty($_FILES['uploadfile']['name'])) {
        $tempname = $_FILES["uploadfile"]["tmp_name"];
        $fileType = mime_content_type($tempname); // get file type
        $fileSize = $_FILES["uploadfile"]["size"]; // get file size

        // validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            $errors[] = "only jpg and png images are allowed.";
        }

        // validate file size
        if ($fileSize > $maxFileSize) {
            $errors[] = "file size should not exceed 2MB.";
        }

        // upload file if no errors
        if (empty($errors)) {
            if (move_uploaded_file($tempname, $filePath)) {
                echo "<script>alert('image uploaded successfully!');</script>";
            } else {
                echo "<script>alert('failed to upload file.');</script>";
                $filePath = null; // set to NULL if upload fails
            }
        } else {
            $filePath = null; // set to NULL if validation fails
        }
    } else {
        $errors[] = "you must upload an image to add new vinyl.";
        $filePath = null;
    }

    // insert data if no errors
    if (empty($errors)) {
        $insertSuccess = addVinylQuery($title, $artist, $releaseDate, $price, $genre, $format, $vinylCondition, $vinylDescription, $filePath, $userID, $quantity);

        if ($insertSuccess) {
            echo "<script>
                alert('vinyl record added successfully!');
                window.location.href = 'vinyls.php'; 
            </script>";
        } else {
            echo "<script>alert('database error: vinyl was not inserted.');</script>";
        }
    } else {
        echo "<script>alert('please fix the following errors: " . implode(', ', $errors) . "');</script>";
    }
}

// close database connection
if ($dbConnect) {
    mysqli_close($dbConnect);
}
?>

<body>

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>shop manager > add new vinyl</p> 
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
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $sellMen; ?>
                        </option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>
        </div>

        <div class="main-content"> 

            <div class="right">
                <form action="vinylAdd.php" method="POST" enctype="multipart/form-data">

                    <label>title *</label>
                    <input type="text" name="title" required>

                    <label>artist *</label>
                    <input type="text" name="artist" required>

                    <div class="double-box">
                        <div>
                            <label>genre *</label>
                            <select name="genre" required>
                                <option value="" disabled selected>choose genre</option>
                                <?php foreach ($genres as $genre) { ?>
                                    <option value="<?= htmlspecialchars($genre) ?>">
                                        <?= htmlspecialchars($genre) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div>
                            <label>format *</label>
                            <select name="format" required>
                                <option value="" disabled selected>choose format</option>
                                <?php foreach ($formats as $format) { ?>
                                    <option value="<?= htmlspecialchars($format) ?>">
                                        <?= htmlspecialchars($format) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="double-box">
                        <div>
                            <label>release date *</label>
                            <input type="date" name="releaseDate" required>
                        </div>
                        <div>
                            <label>price *</label>
                            <input type="number" step="0.01" name="price" required>
                        </div>
                    </div>

                    <div class="double-box">
                        <div>
                            <label>condition</label>
                            <select class="condition-box" name="vinylCondition" required>
                                <option value="" disabled selected>choose condition</option>
                                <?php while ($row = mysqli_fetch_array($vinylConditionResult, MYSQLI_ASSOC)) { ?>
                                    <option value="<?= ($row['vinylCondition']) ?>">
                                        <?= ($row['vinylCondition']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div>
                            <label>quantity *</label>
                            <input type="number" step="1" name="quantity" min="1" required>
                        </div>
                    </div>


                    <label>upload image *</label>
                    <input class="form-control" type="file" name="uploadfile" required>

                    <label>description</label>
                    <textarea name="vinylDescription"></textarea>

                    <div class="submit-btn-container">
                        <button class="submit-btn" type="submit" name="submit">add vinyl</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

<?php include "includes/footer.php" ?>

<script>
    function navigateToMenu(select) {
        let url = select.value;
        if (url) {
            window.location.href = url;
        }
    }
</script>

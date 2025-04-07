<?php 
// include necessary files for functions, navigation, and page title
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

$pageTitle = 'Edit Vinyl';
$currentPage = basename($_SERVER['PHP_SELF']);

// seller menu for sidebar navigation
$sellerMenu = [
    "dashboard" => "sell.php",
    "your vinyls" => "vinyls.php",
    "add" => "vinylAdd.php",
    "edit" => "vinylEdit.php",
    "orders" => "orders.php",
    "logout" => "logout.php"
];

// predefined genres and formats for dropdown selection
$genres = ['rock', 'pop', 'jazz', 'classical', 'hip-hop', 'folk', 'electronic'];    
$formats = ['LP', 'EP', 'single', 'mixtape', 'live album'];

// fetch distinct vinyl conditions for dropdown
$vinylConditionQuery = "SELECT DISTINCT vinylCondition FROM vinylInfo";
$vinylConditionResult = @mysqli_query($dbConnect, $vinylConditionQuery);

// ensure the seller is logged in before allowing access
if (!isset($_SESSION['userID'])) {
    die("Error: userID not found.");
}
$userID = $_SESSION['userID']; 

// connect to the database
$mysqli = dbConnect(); 

// validate vinylID from GET or POST request
if (isset($_GET['vinylID']) && is_numeric($_GET['vinylID'])) {
    $vinylID = $_GET['vinylID'];
} elseif (isset($_POST['vinylID']) && is_numeric($_POST['vinylID'])) {
    $vinylID = $_POST['vinylID'];
} else {
    echo '<p>Error. Access invalid.</p>';
    include "includes/footer.php";
    exit();
}

// fetch existing vinyl record from the database
$query = "SELECT * FROM vinylInfo WHERE vinylID=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $vinylID);
$stmt->execute();
$result = $stmt->get_result();
$vinyl = $result->fetch_assoc();

// check if the vinyl exists before proceeding
if (!$vinyl) {
    echo "<p>Error: Vinyl not found.</p>";
    include "includes/footer.php"; // include footer before exiting
    exit();
}

// process form submission for updating vinyl record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // validate and trim input fields
    $title = !empty($_POST['title']) ? trim($_POST['title']) : $errors[] = "You need to enter a title.";
    $artist = !empty($_POST['artist']) ? trim($_POST['artist']) : $errors[] = "You need to enter an artist.";
    $releaseDate = !empty($_POST['releaseDate']) ? trim($_POST['releaseDate']) : $errors[] = "You need to enter a release date.";
    $price = !empty($_POST['price']) ? trim($_POST['price']) : $errors[] = "You need to enter a price.";
    $genre = !empty($_POST['genre']) ? trim($_POST['genre']) : $errors[] = "You need to select a genre.";
    $format = !empty($_POST['format']) ? trim($_POST['format']) : $errors[] = "You need to enter a format.";
    $vinylCondition = !empty($_POST['vinylCondition']) ? trim($_POST['vinylCondition']) : $errors[] = "You need to enter a condition.";
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $vinylDescription = isset($_POST['vinylDescription']) ? trim($_POST['vinylDescription']) : "";

    // directory for storing images
    $uploadDirectory = "images/";

    // generate a unique filename for image uploads
    $filename = uniqid() . "_" . basename($_FILES["uploadfile"]["name"]);
    $filePath = $uploadDirectory . $filename;

    // check if directory exists, if not, create it
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // process image upload if a file is provided
    if (!empty($_FILES['uploadfile']['name'])) {
        $tempname = $_FILES["uploadfile"]["tmp_name"];

        if (move_uploaded_file($tempname, $filePath)) {
            echo "<script>alert('Image uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Failed to upload file.');</script>";
            $filePath = null; // set to NULL if upload fails
        }
    } else {
        $filePath = null; // if no file uploaded, set to null
    }

    // if no errors, proceed with updating the record
    if (empty($errors)) {
        // update the vinyl record in the database
        $query = "UPDATE vinylInfo 
                  SET title=?, artist=?, releaseDate=?, price=?, genre=?, format=?, vinylCondition=?, quantity=?, vinylDescription=?, photo1=? 
                  WHERE vinylID=?";
        $stmt = $mysqli->prepare($query);

        mysqli_stmt_bind_param($stmt, "sssdsssissi", $title, $artist, $releaseDate, $price, $genre, $format, $vinylCondition, $quantity, $vinylDescription, $filePath, $vinylID);

        // check if update was successful
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Vinyl has been updated successfully!');
                    window.location.href = 'vinylEdit.php'; 
                    // Redirect back to vinyls edit list
                  </script>";
        } else {
            echo '<p class="error">The record could not be updated due to a system error.</p>';
        }

        mysqli_stmt_close($stmt);
    } else {
        // display errors, if any
        echo '<p class="error">The following errors occurred:</p>';
        foreach ($errors as $msg) {
            echo "<p>- $msg</p>";
        }
    }
}

// close database connection
mysqli_close($mysqli);
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
        <!-- display seller menu in the sidebar -->
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
        <!-- dropdown menu for mobile view -->
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

<!-- form for editing vinyl -->
<div class="main-content">
    <div class="right">
        <form action="edit.php?vinylID=<?php echo $vinylID; ?>" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="vinylID" value="<?php echo $vinylID; ?>">

        <label>title *</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($vinyl['title']); ?>" required>

        <label>artist *</label>
        <input type="text" name="artist" value="<?php echo htmlspecialchars($vinyl['artist']); ?>" required>

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

        <div class="upload-images">
            <div class="form-group">
                <input class="form-control" type="file" name="uploadfile" value="" />
            </div>
            <!-- <p>image cannot be changed</p> -->
        </div>



        <label>description</label>
        <textarea name="vinylDescription"><?php echo ($vinyl['vinylDescription']); ?></textarea>

        <div class="submit-btn-container">
            <button class="submit-btn" type="submit">update vinyl</button>
        </div>
        </form>
    </div>
</div>
</div>
</body>

<?php require_once 'includes/footer.php' ?>

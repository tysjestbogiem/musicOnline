<?php 
// start session once to prevent duplicate session issues
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include necessary files for functions, navigation, and page title
require_once "php/functions.php";
require_once 'includes/nav.php';
require_once 'pageTitle.php';

// define the admin menu for sidebar navigation
$adminMenu = [
    "dashboard" => "admin.php",
    "all users" => "allUsers.php",
    "all vinyls" => "allVinyls.php",
    "edit" => "editAllVinyls.php",
    "all orders" => "allOrders.php",
    "settings" => "musicOnline.php",
    "logout" => "logout.php"
];

// predefined genre and format options for dropdown selection
$genres = ['rock', 'pop', 'jazz', 'classical', 'hip-hop', 'folk', 'electronic'];    
$formats = ['LP', 'EP', 'single', 'mixtape', 'live album'];

// get current page name for highlighting the menu
$currentPage = basename($_SERVER['PHP_SELF']);

// ensure admin is logged in before granting access
if (!isset($_SESSION['adminID']) || !isset($_SESSION['role'])) {
    die("error: unauthorised access.");
}

// get user ID and role from session data
$userID = $_SESSION['userID']; 
$role = $_SESSION['role']; 

// connect to the database
$mysqli = dbConnect(); 

// ensure vinyl ID is provided in the URL
if (isset($_GET['vinylID'])) {
    $vinylID = intval($_GET['vinylID']);
} else {
    die("error: vinyl ID is missing.");
}

// retrieve vinyl details from the database
$query = "SELECT * FROM vinylInfo WHERE vinylID=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $vinylID);
$stmt->execute();
$result = $stmt->get_result();
$vinyl = $result->fetch_assoc();

// check if the vinyl exists before proceeding
if (!$vinyl) {
    echo "<p>error: vinyl not found.</p>";
    include "includes/footer.php";
    exit();
}

// fetch distinct vinyl conditions for the dropdown
$vinylConditionQuery = "SELECT DISTINCT vinylCondition FROM vinylInfo";
$vinylConditionResult = mysqli_query($mysqli, $vinylConditionQuery);

// process form submission when updating vinyl
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    // validate input fields and trim whitespace
    $title = !empty($_POST['title']) ? trim($_POST['title']) : $errors[] = "you need to enter a title.";
    $artist = !empty($_POST['artist']) ? trim($_POST['artist']) : $errors[] = "you need to enter an artist.";
    $releaseDate = !empty($_POST['releaseDate']) ? trim($_POST['releaseDate']) : $errors[] = "you need to enter a release date.";
    $price = !empty($_POST['price']) ? floatval($_POST['price']) : $errors[] = "you need to enter a price.";
    $genre = !empty($_POST['genre']) ? trim($_POST['genre']) : $errors[] = "you need to select a genre.";
    $format = !empty($_POST['format']) ? trim($_POST['format']) : $errors[] = "you need to enter a format.";
    $vinylCondition = !empty($_POST['vinylCondition']) ? trim($_POST['vinylCondition']) : $errors[] = "you need to enter a condition.";
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $vinylDescription = isset($_POST['vinylDescription']) ? trim($_POST['vinylDescription']) : "";

    // define upload directory for images
    $uploadDirectory = "images/";

    // check if directory exists, create it if not
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // check if the user uploaded a new image
    if (!empty($_FILES['uploadfile']['name'])) {
        $filename = uniqid() . "_" . basename($_FILES["uploadfile"]["name"]);
        $filePath = $uploadDirectory . $filename;
        $tempname = $_FILES["uploadfile"]["tmp_name"];

        // try to move the uploaded file
        if (move_uploaded_file($tempname, $filePath)) {
            echo "<script>alert('image uploaded successfully!');</script>";
        } else {
            echo "<script>alert('failed to upload file.');</script>";
            $filePath = $vinyl['photo1']; // keep old image if upload fails
        }
    } else {
        $filePath = $vinyl['photo1']; // keep old image if no new file is uploaded
    }

    // proceed only if there are no validation errors
    if (empty($errors)) {
        $query = "UPDATE vinylInfo 
                  SET title=?, artist=?, releaseDate=?, price=?, genre=?, format=?, vinylCondition=?, quantity=?, vinylDescription=?, photo1=? 
                  WHERE vinylID=?";
        $stmt = $mysqli->prepare($query);

        // ensure $filePath is always set
        mysqli_stmt_bind_param($stmt, "sssdssssssi", $title, $artist, $releaseDate, $price, $genre, $format, $vinylCondition, $quantity, $vinylDescription, $filePath, $vinylID);

        // check if the update was successful
        if ($stmt->execute()) {
            echo "<script>alert('vinyl has been updated successfully!');
                    window.location.href = 'editAllVinyls.php'; 
                  </script>";
        } else {
            echo "<p class='error'>update failed: " . $stmt->error . "</p>";
        }

        mysqli_stmt_close($stmt);
    } else {
        // display validation errors
        echo '<p class="error">the following errors occurred:</p>';
        foreach ($errors as $msg) {
            echo "<p>- $msg</p>";
        }
    }
}

// close database connection
mysqli_close($mysqli);
?>

<body class="<?php echo $bodyClass; ?>">

    <div class="main-container">

        <div class="header">
            <div class="page-view-title">
                <p>administrator account > edit vinyls</p> 
            </div>
        </div>

        <div class="sidebar">

            <div class="desktop-sidebar">
                <!-- loop through admin menu for navigation -->
                <?php if (isset($_SESSION['userID'])) { ?>
                    <ul>
                        <?php foreach ($adminMenu as $adMen => $url): 
                            $active = (basename($url) == $currentPage) ? 'data-active="active"' : ""; ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $adMen; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>

            <div class="mobile-sidebar">
                <!-- dropdown menu for mobile navigation -->
                <form id="mobile-form" name="mobile-form">
                    <select id="menu-select" name="menu" onchange="navigateToMenu(this)">
                        <option value="">---- select menu option ----</option>
                        <?php foreach ($adminMenu as $adMen => $url): 
                            $active = (basename($url) == $currentPage) ? 'selected' : ""; ?>
                            <option value="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $adMen; ?></option>
                        <?php endforeach; ?>
                    </select> 
                </form> 
            </div>
        </div>

        <div class="main-content">
            <div class="right">
                <!-- form for editing vinyl details -->
                <form action="editAllVinylsAdmin.php?vinylID=<?php echo $vinylID; ?>" method="POST" enctype="multipart/form-data">

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
                    
                    <label>upload image *</label>
                    <input class="form-control" type="file" name="uploadfile" required>

                    <label>description</label>
                    <textarea name="vinylDescription"><?php echo htmlspecialchars($vinyl['vinylDescription']); ?></textarea>

                    <div class="submit-btn-container">
                        <button class="submit-btn" type="submit">update vinyl</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include "includes/footer.php" ?>

</body>

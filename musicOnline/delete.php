<?php 
// include necessary files for functions, navigation, and page title
require_once "php/functions.php"; 
require_once 'includes/nav.php';
require_once 'pageTitle.php';

$pageTitle = 'Delete Vinyl';
$currentPage = basename($_SERVER['PHP_SELF']);

// ensure the user is logged in before allowing access
if (!isset($_SESSION['userID'])) {
    die("Error: User ID not found."); // stop execution if user is not logged in
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
    die("<p>Error. Access invalid.</p>"); // prevent invalid access
}

// fetch the existing vinyl record to confirm its existence
$query = "SELECT * FROM vinylInfo WHERE vinylID=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $vinylID);
$stmt->execute();
$result = $stmt->get_result();
$vinyl = $result->fetch_assoc();

// check if the vinyl exists
if (!$vinyl) {
    echo "<p>Error: Vinyl not found.</p>";
    include "includes/footer.php"; // include footer before exiting
    exit();
}

// process the delete request if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accepted']) && $_POST['accepted'] === 'yes') {
        // use a prepared statement to securely delete the vinyl record
        $stmt = $mysqli->prepare("DELETE FROM vinylInfo WHERE vinylID = ? LIMIT 1");
        $stmt->bind_param("i", $vinylID);
        $stmt->execute();

        // check if a record was successfully deleted
        if ($stmt->affected_rows === 1) {
            echo "<script>alert('Vinyl has been deleted successfully.');
                    window.location.href = 'vinylEdit.php'; 
                    // redirect to vinyl edit list
                  </script>";
        } else {
            echo "<script>alert('Something went wrong, vinyl was not deleted.')</script>";
            echo '<p>Error: ' . $mysqli->error . '</p>'; // display error if deletion fails
        }

        $stmt->close();
    } else {
        // if the deletion is cancelled
        echo "<script>alert('Deletion cancelled. Vinyl was not deleted.');
        window.location.href = 'vinylEdit.php'; // redirect to vinyls list
      </script>";;
    }
}
?>

<body>
<main class="middle-page-container">     

    <div class="delete-box">
        <!-- confirmation message for vinyl deletion -->
        <p>are you sure that you want to delete<br>
        <?php echo htmlspecialchars($vinyl['artist']) . "<br>" . htmlspecialchars($vinyl['title']); ?>
        </p>

        <!-- form to confirm deletion -->
        <form action="delete.php" method="POST" class="delete-form">
            <div class="radio-group">
                <label><input type="radio" name="accepted" value="yes"> yes</label>
                <label><input type="radio" name="accepted" value="no"> no</label>
            </div>
        
            <input type="hidden" name="vinylID" value="<?php echo $vinylID; ?>">
        
            <input type="submit" name="Submit" value="Submit" class="submit-delete">
        </form>
    </div>

</main>   
</body>

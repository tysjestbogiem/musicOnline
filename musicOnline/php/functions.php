<?php

// Include the database connection file
require_once "myConnect.php";

/**
 * Function to connect to the database
 * Returns the database connection or null on failure
 */
function dbConnect() {
    $mysqli = new mysqli(HOSTNAME, USERNAME, PWRD, DBNAME);

    if ($mysqli->connect_errno) {
        die("Database connection failed: " . $mysqli->connect_error);
    }
    return $mysqli;
}

/**
 * Get all unique genres from `vinylInfo`
 */
function getGenres() {
    $mysqli = dbConnect();
    $result = $mysqli->query("SELECT DISTINCT genre FROM vinylInfo");

    return $result->fetch_all(MYSQLI_ASSOC);
}
/**
 * Get random vinyl records (default = 10)
 */
function getVinylsCard($limit = 10) {
    $mysqli = dbConnect();
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 *  Get all vinyls by date from newste to old
 */
function getVinylsCardByDate($limit = 10) {
    $mysqli = dbConnect();
    
    // Prepare statement
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo ORDER BY createdAT DESC LIMIT ?");
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 *  Get user username
 */
function getUserUsername() {
    $mysqli = dbConnect();
    $stmt = $mysqli->prepare(
        "SELECT * FROM vinylInfo ORDER BY createdAT DESC LIMIT ?");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get username of user that added vinyl -- seller
 */
function getUsernameByVinyl($vinylID) {
    $mysqli = dbConnect(); 

    $stmt = $mysqli->prepare(
        "SELECT ui.username
        FROM userInfo ui
        JOIN vinylInfo vi ON ui.userID = vi.createdBY
        WHERE vi.vinylID = ?"
    );

    $stmt->bind_param("i", $vinylID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return $result ? $result['username'] : "Unknown";
}


/**
 * Get a single random image per genre
 */
function getGenreImage() {
    $mysqli = dbConnect();
    
    $query = "
        SELECT DISTINCT v.genre, 
               (SELECT photo1 FROM vinylInfo WHERE genre = v.genre ORDER BY RAND() LIMIT 1) AS photo1
        FROM vinylInfo v
    ";

    $result = $mysqli->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get vinyls by genre
 */
function getVinylsByGenre($genre) {
    $mysqli = dbConnect();
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo WHERE genre = ? ORDER BY RAND()");
    $stmt->bind_param("s", $genre);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllVinyls() {
    $mysqli = dbConnect();
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo ORDER BY createdAT DESC");

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


/**
 * Get vinyl by title
 */
function getVinylByTitle($title) {
    $mysqli = dbConnect();
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get vinyl by ID
 */
function getVinylByID($vinylID) {
    $mysqli = dbConnect();
    
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo WHERE vinylID = ?");
    $stmt->bind_param("i", $vinylID);
    $stmt->execute();
    
    $result = $stmt->get_result()->fetch_assoc();
    return $result ?: [];
}


/**
 * Get images for vinyls
 */
function getImages() {
    $mysqli = dbConnect();
    $result = $mysqli->query("SELECT vinylID, title, artist, price, photo1 FROM vinylInfo");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get images filtered by genre
 */
function getImagesID($genre = null) {
    $mysqli = dbConnect();
    
    if ($genre) {
        $stmt = $mysqli->prepare("SELECT vinylID, title, artist, price, photo1 FROM vinylInfo WHERE genre = ?");
        $stmt->bind_param("s", $genre);
    } else {
        $stmt = $mysqli->prepare("SELECT vinylID, title, artist, price, photo1 FROM vinylInfo");
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Redirect user based on their role
 */
function loadCorrectPage($user_id, $dbConnect) {
    $stmt = $dbConnect->prepare("SELECT role FROM userInfo WHERE userID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && isset($user['role'])) {
        switch ($user['role']) {
            case 'user':
                header("Location: home.php");
                exit();
            case 'admin':
                header("Location: admin.php");
                exit();
        }
    }
}

/**
 * Get orders where this user is the seller
 */
function getSellersOrders($userID, $limit = 3) {
    $mysqli = dbConnect();
    
    $stmt = $mysqli->prepare("
        SELECT vi.vinylID, vi.title, vi.artist, vi.price, vi.photo1, vi.createdBY AS sellerID, 
               oi.userID AS buyerID, oi.orderDate, oi.totalAmount, 
               oi.orderID, oi.addressID, oi.totalAmount, 
               oi.orderDate, oi.userID, 
               oi.totalAmount, oi.addressID, 
               SUM(oiItems.quantity) AS quantitySold
        FROM orderInfo AS oi
        INNER JOIN orderItems AS oiItems ON oi.orderID = oiItems.orderID  
        INNER JOIN vinylInfo AS vi ON oiItems.vinylID = vi.vinylID  
        WHERE vi.createdBY = ?
        GROUP BY vi.vinylID, oi.orderID  
        ORDER BY oi.orderDate DESC
        LIMIT ?
    ");
    
    $stmt->bind_param("ii", $userID, $limit); 
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


/**
 * Get vinyls sorted by seller and by date (newest first)
 */
function getVinylsBySellerDate($userID) {
    $mysqli = dbConnect();
    
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo WHERE createdBY = ? ORDER BY createdAT DESC");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get vinyls sorted by seller in alphabetical order
 */
function getVinylsBySellerAtoZ($userID) {
    $mysqli = dbConnect();
    
    $stmt = $mysqli->prepare("SELECT * FROM vinylInfo WHERE createdBY = ? ORDER BY artist ASC, title ASC");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); //all rows
}



/**
 * Get all vinyls bought by the user
 */
function getBoughtVinylsByUser($userID) {
    $mysqli = dbConnect();
    
    $stmt = $mysqli->prepare("
        SELECT v.vinylID, v.title, v.artist, v.price, v.photo1, 
               o.orderDate, oi.quantity, oi.price AS itemPrice
        FROM orderInfo o
        JOIN orderItems oi ON o.orderID = oi.orderID  
        JOIN vinylInfo v ON oi.vinylID = v.vinylID  
        WHERE o.userID = ?
        ORDER BY o.orderDate DESC
    ");
    
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 *  Adds user details to db
 */

function addAccountDetails() {

    $mysqli = dbConnect();

    $query = "INSERT INTO userInfo(firstName, surname, email)
        VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($mysqli, $query)) {
            
        // Bind Parameters
        mysqli_stmt_bind_param($stmt, 'sss', $firstName, $surname, $email);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;  // Success
        } else {
            error_log("Execute failed: " . mysqli_stmt_error($stmt)); 
            // logs error, without showing users sql error
        }

        mysqli_stmt_close($stmt);
    } else {
        error_log("Prepare failed: " . mysqli_error($mysqli)); // log error instead of stopping execution
    }

    return false; 
}

/**
 *  Add new vinyl and image to database
 */
function addVinylQuery($title, $artist, $releaseDate, $price, $genre, $format, $vinylCondition, $vinylDescription, $filename, $userID, $quantity) {
    // Use global database connection
    $mysqli = dbConnect();


    // description is optional so allow for null
    $vinylDescription = !empty($vinylDescription) ? $vinylDescription : null;

    // secure SQL Query
    $query = "INSERT INTO vinylInfo (title, artist, releaseDate, price, genre, format, vinylCondition, vinylDescription, photo1, createdBY, quantity) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Debugging: Log the query execution
    error_log("Executing Query: " . $query);

    // Prepare statement
    if ($stmt = mysqli_prepare($mysqli, $query)) {
        
        // Bind Parameters
        mysqli_stmt_bind_param(
            $stmt, 
            'sssdssssssi', 
            $title, 
            $artist, 
            $releaseDate, 
            $price, 
            $genre, 
            $format, 
            $vinylCondition, 
            $vinylDescription, 
            $filename, 
            $userID,
            $quantity
        );

        // execute the query
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;  // Success
        } else {
            error_log("Execute failed: " . mysqli_stmt_error($stmt)); // log error instead of stopping execution
        }

        mysqli_stmt_close($stmt);
    } else {
        error_log("Prepare failed: " . mysqli_error($mysqli)); // log error instead of stopping execution
    }

    return false; // return false if something goes wrong
}

/**
 * Get number of active (avaiable) vninyls to sell
 */
function getNumberOfVinylsToSell($userID) {

    $mysqli = dbConnect(); 

    // query
    $stmt = $mysqli->prepare("
        SELECT SUM(quantity) AS totalActive 
        FROM vinylInfo 
        WHERE createdBY = ?
    ");

    $stmt->bind_param("i", $userID); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalActive = $row['totalActive'] ?? 0; // set default to 0 if nothing added bu user

    $stmt->close();
    $mysqli->close();

    return $totalActive; 
}

/**
 * Get username of user that added vinyl
 */

function getSoldByUsername($vinylID) {
    $mysqli = dbConnect();
    
    $stmt = $mysqli->prepare("
        SELECT ui.username
        FROM vinylInfo vi
        JOIN userInfo ui ON vi.createdBY = ui.userID
        WHERE vi.vinylID = ?;
    ");

    $stmt->bind_param("i", $vinylID); 
    $stmt->execute();
    
    $result = $stmt->get_result()->fetch_assoc(); 
    return $result['username'] ?? 'unknown'; 
}

/**
 * Summary of getAllUsers
 * 
 */
function getAllUsers($limit = 6) {

    $mysqli = dbConnect();

    $stmt = $mysqli->prepare("
        SELECT * FROM userInfo 
        ORDER BY createdAT DESC 
        LIMIT ?
    ");

    $stmt->bind_param("i", $limit);

    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $mysqli->close();

    return $result;
}

/**
 * Summary of placeOrderQuery
 * 
 */
function placeOrderQuery($userID, $cartItems, $addressID, $totalAmount) {
    $mysqli = dbConnect();

    // Find existing order or create a new one
    $stmt = $mysqli->prepare("
        SELECT orderID FROM orderInfo
        WHERE userID = ? AND addressID = ? AND orderDate >= NOW() - INTERVAL 1 HOUR
        ORDER BY orderDate DESC
        LIMIT 1
    ");
    $stmt->bind_param("ii", $userID, $addressID);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingOrder = $result->fetch_assoc();
    $stmt->close();

    if ($existingOrder) {
        // Order exists, update totalAmount
        $orderID = $existingOrder['orderID'];
        $stmt = $mysqli->prepare("UPDATE orderInfo SET totalAmount = totalAmount + ? WHERE orderID = ?");
        $stmt->bind_param("di", $totalAmount, $orderID);
        $stmt->execute();
        $stmt->close();
    } else {
        // No order exists, create a new one
        $stmt = $mysqli->prepare("
            INSERT INTO orderInfo (userID, orderDate, addressID, totalAmount)
            VALUES (?, NOW(), ?, ?)
        ");
        $stmt->bind_param("iid", $userID, $addressID, $totalAmount);

        if (!$stmt->execute()) {
            error_log("Order Insertion Failed: " . $stmt->error);
            return false;
        }
        $orderID = $mysqli->insert_id;
        $stmt->close();
    }

    // Insert each vinyl into orderItems
    $stmt = $mysqli->prepare("
        INSERT INTO orderItems (orderID, vinylID, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($cartItems as $vinylID => $quantity) {
        // Fetch price from vinylInfo
        $priceStmt = $mysqli->prepare("SELECT price FROM vinylInfo WHERE vinylID = ?");
        $priceStmt->bind_param("i", $vinylID);
        $priceStmt->execute();
        $priceResult = $priceStmt->get_result();
        $priceRow = $priceResult->fetch_assoc();
        $priceStmt->close();

        if ($priceRow) {
            $price = (float)$priceRow['price'];
            $stmt->bind_param("iiid", $orderID, $vinylID, $quantity, $price);
            $stmt->execute();
        }
    }

    $stmt->close();

    //  Deduct stock from vinylInfo
    $stmt = $mysqli->prepare("UPDATE vinylInfo SET quantity = quantity - ? WHERE vinylID = ? AND quantity >= ?");
    foreach ($cartItems as $vinylID => $quantity) {
        $stmt->bind_param("iii", $quantity, $vinylID, $quantity);
        $stmt->execute();
    }
    $stmt->close();

    return true;
}

/**
 * Get details of user order
 */
function getAllUsersOrders($limit = 100) {
    global $dbConnect;

    $query = "SELECT o.orderID, o.totalAmount, o.orderDate, ui2.username AS buyer
              FROM orderInfo o
              JOIN userInfo ui2 ON ui2.userID = o.userID  -- buyer username
              GROUP BY o.orderID
              ORDER BY o.orderDate DESC
              LIMIT ?";

    if ($stmt = $dbConnect->prepare($query)) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); 
    } else {
        return []; 
    }
}
/**
 * Get details of individual vinyls that were 'bought' in that order
 */
function getOrderItemsByOrderID($orderID) {
    global $dbConnect;

    $query = "SELECT vi.vinylID, vi.title, vi.artist, oi.quantity, oi.price
              FROM orderItems oi
              JOIN vinylInfo vi ON oi.vinylID = vi.vinylID
              WHERE oi.orderID = ?";

    if ($stmt = $dbConnect->prepare($query)) {
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); 
    } else {
        return []; 
    }
}



?>




    
    
<?php

// define login credentials as constants so they cannot be changed later
if (!defined('USERNAME')) define('USERNAME', 's2265080');
if (!defined('PWRD')) define('PWRD', 'a5OztBO6');
if (!defined('HOSTNAME')) define('HOSTNAME', 'localhost');
if (!defined('DBNAME')) define('DBNAME', 's2265080_musicOnline'); 

// establish database connection
// the @ symbol suppresses errors to avoid exposing sensitive details
$dbConnect = @mysqli_connect(HOSTNAME, USERNAME, PWRD, DBNAME) OR die('Could not connect: ' . mysqli_connect_error());

// check if the connection failed (extra safety measure)
if (!$dbConnect) {
    die("Database connection failed: " . mysqli_connect_error());
}


<?php
require('config.php');
error_reporting(E_ALL);
ini_set('display_errors', 'on');

var_dump($_FILES);

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (!$link) {
    echo "Error: Unable to connect to MySQL.";
    echo "Debugging errno: " . mysqli_connect_errno();
    exit;
}

$result = mysqli_query($link, "SELECT * FROM users");

if ($result) {
	while ($row = mysqli_fetch_assoc($result)) {
        printf ("%s (%s)<br/>", $row["id"], $row["username"]);
    }
	// release the result set
	mysqli_free_result($result);
}

mysqli_close($link);

?>
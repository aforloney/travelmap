<?php
require('config.php');
session_start();

if (isset($_POST) && 
	isset($_POST["username"]) &&
	isset($_POST["password"])
	) {

	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	if (!$link) {
    	echo "Error: Unable to connect to MySQL.";
    	echo "Debugging errno: " . mysqli_connect_errno();
    	exit;
	}

	$stmt = mysqli_prepare($link, "SELECT id FROM user_login WHERE username=? AND password=?");
	mysqli_stmt_bind_param($stmt, 'ss', $user, $password);

	$user = $_POST["username"];
	$password = $_POST["password"];

	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $user_id);
	 /* fetch value */
	mysqli_stmt_fetch($stmt);

	if (isset($user_id) && is_numeric($user_id)) {
		$_SESSION["user_id"] = $user_id;
		header('Location: index.php');
	}
	else {
		// redirect to index.html
		echo "something bad happened using username: " . $_POST["username"];
		
	}

	mysqli_close($link);
}


?>

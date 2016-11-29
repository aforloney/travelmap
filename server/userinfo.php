
<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$link = mysqli_connect("localhost", "aforloney", "password", "places");

mysqli_report(MYSQLI_REPORT_ALL);

if (!$link) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM pending_images WHERE user_id = ?");

mysqli_stmt_bind_param($stmt, 'i', $id);

$id = $_GET['id'];

/* execute prepared statement */
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, $pending);

 /* fetch value */
mysqli_stmt_fetch($stmt);

if (isset($pending)) {
	$arr = array('status' => 'success',
				 'rows' => $pending);
	echo json_encode($arr);	
}

mysqli_close($link);

?>

<?php

require('config.php');
error_reporting(E_ALL);
ini_set('display_errors', 'on');

function get_pending() {

	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	if (!$link) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	$stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM image_info WHERE user_id = ?");

	mysqli_stmt_bind_param($stmt, 'i', $id);

	$id = $_GET['id'];

	/* execute prepared statement */	
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $pending);
	 /* fetch value */
	mysqli_stmt_fetch($stmt);

	if (isset($pending)) {
		$arr = array('status' => 'success',
					 'message' => $pending);
		echo json_encode($arr);	
	}

	mysqli_close($link);
}

function get_image() {

	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	if (!$link) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	/* 
	 *	build a query of the images for the given user,
	 */
	$sql = 'select info.FilePath
			from `image_info` info 
			WHERE info.user_id = ? AND info.state = ?;';


	//$sql = '';

	$stmt = mysqli_prepare($link, $sql);
	mysqli_stmt_bind_param($stmt, 'is', $id, $state);

	$id = $_GET['id'];
	$state = $_GET["state"];

	/* execute prepared statement */
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $img_path);

	$img = array();

	 /* fetch value */
	while (mysqli_stmt_fetch($stmt)) {
		$img[] = $img_path;
	}

	$arr = array('status' => 'success',
				 'image_path' => $img);
		
	// output the results back to client,
	echo json_encode($arr);	

	mysqli_close($link);
}

function get_user() {

	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	if (!$link) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	/* 
	 *	build a query of the images for the given user,
	 */
	$sql = 'select user.DisplayName, info.State 
			from `users` user 
			LEFT OUTER JOIN `image_info` info ON user.id = info.user_id
			WHERE user.id = ?;';

	$stmt = mysqli_prepare($link, $sql);
	mysqli_stmt_bind_param($stmt, 'i', $id);

	$id = $_GET['id'];

	/* execute prepared statement */
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $name, $state);

	 /* fetch value */
	while (mysqli_stmt_fetch($stmt)) {
		$result = $name;
		$states[] = $state;
	}

	if (isset($result)) {
		$arr = array('status' => 'success',
					 'message' => $name,
					 'states' => $states);
	} 
	else {
		$arr = array('status' => 'failure');
	}
		
	// output the results back to client,
	echo json_encode($arr);	

	mysqli_close($link);
}

// dispatcher method,
if (isset($_GET)) {
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		switch ($action) {
			case 'get_pending':
				get_pending();
				break;
			case 'get_user':
				get_user();
				break;
			case 'get_images':
				get_image();
				break;
			default:
				$arr = array('status' => 'error',
							 'message' => ( 'No defined function for: ' . $action ));
				echo json_encode($arr);	
		}
	}	
}

?>
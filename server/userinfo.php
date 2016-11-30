
<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

function get_pending() {

	$link = mysqli_connect("localhost", "aforloney", "password", "places");

	mysqli_report(MYSQLI_REPORT_ALL);

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

function get_user() {

	$link = mysqli_connect("localhost", "aforloney", "password", "places");

	mysqli_report(MYSQLI_REPORT_ALL);

	if (!$link) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	$stmt = mysqli_prepare($link, "SELECT DisplayName FROM users WHERE id = ?");

	mysqli_stmt_bind_param($stmt, 'i', $id);

	$id = $_GET['id'];

	/* execute prepared statement */
	mysqli_stmt_execute($stmt);

	mysqli_stmt_bind_result($stmt, $result);

	 /* fetch value */
	mysqli_stmt_fetch($stmt);

	if (isset($result)) {
		$arr = array('status' => 'success',
					 'message' => $result);
		echo json_encode($arr);	
	}

	mysqli_close($link);
}

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
			default:
				$arr = array('status' => 'error',
							 'message' => ( 'No defined function for: ' . $action ));
				echo json_encode($arr);	
		}
	}	
}

?>
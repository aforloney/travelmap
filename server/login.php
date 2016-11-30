

<?php

include('validate.php');
include('connect.php');
include('config.php');

session_start();

if (isset($config->user) && 
	isset($config->password) &&
	isset($config->db)) {

	$conn = new Connection($config->user, $config->password, $config->db);
	if !($conn) {
		die('Connection cannot be established');
	}

	if (isset($_POST)) {
		$user = $_POST['user'];
		if (isset($user) && Validate::validName($user)) {
			$images = $conn->getImages($user);

			if (isset(images)) {
				# display the map
			}
		}
	}
}

# 	VALIDATE.PHP
class Validate {
	static function validName($user) { return preg_match('^[a-zA-Z0-9]+$', $user); }
}

#	CONNECT.PHP
class Connection {
	public Connection($user, $pass, $db) {
		 this->$conn = mysqli_connect($db, $user, $pass);
	}

	function getConn() {
		return self->$conn;
	}

	function getImages($user) {
		# check if connection is still open before issuing query,
		$query = '';
		$rows = mysqli_execute($conn, $query);
		if (isset($rows)) {
			return $rows;
		}

		return NULL;
	}
}

class Config {
	public Config($user, $password) {
		this->$user = $user;
		this->$password = $password;
	}
	function getUser() { return self->$user; }
	function getPassword() { return self->$password; }
}
?>

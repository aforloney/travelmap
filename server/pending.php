
<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$link = mysqli_connect("localhost", "aforloney", "password", "places");

mysqli_report(MYSQLI_REPORT_ALL);

/*
	Move the uploaded image over to a "pending" folder,
*/

var_dump($_FILES);
var_dump($_POST);

$target_dir = "pending/";
$target_file = $target_dir . basename($_FILES["upload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image

$check = getimagesize($_FILES["upload"]["tmp_name"]);
if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
} else {
    echo "File is not an image.";
    $uploadOk = 0;
}

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["upload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["upload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

if (!$link) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$stmt = mysqli_prepare($link, "INSERT INTO pending_images VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, 'iisddsssssss', $dft, $id, $filepath, $long, $lat, $address, $city, $state, $postal, $country, $blurb, $dt);

/*
		lATITUDE AND LONGITUDE ARE NOT PROPERLY SAVING, NEITHER IS THE STRING FORMAT FOR THE DATE
*/

$dft = '';
$id = $_POST['user_id'];
$filepath = $_POST['filepath'];
$long = $_POST['lng'];
$lat = $_POST['lat'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$postal = $_POST['postal'];
$country = $_POST['country'];
$blurb = $_POST['blurb'];
$dt = $_POST['dt'];

/* execute prepared statement */
mysqli_stmt_execute($stmt);

if ( mysqli_stmt_affected_rows($stmt) > 0) {
	$arr = array('status' => 'success',
				 'rows' => mysqli_stmt_affected_rows($stmt));
	echo json_encode($arr);
}

mysqli_close($link);

?>
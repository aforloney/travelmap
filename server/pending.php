
<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$link = mysqli_connect("localhost", "aforloney", "password", "places");

mysqli_report(MYSQLI_REPORT_ALL);

/*
	Move the uploaded image over to a "pending" folder,
*/


$target_dir = "pending/";
$target_file = $target_dir . basename($_FILES[0]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$message = '';

// Check if image file is a actual image or fake image

$check = getimagesize($_FILES[0]["tmp_name"]);
if($check !== false) {
    $uploadOk = 1;
} else {
    $message = "File is not an image.";
    $uploadOk = 0;
}

// Check if file already exists
if (file_exists($target_file)) {
    $message = "Sorry, file already exists.";
    $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed. You uploaded, " . $imageFileType;
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $message = "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES[0]["tmp_name"], $target_file)) {
        $message = "The file ". basename( $_FILES[0]["name"]). " has been uploaded.";
    } else {
        $message = "Sorry, there was an error uploading your file.";
    }
}


if (!$link) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$stmt = mysqli_prepare($link, "INSERT INTO image_info VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, 'iisddsssssss', $dft, $id, $filepath, $long, $lat, $address, $city, $state, $postal, $country, $blurb, $dt);

$dft = '';
$id = $_POST['user_id'];
$filepath = $target_file;
$long = $_POST['lng'];
$lat = $_POST['lat'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$postal = $_POST['postal'];
$country = $_POST['country'];
$blurb = '';
$dt = date("Y-m-d H:i:s");

/* execute prepared statement */
mysqli_stmt_execute($stmt);

if ( mysqli_stmt_affected_rows($stmt) > 0) {
	$arr = array('status' => 'success',
				 'message' => $message);
	echo json_encode($arr);
}

mysqli_close($link);

?>
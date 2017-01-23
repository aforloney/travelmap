
<?php

require('config.php');
error_reporting(E_ALL);
ini_set('display_errors', 'on');

$target_dir = "pending/" . $_POST['user_id'] . '/';

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$date = new DateTime();
$result = $date->format('Y-m-d-H-i-s');
$timeString = str_replace('-','', $result);

$target_file = $target_dir . $timeString . '-' . basename($_FILES[0]["name"]);
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
if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES[0]["tmp_name"], $target_file)) {
        $message = "The file ". basename( $_FILES[0]["name"]). " has been uploaded.";
    } else {
        $uploadOk = 0;
        $message = "Sorry, there was an error uploading your file.";
    }
}

if ($uploadOk == 1) {

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

    mysqli_report(MYSQLI_REPORT_ALL);

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

    // grab the id sssociated to the executed statement,
    $img_id = mysqli_insert_id($link);

    /*  insert the associated blob data, maybe store the blob in the above table itself,
    */
    $stmt = mysqli_prepare($link, "INSERT INTO image_blob VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iiis', $dft, $image_id, $user_id, $blob);
    $dft = '';
    $image_id = $img_id;
    $user_id = $_POST['user_id'];
    // store away the contents of the file into the database,
    $blob = base64_encode(file_get_contents($target_file));

    mysqli_stmt_execute($stmt);

    mysqli_close($link);
}

$arr = array('status' => ( $uploadOk == 1 ? 'success' : 'failure' ),
             'message' => $message);

echo json_encode($arr);

?>
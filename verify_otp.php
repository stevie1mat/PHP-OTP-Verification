
<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
 
 
$response = array();
$response["error"] = false;
 
if (isset($_POST['otp']) && $_POST['otp'] != '') {
    $otp = $_POST['otp'];
 
 
    $user = $db->activateUser($otp);
 
    if ($user != NULL) {
 
        $response["message"] = "User created successfully!";
        $response["profile"] = $user;
    } else {
        $response["message"] = "Sorry! Failed to create your account.";
    }
     
     
} else {
    $response["message"] = "Sorry! OTP is missing.";
}
 
 
echo json_encode($response);
?>
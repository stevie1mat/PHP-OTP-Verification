
<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
 
 
$response = array();
 
if (isset($_POST['mobile']) && $_POST['mobile'] != '') {
 

    $mobile = $_POST['mobile'];
 
    $otp = rand(100000, 999999);
 
    $res = $db->createUser($mobile, $otp);
 
    if ($res == USER_CREATED_SUCCESSFULLY) {
         
        // send sms
        sendSms($mobile, $otp);
         
        $response["error"] = false;
        $response["message"] = "SMS request is initiated! You will be receiving it shortly.";
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Sorry! Error occurred in registration.";
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Mobile number already existed!";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! mobile number is not valid or missing.";
}
 
echo json_encode($response);
 
function sendSms($mobile, $otp) {
     
    $otp_prefix = ':';
 
    //Your message to send, Add URL encoding here.
    $message = urlencode("Hello! Welcome to TestingOtp. Your OPT is '$otp_prefix $otp'");
 
    $response_type = 'json';
 
    //Define route 
    $route = "4";
     
    //Prepare you post parameters
    $postData = array(
        'authkey' => MSG91_AUTH_KEY,
        'mobiles' => $mobile,
        'message' => $message,
        'sender' => MSG91_SENDER_ID,
        'route' => $route,
        'response' => $response_type
    );
 
//API URL
    $url = "https://control.msg91.com/sendhttp.php";
 
// init the resource
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
    ));
 
 
    //Ignore SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 
 
    //get response
    $output = curl_exec($ch);
 
    //Print error if any
    if (curl_errno($ch)) {
        echo 'error:' . curl_error($ch);
    }
 
    curl_close($ch);
}
?>
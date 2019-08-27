Config.php
<?php
/**
 * Database configuration
 */
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'android_sms');
 
 
/**
 * MSG91 configuration
 */
define('MSG91_AUTH_KEY', "103159AOIrnDjUO56abd329");
// sender id should 6 character long
define('MSG91_SENDER_ID', 'ANHIVE');
 
define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);
?>

<?php
 
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
 
    /* ------------- `users` table method ------------------ */
 
    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $mobile User mobile number
     * @param String $otp user verificaiton code
     */
    public function createUser($mobile, $otp) {
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isUserExists($mobile)) {
 
            // Generating API key
            $api_key = $this->generateApiKey();
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users(mobile, apikey, status) values(?, ?, 0)");
            $stmt->bind_param("ss",$mobile, $api_key);
 
            $result = $stmt->execute();
 
            $new_user_id = $stmt->insert_id;
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
 
                $otp_result = $this->createOtp($new_user_id, $otp);
 
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }
 
        return $response;
    }
 
    public function createOtp($user_id, $otp) {
 
        // delete the old otp if exists
        $stmt = $this->conn->prepare("DELETE FROM sms_codes where user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
 
 
        $stmt = $this->conn->prepare("INSERT INTO sms_codes(user_id, code, status) values(?, ?, 0)");
        $stmt->bind_param("is", $user_id, $otp);
 
        $result = $stmt->execute();
 
        $stmt->close();
 
        return $result;
    }
     
    /**
     * Checking for duplicate user by mobile number
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($mobile) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE mobile = ? and status = 1");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
 
    public function activateUser($otp) {
        $stmt = $this->conn->prepare("SELECT u.id, u.mobile, u.apikey, u.status, u.created_at FROM users u, sms_codes WHERE sms_codes.code = ? AND sms_codes.user_id = u.id");
        $stmt->bind_param("s", $otp);
 
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($id, $mobile, $apikey, $status, $created_at);
             
            $stmt->store_result();
 
            if ($stmt->num_rows > 0) {
                 
                $stmt->fetch();
                 
                // activate the user
                $this->activateUserStatus($id);
                 
                $user = array();
                $user["mobile"] = $mobile;
                $user["apikey"] = $apikey;
                $user["status"] = $status;
                $user["created_at"] = $created_at;
                 
                $stmt->close();
                 
                return $user;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
 
        return $result;
    }
     
    public function activateUserStatus($user_id){
        $stmt = $this->conn->prepare("UPDATE users set status = 1 where id = ?");
        $stmt->bind_param("i", $user_id);
         
        $stmt->execute();
         
        $stmt = $this->conn->prepare("UPDATE sms_codes set status = 1 where user_id = ?");
        $stmt->bind_param("i", $user_id);
         
        $stmt->execute();
    }
 
    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
}
?>

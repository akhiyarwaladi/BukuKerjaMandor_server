<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DbHandler {

    //put your code hereprivate $conn;
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `users` table method ------------------ */

    public function createUser($name, $email, $password) {
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO user(username, email, password, api_key) values(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);

            $result = $stmt->execute();
            $stmt->close();

            // Check for successful insertion
            if ($result) {
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

    // updating user GCM registration ID
    public function updateGcmID($user_id, $gcm_registration_id) {
        $response = array();
        $stmt = $this->conn->prepare("UPDATE users SET gcm_registration_id = ? WHERE id = ?");
        $stmt->bind_param("si", $gcm_registration_id, $user_id);

        if ($stmt->execute()) {
            // User successfully updated
            $response["error"] = false;
            $response["message"] = 'GCM registration ID updated successfully';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Failed to update GCM registration ID";
            $stmt->error;
        }
        $stmt->close();
        return $response;
    }

    public function updateAlatUser($rssi, $battery, $idalat, $status) {
        $response = array();
        $stmt = $this->conn->prepare("UPDATE alatuser SET rssi = ?, battery = ?, status = ? WHERE id = ?");
        $stmt->bind_param("iiii", $rssi, $battery,  $status, $idalat);

        if ($stmt->execute()) {
            // User successfully updated
            $response["error"] = false;
            $response["message"] = 'AlatUser updated successfully';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Failed to update AlatUser";
            $stmt->error;
        }
        $stmt->close();
        return $response;
    }
    
    public function updateSettingsAlat($hpsp, $optime, $idalat) {
        $response = array();
        $stmt = $this->conn->prepare("UPDATE alatuser SET setPoint = ?, opTime = ? WHERE id = ?");
        $stmt->bind_param("iii", $hpsp, $optime, $idalat);

        if ($stmt->execute()) {
            // User successfully updated
            $response["error"] = false;
            $response["message"] = 'SettingsAlat updated successfully';
        } else {
            // Failed to update user
            $response["error"] = true;
            $response["message"] = "Failed to update SettingsAlat";
            $stmt->error;
        }
        $stmt->close();
        return $response;
    }
    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password FROM user WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
	 
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
    public function createAlatUser($id_alat, $user_id, $nama) {
        $response = array();
        if (!$this->isAlatExists($id_alat)) {
            return ALAT_USER_ISNOT_EXISTED;
        }
        else{
            $stmt = $this->conn->prepare("INSERT INTO alatuser(id_alat, id_user, nama) values(?, ?, ?)");
            $stmt->bind_param("sss", $id_alat, $user_id, $nama);
            $result = $stmt->execute();
            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return ALAT_USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return ALAT_USER_CREATE_FAILED;
            }
        }
        return $response;
    }
	
    private function isAlatExists($id_alat) {
        $stmt = $this->conn->prepare("SELECT id from dataalat WHERE kode = ?");
        $stmt->bind_param("s", $id_alat);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
    public function deleteAlatUser($id_alat, $user_id) {
        $response = array();

        // delete query
        $stmt = $this->conn->prepare("DELETE from alatuser WHERE id = ?");
        $stmt->bind_param("s", $id_alat);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT username, email, api_key, created_at FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($username, $email, $api_key, $created_at);
            $stmt->fetch();
            $user = array();
            $user["username"] = $username;
            $user["email"] = $email;
            $user["api_key"] = $api_key;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            // $api_key = $stmt->get_result()->fetch_assoc();
            // TODO
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM user WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from user WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    /* ------------- `alat` table method ------------------ */

    public function createDataSensor($user_id, $alat_id, $hpsp, $hpc, $humid, $temp, $uk, $optime) {
        $api_key = $this->generateApiKey();
        $stmt = $this->conn->prepare("INSERT INTO datasensor(id_alat, hpsp, hpc, humidity, temperature, uk, optime) VALUES(?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss", $alat_id, $hpsp, $hpc, $humid, $temp, $uk, $optime);
        $result = $stmt->execute();
        if ($result) {
            // task row created
            // now assign the task to user
            $new_task_id = $this->conn->insert_id;
            $stmt->fetch();
            $data = array();
            $data["hpsp"] = $hpsp;
            $data["hpc"] = $hpc;
            $data["humid"] = $humid;
            $data["temp"] = $temp;
            $data["uk"] = $uk;
            $data["optime"] = $optime;
            $stmt->close();
            return $data;

        } else {
            // task failed to create
            return NULL;
        }
    }

    public function getAllDataByIdAlat($id_alat) {
        $stmt = $this->conn->prepare("SELECT t.* FROM datasensor t WHERE t.id_alat = ? ORDER BY t.created_at DESC LIMIT 30");
        $stmt->bind_param("s", $id_alat);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
    public function getLastDataByIdAlat($id_alat) {
        $stmt = $this->conn->prepare("SELECT t.* FROM datasensor t WHERE t.id_alat = ? ORDER BY t.create_at DESC LIMIT 1");
        $stmt->bind_param("s", $id_alat);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
    public function getAlatUser($user_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM alatuser t WHERE t.id_user = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getAllMaterial($user_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM material t");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function createPegawai($id_pegawai, $nama_pegawai, $panggilan_pegawai, $jabatan, $status, $username) {
        $response = array();

        $stmt = $this->conn->prepare("INSERT INTO pegawai(id_pegawai, nama_pegawai, panggilan_pegawai, jabatan, status, username) values(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id_pegawai, $nama_pegawai, $panggilan_pegawai, $jabatan, $status, $username);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return ALAT_USER_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return ALAT_USER_CREATE_FAILED;
        }
   
        return $response;
    }

    public function getAlatUserById($id_alat) {
        $stmt = $this->conn->prepare("SELECT t.* FROM alatuser t WHERE t.id = ?");
        $stmt->bind_param("s", $id_alat);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
    public function createDataAlat($user_id) {
        $response = array();

        // Generating API key
        $api_key = $this->generateApiKey();
	    $date = date('Y-m-d G:i:s');
        // insert query
        $stmt = $this->conn->prepare("INSERT INTO dataalat(kode, tanggal_produksi) values(?, ?)");
        $stmt->bind_param("ss", $api_key, $date);

        $result = $stmt->execute();

        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return ID_ALAT_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return ID_ALAT_USER_CREATE_FAILED;
        }

        return $response;
    }

}
?>
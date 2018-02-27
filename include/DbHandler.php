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

    public function createUser($id_pegawai, $username, $password) {
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($username)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO user(id_pegawai, username, password, api_key) values(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $id_pegawai, $username, $password_hash, $api_key);

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

    /**
     * Checking user login
     * @param String $username User login username id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($username, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password FROM user WHERE username = ?");

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the user
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
	 
    private function isUserExists($id_pegawai) {
        $stmt = $this->conn->prepare("SELECT id_pegawai from user WHERE id_pegawai = ?");
        $stmt->bind_param("s", $id_pegawai);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($username) {
        $stmt = $this->conn->prepare("SELECT username, api_key, created_at FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($username, $api_key, $created_at);
            $stmt->fetch();
            $user = array();
            $user["username"] = $username;
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
        $stmt = $this->conn->prepare("SELECT id_pegawai FROM user WHERE api_key = ?");
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
        $stmt = $this->conn->prepare("SELECT id_pegawai from user WHERE api_key = ?");
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

    /* ------------- `pegawai` table method ------------------ */
	
	public function getAllPegawai($user_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM pegawai t");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
	public function createPegawai($id_pegawai, $nama_pegawai, $panggilan_pegawai, $jabatan, $status, $kode_mandoran) {
        $response = array();

        $stmt = $this->conn->prepare("INSERT INTO pegawai(id_pegawai, nama_pegawai, panggilan_pegawai, jabatan, status, kode_mandoran) values(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $id_pegawai, $nama_pegawai, $panggilan_pegawai, $jabatan, $status, $kode_mandoran);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return PEGAWAI_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return PEGAWAI_CREATE_FAILED;
        }
   
        return $response;
    }

    /* ------------- `aktivitas` table method ------------------ */
	
	public function getAllAktivitas($user_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM aktivitas t");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

	public function createAktivitas($kode_aktivitas, $nama_aktivitas, $kode_material) {
        $response = array();

        $stmt = $this->conn->prepare("INSERT INTO aktivitas(kode_aktivitas, nama_aktivitas, kode_material) values(?, ?, ?)");
        $stmt->bind_param("sss", $kode_aktivitas, $nama_aktivitas, $kode_material);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return AKTIVITAS_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return AKTIVITAS_CREATE_FAILED;
        }
   
        return $response;
    }
	
    /* ------------- `material` table method ------------------ */
	
    public function getAllMaterial($user_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM material t");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
	public function createMaterial($kode_material, $nama_material, $unit) {
        $response = array();

        $stmt = $this->conn->prepare("INSERT INTO material(kode_material, nama_material, unit) values(?, ?, ?)");
        $stmt->bind_param("sss", $kode_material, $nama_material, $unit);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return MATERIAL_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return MATERIAL_CREATE_FAILED;
        }
   
        return $response;
    }
	
	/* ------------- `sektor tanam` table method ------------------ */
	public function getRKHAktivitas($user_id) {
        $stmt = $this->conn->prepare("SELECT t.* FROM rkh_aktivitas t");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
}
?>
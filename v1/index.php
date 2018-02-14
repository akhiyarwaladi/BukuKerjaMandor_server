<?php

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require_once '../include/Firebase.php';
require_once '../include/Push.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary keR23y id
            $user_id = $db->getUserId($api_key);
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}

$app->post('/register', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('name', 'email', 'password'));
    $response = array();

    // reading post params
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $password = $app->request->post('password');

    // validating email address
    validateEmail($email);

    $db = new DbHandler();
    $res = $db->createUser($name, $email, $password);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
    }
    // echo json response
    echoRespnse(201, $response);
});

$app->post('/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('email', 'password'));

    // reading post params
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = array();

    $db = new DbHandler();
    // check for correct email and password
    if ($db->checkLogin($email, $password)) {
        // get the user by email
        $user = $db->getUserByEmail($email);

        if ($user != NULL) {
            $response["error"] = false;
            $response['username'] = $user['username'];
            $response['email'] = $user['email'];
            $response['apiKey'] = $user['api_key'];
            $response['createdAt'] = $user['created_at'];
        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials';
    }

    echoRespnse(200, $response);
});

$app->put('/users', 'authenticate', function() use ($app) {
    global $user_id;
 
    verifyRequiredParams(array('gcm_registration_id'));
 
    $gcm_registration_id = $app->request->put('gcm_registration_id');
    $db = new DbHandler();
    $response = $db->updateGcmID($user_id, $gcm_registration_id);
 
    echoRespnse(200, $response);
});

$app->put('/alatuser', 'authenticate', function() use ($app) {
    verifyRequiredParams(array('rssi', 'battery', 'idalat', 'status'));
 
    $rssi = $app->request->put('rssi');
    $battery = $app->request->put('battery');
    $idalat = $app->request->put('idalat');
    $status = $app->request->put('status');
    $db = new DbHandler();
    $response = $db->updateAlatUser($rssi, $battery, $idalat, $status);
 
    echoRespnse(200, $response);
});


$app->put('/settingsalat', 'authenticate', function() use ($app) {
    verifyRequiredParams(array('hpsp', 'optime', 'idalat'));
 
    $hpsp = $app->request->put('hpsp');
    $optime = $app->request->put('optime');
    $idalat = $app->request->put('idalat');
    $db = new DbHandler();
    $response = $db->updateSettingsAlat($hpsp, $optime, $idalat);
 
    echoRespnse(200, $response);
});

$app->post('/data_sensor', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('hpsp', 'hpc', 'humid', 'temp', 'uk', 'optime','idalat'));

    $response = array();
    $hpsp = $app->request->post('hpsp');
    $hpc = $app->request->post('hpc');
    $humid = $app->request->post('humid');
    $temp = $app->request->post('temp');
    $uk = $app->request->post('uk');
    $optime = $app->request->post('optime');
    $idalat = $app->request->post('idalat');

    global $user_id;
    $db = new DbHandler();
    $task_id = $db->createDataSensor($user_id, $idalat, $hpsp, $hpc, $humid, $temp, $uk, $optime);
    if ($task_id != NULL) {
        
        $response["error"] = false;
        $response["message"] = "Succesfully add data sensor";
        $response['hpsp'] = $task_id['hpsp'];
        $response['hpc'] = $task_id['hpc'];
        $response['humid'] = $task_id['humid'];
        $response['temp'] = $task_id['temp'];
        $response['uk'] = $task_id['uk'];
        $response['optime'] = $task_id['optime'];
       
        echoRespnse(201, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Failed to create task. Please try again";
        echoRespnse(200, $response);
    }
});

$app->get('/databyidalat/:id', 'authenticate', function($id_alat) use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getAllDataByIdAlat($id_alat);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["hpsp"] = $task["hpsp"];
        $tmp["hpc"] = $task["hpc"];
        $tmp["humid"] = $task["humidity"];
        $tmp["temp"] = $task["temperature"];
        $tmp["uk"] = $task["uk"];
        $tmp["optime"] = $task["optime"];
        $tmp["createdAt"] = strtotime($task["created_at"]);
        array_push($response["tasks"], $tmp);
    }
    echoRespnse(200, $response);
});

$app->get('/lastdatabyidalat/:id', 'authenticate', function($id_alat) use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getLastDataByIdAlat($id_alat);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["suhu"] = $task["suhu"];
        $tmp["ph"] = $task["ph"];
        $tmp["do"] = $task["do"];
        $tmp["hasil"] = $task["hasil"];
        $tmp["status"] = $task["status"];
        $tmp["createdAt"] = $task["create_at"];
        array_push($response["tasks"], $tmp);
    }

    echoRespnse(200, $response);
});
$app->get('/getalatuser/:id', 'authenticate', function($id_alat) use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getAlatUserById($id_alat);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["kode_alat"] = $task["kode_alat"];
        $tmp["id_user"] = $task["id_user"];
	    $tmp["nama"] = $task["nama"];
        $tmp["latitude"] = $task["latitude"];
        $tmp["longitude"] = $task["longitude"];
        $tmp["rssi"] = $task["rssi"];
        $tmp["battery"] = $task["battery"];
        $tmp["status"] = $task["status"];
        $tmp["setPoint"] = $task["setPoint"];
        $tmp["opTime"] = $task["opTime"];

        array_push($response["tasks"], $tmp);
    }
    echoRespnse(200, $response);
});

$app->get('/getalatuser', 'authenticate', function() use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getAlatUser($user_id);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["kode_alat"] = $task["kode_alat"];
        $tmp["id_user"] = $task["id_user"];
	    $tmp["nama"] = $task["nama"];
        $tmp["latitude"] = $task["latitude"];
        $tmp["longitude"] = $task["longitude"];
        $tmp["rssi"] = $task["rssi"];
        $tmp["battery"] = $task["battery"];
        $tmp["status"] = $task["status"];

        array_push($response["tasks"], $tmp);
    }
    echoRespnse(200, $response);
});

$app->get('/getAktivitas', 'authenticate', function() use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getAllAktivitas($user_id);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["kode_aktivitas"] = $task["kode_aktivitas"];
        $tmp["nama_aktivitas"] = $task["nama_aktivitas"];
        $tmp["kode_material"] = $task["kode_material"];

        array_push($response["tasks"], $tmp);
    }
    echoRespnse(200, $response);
});

$app->get('/getMaterial', 'authenticate', function() use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getAllMaterial($user_id);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["kode_material"] = $task["kode_material"];
        $tmp["nama_material"] = $task["nama_material"];
        $tmp["unit"] = $task["unit"];

        array_push($response["tasks"], $tmp);
    }
    echoRespnse(200, $response);
});

$app->get('/getPegawai', 'authenticate', function() use ($app){
    global $user_id;
    $response = array();
    $db = new DbHandler();
    
    // fetching all user tasks
    $result = $db->getAllPegawai($user_id);
    $response["error"] = false;
    $response["tasks"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id_pegawai"] = $task["id_pegawai"];
        $tmp["nama_pegawai"] = $task["nama_pegawai"];
        $tmp["panggilan_pegawai"] = $task["panggilan_pegawai"];
		$tmp["jabatan"] = $task["jabatan"];
		$tmp["status"] = $task["status"];
		$tmp["username"] = $task["username"];
        array_push($response["tasks"], $tmp);
    }
    echoRespnse(200, $response);
});

$app->post('/createAktivitas', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('kode_aktivitas', 'nama_aktivitas', 'kode_material'));
    $response = array();
    global $user_id;
    // reading post params
    $kode_aktivitas = $app->request->post('kode_aktivitas');
    $nama_aktivitas = $app->request->post('nama_aktivitas');
    $kode_material = $app->request->post('kode_material');

    $db = new DbHandler();
    $res = $db->createAktivitas($kode_aktivitas, $nama_aktivitas, $kode_material);

    if ($res == ALAT_USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Tools are successfully registered";
    } else if ($res == ALAT_USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
    } else if ($res == ALAT_USER_ISNOT_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this tools already existed";
    }
    // echo json response
    echoRespnse(201, $response);
});

$app->post('/createMaterial', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('kode_material', 'nama_material', 'unit'));
    $response = array();
    global $user_id;
    // reading post params
    $kode_material = $app->request->post('kode_material');
    $nama_material = $app->request->post('nama_material');
    $unit = $app->request->post('unit');

    $db = new DbHandler();
    $res = $db->createMaterial($kode_material, $nama_material, $unit);

    if ($res == ALAT_USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Tools are successfully registered";
    } else if ($res == ALAT_USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
    } else if ($res == ALAT_USER_ISNOT_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this tools already existed";
    }
    // echo json response
    echoRespnse(201, $response);
});

$app->post('/createPegawai', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('id_pegawai', 'nama_pegawai', 'panggilan_pegawai', 'jabatan', 'status', 'username'));
    $response = array();
    global $user_id;
    // reading post params
    $id_pegawai = $app->request->post('id_pegawai');
    $nama_pegawai = $app->request->post('nama_pegawai');
    $panggilan_pegawai = $app->request->post('nama_pegawai');
    $jabatan = $app->request->post('jabatan');
    $status = $app->request->post('status');
    $username = $app->request->post('username');

    $db = new DbHandler();
    $res = $db->createPegawai($id_pegawai, $nama_pegawai, $panggilan_pegawai, $jabatan, $status, $username);

    if ($res == ALAT_USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Tools are successfully registered";
    } else if ($res == ALAT_USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
    } else if ($res == ALAT_USER_ISNOT_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this tools already existed";
    }
    // echo json response
    echoRespnse(201, $response);
});

$app->post('/registeralat', 'authenticate', function() use ($app) {
    global $user_id;
    $response = array();
    
    $db = new DbHandler();
    $res = $db->createDataAlat($user_id);

    if ($res == ID_ALAT_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Tools successfully registered";
    } else if ($res == ID_ALAT_USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registering tools";
    } 
    // echo json response
    echoRespnse(201, $response);
});
$app->post('/registeralatuser', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('id_alat', 'nama'));
    $response = array();
    global $user_id;
    // reading post params
    $id_alat = $app->request->post('id_alat');
    $nama = $app->request->post('nama');

    $db = new DbHandler();
    $res = $db->createAlatUser($id_alat, $user_id, $nama);

    if ($res == ALAT_USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Tools are successfully registered";
    } else if ($res == ALAT_USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
	} else if ($res == ALAT_USER_ISNOT_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this tools already existed";
    }
    // echo json response
    echoRespnse(201, $response);
});

$app->delete('/deletealatuser/:id', 'authenticate', function($id_alat) use ($app) {
    // check for required params
    global $user_id;  
    $db = new DbHandler();
    $response = array();
    $result = $db->deleteAlatUser($id_alat, $user_id);

    if ($result) {
        // task deleted successfully
        $response["error"] = false;
        $response["message"] = "Alat deleted succesfully";
    } else {
        // task failed to delete
        $response["error"] = true;
        $response["message"] = "Alat failed to delete. Please try again!";
    }
    echoRespnse(200, $response);
});

$app->post('/sendtotopic', function() use ($app){
    
    verifyRequiredParams(array('title', 'message'));
    $response = array();
    global $user_id;
    // reading post params
    $title = $app->request->post('title');
    $message = $app->request->post('message');

    $db = new DbHandler();
    $res = $db->sendToTopic($title, $message);

    if ($res == MESSAGE_SENT_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "message are successfully sent";
    } else if ($res == MESSAGE_SENT_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while sending message";
    } 
    // echo json response
    echoRespnse(201, $response);
});

$app->post('/sendsingle', function() use ($app){
    
    verifyRequiredParams(array('to', 'title', 'message'));
    $response = array();
    global $user_id;
    // reading post params
    $to = $app->request->post('to');
    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $firebase = new Firebase();
    $push = new Push();
    
    $push->setTitle($title);
    $push->setMessage($message);
    $push->setImage('');
    $push->setIsBackground(FALSE);
    $payload = array();
    $payload['team'] = 'India';
    $payload['score'] = '5.6';
    $push->setPayload($payload);
    $json = '';
    $json = $push->getPush();
    
    $db = new DbHandler();
    //$res = $db->send($to, $json);
    $res = $firebase->send($to, $json);

    if ($res == MESSAGE_SENT_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Notification message are successfully sent";
    } else if ($res == MESSAGE_SENT_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while sending message";
    } 
    // echo json response
    echoRespnse(201, $response);
});
/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();

?>
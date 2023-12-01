<?php 
namespace DBManager;
require __DIR__ .'/DBConnector.php';

use DBConnector;
use Firebase\JWT\JWT;

class DBHandler extends DBConnector{

    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sp_select_ipadd()
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_ipadd()"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function sp_select_test()
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_test()"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

     // ip add 저장하기
     public function sp_insert_ipadd($ip_add)
     {
         $error = "E0000";
 
         if(!($stmt = $this->db->prepare("CALL sp_insert_ipadd(?)"))){
             $error = "E1000";
         }
         if(!$stmt->bind_param("s", $ip_add)){
             $error = "E1001";
         }
         if(!$stmt->execute()){
             $error = "E1002";
         }
 
         $res = $stmt->get_result();
         $data = array();
 
         while($row = $res->fetch_assoc()){
             $data[] = $row;
         }
 
         $json_data = array
         (
             "error" => $error,
             "data" => $data
         );
 
         return $json_data;
     }

     // 유저 로그 저장하기
    public function sp_insert_UserLog($ipidx,$photo_url,$user_cnt)
    {
        $error = "E0000";

        if(!($stmt = $this->db->prepare("CALL sp_insert_UserLog(?,?,?)"))){
            $error = "E1000";
        }
        if(!$stmt->bind_param("ssi", $ipidx,$photo_url,$user_cnt)){
            $error = "E1001";
        }
        if(!$stmt->execute()){
            $error = "E1002";
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array
        (
            "error" => $error,
            "data" => $data
        );

        return $json_data;
    }

    public function sp_select_UserLog($uidx)
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_UserLog(?)"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->bind_param("s", $uidx)) {
            $error = "E1001"; // Bind failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function sp_select_TotalLog_All($ipidx)
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_TotalLog_All(?)"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->bind_param("s", $ipidx)) {
            $error = "E1001"; // Bind failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function sp_select_Total_day($ipidx)
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_Total_day(?)"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->bind_param("s", $ipidx)) {
            $error = "E1001"; // Bind failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function sp_select_total_week($ipidx)
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_total_week(?)"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->bind_param("s", $ipidx)) {
            $error = "E1001"; // Bind failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function sp_select_Total_month($ipidx)
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_Total_month(?)"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->bind_param("s", $ipidx)) {
            $error = "E1001"; // Bind failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function sp_select_Total_year($ipidx)
    {
        $error = "E0000";

        if (!($stmt = $this->db->prepare("CALL sp_select_Total_year(?)"))) {
            $error = "E1000"; // Prepare failed
        }
        if (!$stmt->bind_param("s", $ipidx)) {
            $error = "E1001"; // Bind failed
        }
        if (!$stmt->execute()) {
            $error = "E1002"; // Execute failed
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array(
            "error" => $error,
            "data" => $data
        );

        return json_encode($json_data);
    }
    // 키오스크 ip 저장 DB 핸들러
    public function registerKiosk($ipAddress)
{
    try {
        // Check if the IP address already exists in the table
        $stmt = $this->db->prepare('SELECT ip_idx FROM IpAdd WHERE ip_address = ?');
        $stmt->bind_param("s", $ipAddress);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // IP address already exists, return the ip_idx
            $row = $result->fetch_assoc();
            $ipIdx = $row['ip_idx'];
            echo "IP address already exists. ip_idx: $ipIdx\n";
            return $ipIdx;
        }

        // Insert the IP address into the IpAdd table
        $stmt = $this->db->prepare('INSERT INTO IpAdd (ip_address) VALUES (?)');
        $stmt->bind_param("s", $ipAddress);
        $stmt->execute();

        // Return the last inserted id (ip_idx)
        $ipIdx = $stmt->insert_id;
        echo "IP address inserted. ip_idx: $ipIdx\n";
        return $ipIdx;
    } catch (\PDOException $e) {
        // Handle the exception as needed, e.g., log the error.
        echo 'Database error: ' . $e->getMessage();
    }
}

    public function getIpIdxByIp($kioskIp)
    {
    try {
        $stmt = $this->db->prepare('SELECT ip_idx FROM IpAdd WHERE ip_address = ?');
        $stmt->bind_param("s", $kioskIp);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['ip_idx'];
    } catch (\PDOException $e) {
        // Handle the exception as needed, e.g., log the error.
        echo 'Database error: ' . $e->getMessage();
        return null;
    }
    }

    public function saveMetadata($filename, $s3Key, $ipIdx)
    {
    try {
        $stmt = $this->db->prepare('INSERT INTO UploadData (filename, s3_key, ip_idx) VALUES (?, ?, ?)');
        $stmt->bind_param("ssi", $filename, $s3Key, $ipIdx);
        $stmt->execute();
    } catch (\PDOException $e) {
        // Handle the exception as needed, e.g., log the error.
        echo 'Database error: ' . $e->getMessage();
    }
    }

    public function updateTotalLog($ipIdx, $dayIncrement, $weekIncrement, $monthIncrement, $yearIncrement)
{
    try {
        $stmt = $this->db->prepare('INSERT INTO TotalLog (ipidx, day_total, week_total, month_total, year_total) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE day_total = day_total + ?, week_total = week_total + ?, month_total = month_total + ?, year_total = year_total + ?');
        $stmt->bind_param("iiiiiiiii", $ipIdx, $dayIncrement, $weekIncrement, $monthIncrement, $yearIncrement, $dayIncrement, $weekIncrement, $monthIncrement, $yearIncrement);
        $stmt->execute();
    } catch (\PDOException $e) {
        // Handle the exception as needed, e.g., log the error.
        echo 'Database error: ' . $e->getMessage();
    }
}

    public function getUploadData($udIdx) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM UploadData WHERE ud_idx = ?');
            $stmt->execute([$udIdx]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            // Handle the exception as needed, e.g., log the error.
            echo 'Database error: ' . $e->getMessage();
            return false;
        }
    }

        // 예제: 저장 프로시저를 호출하여 메타데이터 저장

        /*$error = "E0000";
 
        if(!($stmt = $this->db->prepare("CALL sp_insert_S3file_uplode(?, NOW())"))){
            $error = "E1000";
        }
        if(!$stmt->bind_param("s", $fileName)){
            $error = "E1001";
        }
        if(!$stmt->execute()){
            $error = "E1002";
        }

        $res = $stmt->get_result();
        $data = array();

        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }

        $json_data = array
        (
            "error" => $error,
            "data" => $data
        );

        return $json_data;*/

    #}

    public function getImageData($imageKey)
    {
        try {
            // 여기서 데이터베이스에서 이미지 데이터를 가져오는 로직을 구현
            // 실제로 사용하는 데이터베이스 및 테이블에 맞게 수정
            $stmt = $this->db->prepare('SELECT * FROM UploadData WHERE s3_key = ?');
            $stmt->execute([$imageKey]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            // Handle the exception as needed, e.g., log the error.
            echo 'Database error: ' . $e->getMessage();
            return null;
        }
    }

    public function updateFileStatus($filename)
    {
        try {
            $stmt = $this->db->prepare('UPDATE UploadData SET status = 1 WHERE filename = ?');
            $stmt->bind_param("s", $filename);
            $stmt->execute();
        } catch (\PDOException $e) {
            // Handle the exception as needed, e.g., log the error.
            echo 'Database error: ' . $e->getMessage();
        }
    }

    public function getPendingFile()
    {
        try {
            // 수정: status가 0인 파일 중 ud_idx가 가장 낮은 파일 가져오기
            $stmt = $this->db->prepare('SELECT * FROM UploadData WHERE status = 0 ORDER BY ud_idx LIMIT 1');
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            // Handle the exception as needed, e.g., log the error.
            echo 'Database error: ' . $e->getMessage();
            return null;
        }
    }
}
    ?>
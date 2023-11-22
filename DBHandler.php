<?php 
namespace DBManager;
require __DIR__ .'/DBConnector.php';

use DBConnector;
use Firebase\JWT\JWT;

class DBHandler extends DBConnector{

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

    public function saveMetadata($filename, $s3Key)
    {   
        try {
            $stmt = $this->db->prepare('INSERT INTO UploadData (filename, s3_key) VALUES (?, ?)');
            $stmt->bind_param("ss", $filename, $s3Key); // 바인딩 수정
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
}
    ?>
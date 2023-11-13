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

}
    ?>
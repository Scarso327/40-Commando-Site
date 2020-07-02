<?php

class Logs {

    /*
    ** Creates a new "log" or "Form" as they were known before v2
    */
    public function log ($fields, $actioner, $action, $status, $level) {
        $member = $fields["steamid"]["value"];

        if ($member == "" || $actioner == "" || $action == "" || $status == "") {
            return false;
        }

        $db = Database::getFactory()->getConnection(DB_NAME);

        $query = $db->prepare(
            "INSERT INTO logs (member, actioner, `action`, `status`, `level`) VALUES (:member, :actioner, :action, :status, :level)"
        );
        
        $query->execute(array(
            ':member' => $member, 
            ':actioner' => $actioner,
            ':action' => $action,
            ':status' => $status,
            ':level' => $level
        ));

        if ($query->rowCount() == 1) {
            $logID = Database::lastInsertId();

            foreach ($fields as $field) {
                if (!array_key_exists("is_hidden", $field)) { $field["is_hidden"] = false; }
                
                if (!$field["is_hidden"]) {
                    $query = $db->prepare(
                        "INSERT INTO responses (logid, `name`, `value`) VALUES (:id, :name, :value)"
                    );

                    if ($field["value"] == "" && $field["if_blank"] != "") {
                        $field["value"] = $field["if_blank"];
                    }
                    
                    $query->execute(array(
                        ':id' => $logID,
                        ':name' => $field["name"], 
                        ':value' => $field["value"]
                    ));

                    if ($query->rowCount() != 1) {
                        $query = $db->prepare("DELETE FROM logs WHERE id = :id");
                        $query->execute(array(':id' => $logID));

                        return false;
                    }
                }
            }

            return $logID;
        }

        return false;
    }
    
    /*
    ** Gets logs for given target...
    */
    public function getHistory ($steamid, $target = "member", $dates = array()) {
        if (!(count($dates) > 0)) {
            $dates = array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d'));
        }

        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM logs WHERE ".$target." = :member AND (DATE(timestamp) > :start AND DATE(timestamp) <= :end) AND hidden = 0");
        $query->execute(array(
            ":member" => $steamid, 
            ":start" => $dates[0], 
            ":end" => $dates[1]
        ));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }

    public function getLog($logid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM logs WHERE id = :id LIMIT 1");
        $query->execute(array(":id" => $logid));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetch();
    }

    /*
    ** Read it...
    */
    public function getResponse($logid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM responses WHERE logid = :id");
        $query->execute(array(":id" => $logid));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }
}
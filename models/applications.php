<?php

class Applications {

    public static function submitApplication($steamid, $logid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("INSERT INTO applications (steamid, log_id) VALUES (:steamid, :log_id)");
        $query->execute(array(':steamid' => $steamid, ':log_id' => $logid));
        if ($query->rowCount() == 1) { return Database::lastInsertId(); }

        return false;
    }

    public static function getApplication($app_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM applications WHERE id = :app_id");
        $query->execute(array(":app_id" => $app_id));

        if ($query->rowCount() == 0) { return false; } 

        return $query->fetch();
    }

    public static function getApplicationResponses($app_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM application_responses WHERE app_id = :app_id");
        $query->execute(array(":app_id" => $app_id));

        $return = array();

        foreach ($query->fetchAll() as $response) {
            $log = Logs::getLog($response->log_id);

            if ($log) {
                array_push($return, $log);
            }
        }

        return $return;
    }

    public static function updateApplicationActivity($app_id, $db = null) {
        if ($db == null) {
            $db = Database::getFactory()->getConnection(DB_NAME);
        }

        $query = $db->prepare("UPDATE applications SET last_response = CURRENT_TIMESTAMP() WHERE id = :app_id LIMIT 1");
        $query->execute(array(':app_id' => $app_id));
    }

    public static function addApplicationResponse($app_id, $log_id) {
        $db = Database::getFactory()->getConnection(DB_NAME);

        self::updateApplicationActivity($app_id, $db);

        $query = $db->prepare("INSERT INTO application_responses (app_id, log_id) VALUES (:app_id, :log_id)");
        $query->execute(array(
            ':app_id' => $app_id,
            ':log_id' => $log_id
        ));

        if ($query->rowCount() != 1) {
            $query = $db->prepare("DELETE FROM logs WHERE id = :id");
            $query->execute(array(':id' => $log_id));

            return false;
        }

        return true;
    }

    public static function acceptApplication($app_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE applications SET accepted = 1 WHERE id = :app_id LIMIT 1");
        $query->execute(array(':app_id' => $app_id));

        if($query->rowCount() != 1) { return false; }

        return true;
    }

    public static function rejectApplication($app_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE applications SET declined = 1 WHERE id = :app_id LIMIT 1");
        $query->execute(array(':app_id' => $app_id));

        if($query->rowCount() != 1) { return false; }

        return true;
    }

    public static function getAllApplications() {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM applications");
        $query->execute();

        if ($query->rowCount() == 0) { return false; } 

        return $query->fetchAll();
    }

    public static function wasLastAppDeclined($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT applications.id, applications.log_id FROM applications INNER JOIN accounts WHERE applications.steamid = :steamid AND accounts.steamid = :steamid AND declined = 1 AND applications.insert_time > accounts.left_date ORDER BY applications.insert_time DESC");
        $query->execute(array(":steamid" => $steamid));

        if ($query->rowCount() == 0) { return false; } 
        return $query->fetch()->id;
    }

    public static function isAppActive($app_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT accounts.candidate_id FROM accounts INNER JOIN records WHERE records.app_id = :id AND accounts.steamid = records.steamid LIMIT 1");
        $query->execute(array(":id" => $app_id));

        return ($query->rowCount() > 0);
    }

    public static function getApplicationStatus($app) {
        if (self::isAppActive($app->id)) {
            switch (true) {
                case ($app->accepted == 1):
                    return "Accepted";
                case ($app->declined == 1):
                    return "Declined";
                default:
                    return "Unanswered";
            }
        }

        return "Archived";
    }
}
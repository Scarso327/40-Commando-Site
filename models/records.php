<?php

class Records {
    public static function createRecord($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("INSERT INTO records (steamid) VALUES (:steamid)");
        $query->execute(array(':steamid' => $steamid));

        if ($query->rowCount() == 1) { return Database::lastInsertId(); }
        return false;
    }

    public static function getRecord($record_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM records WHERE id = :record_id");
        $query->execute(array(':record_id' => $record_id));

        if ($query->rowCount() == 1) { return $query->fetch(); }
        return false;
    }

    public static function setFieldID($steamid, $field, $id = -1) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE records SET ".$field." = :id WHERE steamid = :steamid ORDER BY `insert_time` DESC LIMIT 1");
        $query->execute(array(
            ':steamid' => $steamid, 
            ':id' => $id
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }

    public static function getAllRecords() {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM records");
        $query->execute();

        if ($query->rowCount() == 0) { return false; } 
        return $query->fetchAll();
    }

    public static function getInterviews($record) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM logs WHERE `action`='Interview' AND member = :steamid AND `timestamp` >= :record_date");
        $query->execute(array(":steamid" => $record->steamid, ":record_date" => date('Y-m-d H:i:s', strtotime($record->insert_time))));

        if ($query->rowCount() == 0) { return false; } 
        return $query->fetchAll();
    }
}
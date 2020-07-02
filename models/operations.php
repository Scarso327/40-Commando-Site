<?php

class Operations {

    public static function insertOperation ($uid, $steamid, $title, $log_id, $publish, $datetime) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare(
            "INSERT INTO operations (`uid`, steamid, title, log_id, published, scheduled_timestamp) VALUES (:uid, :steamid, :title, :log_id, :published, :datetime)"
        );
        
        $query->execute(array(
            ':uid' => $uid, 
            ':steamid' => $steamid,
            ':title' => $title,
            ':log_id' => $log_id,
            ':published' => $publish,
            ':datetime' => $datetime
        ));

        return ($query->rowCount() == 1);
    }

    public static function getOperation($uid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM operations WHERE `uid` = :uid OR title = :uid LIMIT 1");
        $query->execute(array(":uid" => $uid));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetch();
    }
}
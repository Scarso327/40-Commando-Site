<?php

class Award {
    
    public static function hasAward($steamid, $award) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM awards WHERE steamid = :steamid and award = :award AND active = 1 LIMIT 1");
        $query->execute(array(":steamid" => $steamid, ":award" => $award));

        return ($query->rowCount() == 1);
    }

    public static function giveAward($steamid, $award) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("INSERT INTO awards (steamid, award) VALUES (:steamid, :award)");
        $query->execute(array(":steamid" => $steamid, ":award" => $award));

        return ($query->rowCount() == 1);
    }

    public static function removeAward($steamid, $award) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE awards SET active = 0 WHERE steamid = :steamid AND award = :award AND active = 1 LIMIT 1");
        $query->execute(array(":steamid" => $steamid, ":award" => $award));

        return ($query->rowCount() == 1);
    }

    public static function getMembersAwards($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM awards WHERE steamid = :steamid AND active = 1");
        $query->execute(array(":steamid" => $steamid));

        if ($query->rowCount() < 1) { return false; }

        return $query->fetchAll();
    }

    public static function removeAllAwards($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE awards SET active = 0 WHERE steamid = :steamid AND active = 1 LIMIT 1");
        $query->execute(array(":steamid" => $steamid));

        return ($query->rowCount() > 0);
    }
}
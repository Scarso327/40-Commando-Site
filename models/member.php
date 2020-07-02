<?php

class Member {
    public static function getMember($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM accounts WHERE steamid = :steamid");
        $query->execute(array("steamid" => $steamid));

        if ($query->rowCount() == 0) { return false; } 

        $result = $query->fetch();

        $array = (explode(",", $result->sec_assignment));

        for ($i=0; $i < (count ($array)); $i++) {
            if ($array[$i] == "" || $array[$i] == null) {
                unset($array[$i]);
            }
        }

        $result->sec_assignment = $array;

        return $result;
    }

    public static function getCandidateID($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT candidate_id FROM accounts WHERE steamid = :steamid");
        $query->execute(array(":steamid" => $steamid));

        if ($query->rowCount() == 0) { return false; } 

        return $query->fetch()->candidate_id;
    }

    public static function setRecordID($steamid, $candidate_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE accounts SET candidate_id = :candidate_id WHERE steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':steamid' => $steamid, 
            ':candidate_id' => $candidate_id
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }

    public static function setMembership($steamid, $membership = 0) {
        $date_update = ($membership == 1)  ? "join_date" : "left_date";

        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE accounts SET `is_in_unit` = :membership, ".$date_update." = CURRENT_TIMESTAMP() WHERE steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':steamid' => $steamid, 
            ':membership' => $membership
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }

    public static function setName($steamid, $first_name, $last_name) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE accounts SET first_name = :first_name, last_name = :last_name WHERE steamid = :steamid LIMIT 1");
        $query->execute(array(
            ":steamid" => $steamid, 
            ":first_name" => $first_name,
            ":last_name" => $last_name
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }

    public static function isNameTaken($first_name, $last_name) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT steamName FROM accounts WHERE first_name = :first_name AND last_name = :last_name");
        $query->execute(array(":first_name" => $first_name, ":last_name" => $last_name));

        return ($query->rowCount() > 0);
    }

    public static function getFriendlyName($first_name, $last_name, $steam_name) {
        return (($first_name == "" || $last_name == "") ? $steam_name : $first_name." ".$last_name);
    }

    public static function setRank($steamid, $rank = -1, $acting = 0) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE accounts SET `rank` = :rank, is_acting = :is_acting, rank_change_date = CURRENT_TIMESTAMP() WHERE steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':steamid' => $steamid, 
            ':is_acting' => $acting,
            ':rank' => $rank
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }

    public static function setAssignment($steamid, $assignment = -1) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE accounts SET `assignment` = :assignment WHERE steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':steamid' => $steamid, 
            ':assignment' => $assignment
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }

    public static function setSecondaryAssignments($steamid, $assignments) {
        if ((count($assignments)) == 0) {
            $assignments = "";
        } else {
            $assignments = (implode(",", $assignments));
        }
        
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE accounts SET `sec_assignment` = :sec_assignment WHERE steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':steamid' => $steamid, 
            ':sec_assignment' => $assignments
        ));

        if($query->rowCount() > 0) { return true; }
        return false;
    }
}
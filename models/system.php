<?php

class System {
    public static function getRanks() {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM ranks ORDER BY `order`");
        $query->execute();

        $return = array();

        foreach ($query->fetchAll() as $rank) {
            $rank->info = self::getRankInfo($rank->name_key);
            if ($rank->info) {
                $return[$rank->id] = $rank;
            }
        }

        return $return;
    }

    private static function getRankInfo($key) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM rank_keys WHERE name_key = :name_key");
        $query->execute(array(":name_key" => $key));

        if ($query->rowCount() == 0) { return false; }

        $return = array();

        foreach ($query->fetchAll() as $key) {
            $return[$key->assignment_key] = $key;
        }

        return $return;
    }
}
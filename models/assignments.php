<?php

class Assignments {

    public static function getAssignment($id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM assignments WHERE id = :id AND active = 1");
        $query->execute(array(":id" => $id));

        if ($query->rowCount() == 0) { return false; } 

        return $query->fetch();
    }

    public static function getAssignmentMember($id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM accounts WHERE assignment = :id OR sec_assignment regexp :sec_id");
        $query->execute(array(":sec_id" => "[[:<:]]".$id."[[:>:]]", ":id" => $id));

        return $query->fetchAll();
    }

    public static function getCompanies() {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM assignments WHERE `type`='Company' AND active = 1");
        $query->execute();

        $return = array();

        foreach ($query->fetchAll() as $company) {
            $return[$company->id] = array(
                'id' => $company->id,
                'icon' => $company->icon,
                'name' => $company->name,
                'orbat_name' => $company->orbat_name,
                'sub_units' => self::getTroops($company->id)
            );
        }

        return $return;
    }

    private static function getTroops($company_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM assignments WHERE `type`='Troop' AND parent_id = :company AND active = 1");
        $query->execute(array(":company" => $company_id));
        
        $return = array();

        foreach ($query->fetchAll() as $troop) {
            $return[$troop->id] = array(
                'id' => $troop->id,
                'icon' => $troop->icon,
                'name' => $troop->name,
                'orbat_name' => $troop->orbat_name,
                'sub_units' => self::getSections($troop->id)
            );
        }

        $return["-1"] = array (
            "id" => -1,
            "icon" => "unassigned",
            'name' => "Unassigned",
            'orbat_name' => "Unassigned",
            'sub_units' => array()
        );

        return $return;
    }

    private static function getSections($troop_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM assignments WHERE `type`='Section' AND parent_id = :troop AND active = 1");
        $query->execute(array(":troop" => $troop_id));
        
        $return = array();

        foreach ($query->fetchAll() as $section) {
            $return[$section->id] = array(
                'id' => $section->id,
                'icon' => $section->icon,
                'name' => $section->name,
                'orbat_name' => $section->orbat_name,
                'sub_units' => self::getSquads($section->id)
            );
        }

        return $return;
    }

    private static function getSquads($section_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM assignments WHERE `type`='Squad' AND parent_id = :section AND active = 1");
        $query->execute(array(":section" => $section_id));
        
        $return = array();

        foreach ($query->fetchAll() as $squad) {
            $return[$squad->id] = array(
                'id' => $squad->id,
                'icon' => $squad->icon,
                'name' => $squad->name,
                'orbat_name' => $squad->orbat_name
            );
        }

        return $return;
    }
}